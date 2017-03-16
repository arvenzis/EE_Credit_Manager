<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Credit_manager
 */
class Credit_manager {

    /**
     * @return string
     */
    public function get_product_url()
    {
        return '/?ACT='.ee()->functions->fetch_action_id('Credit_manager', 'buy_product');
    }

    public function check_if_product_is_already_bought()
    {
        // Get the parameter(s) that the user gave the method
        $entry_id = ee()->TMPL->fetch_param('entry_id');

        // Get the member id of the logged in user and the entry id of the webinar the user clicked
        $member_id = ee()->session->userdata('member_id');

        // Check if the webinar has been bought yet
        ee()->db->where('member_id', $member_id);
        ee()->db->where('entry_id', $entry_id);
        $query = ee()->db->get('credit_manager');

        if($query->num_rows() == 0)
        {
            // You can use this as a class for showing a modal pop-up for example
            return 'not-bought';
        }
    }

    /**
     * @return bool
     */
    public function buy_product()
    {
        // Get the member id of the logged in user and the entry id of the webinar the user clicked
        $member_id = ee()->session->userdata('member_id');
        $entry_id = $_GET['entry_id'];

        // Check if the webinar has been bought yet
        ee()->db->where('member_id', $member_id);
        ee()->db->where('entry_id', $entry_id);
        $query = ee()->db->get('credit_manager');

        if($query->num_rows() != 0)
        {
            $this->open_webinar($entry_id);
            return FALSE;
        }

        // Get the id of the credits field
        ee()->db->select('m_field_id');
        ee()->db->from('member_fields');
        ee()->db->where('m_field_name', 'credits');
        $id = ee()->db->get()->row('m_field_id');

        // Get current member credits where m_field_id_ is the field that stores the credits
        ee()->db->select('m_field_id_' . $id);
        ee()->db->where('member_id', $member_id);
        $query = ee()->db->get('member_data');

        // Check if the member credits are above 0
        if ($query->row('m_field_id_'.$id) <= 0)
        {
            // Show error about not having enough credits
            ee()->functions->redirect('/ingelogd/index/error');
            return FALSE;
        }

        // Remove one credit from the specific members' credits
        $new_credit_amount =  $query->row('m_field_id_'.$id) - 1;

        ee()->db->where('member_id', $member_id)->update('member_data', array('m_field_id_' . $id => $new_credit_amount));

        // Add a column to the table credit_manager with the member id and entry_id so the module knows this specific webinar has been bought
        ee()->db->insert('credit_manager', array('member_id' => $member_id, 'entry_id' => $entry_id));

        $this->open_webinar($entry_id);
    }

    /**
     * @param $entry_id
     */
    private function open_webinar($entry_id)
    {
        // Get the id of the webinar url field
        ee()->db->select('field_id');
        ee()->db->from('channel_fields');
        ee()->db->where('field_name', 'webinar_url');
        $id = ee()->db->get()->row('field_id');

        // Get the actual url to the webinar
        ee()->db->select('field_id_' . $id);
        ee()->db->where('entry_id', $entry_id);
        $query = ee()->db->get('channel_data');

        if($query->num_rows() != null)
        {
            // Open the webinar in a new tab (thus the JavaScript) and redirect back to the page we where we came from
            echo '<script type="text/javascript">
                    window.open("' . $query->row('field_id_'.$id) . '");
                    window.location.href = "/ingelogd/index";
                 </script>';

        }
    }

}