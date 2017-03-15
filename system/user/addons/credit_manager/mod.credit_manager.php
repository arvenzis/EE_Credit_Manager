<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Credit_manager
 */
class Credit_manager {

    //Create a function that creates the custom member field credits for you.
    //test1132

    /**
     * @return bool
     */
    public function buy_product()
    {
        // The user got here through this URL: ?ACT=30&entry_id={entry_id}

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
        }

        // Get current member credits where m_field_id_1 is the field that stores the credits
        ee()->db->select('m_field_id_1');
        ee()->db->where('member_id', $member_id);
        $query = ee()->db->get('member_data');

        // Check if the member credits are above 0
        if ($query->row()->m_field_id_1 <= 0)
        {
            // Show error about not having enough credits
            ee()->functions->redirect('/ingelogd/index/error');
        }

        // Remove one credit from the specific members' credits
        $new_credit_amount =  $query->row()->m_field_id_1 - 1;
        ee()->db->where('member_id', $member_id)->update('member_data', array('m_field_id_1' => $new_credit_amount));

        // Add a column to the table credit_manager with the member id and entry_id so the module knows this specific webinar has been bought
        ee()->db->insert('credit_manager', array('member_id' => $member_id, 'entry_id' => $entry_id));

        $this->open_webinar($entry_id);
    }

    /**
     * @param $entry_id
     */
    function open_webinar($entry_id){
        ee()->db->select('field_id_3');
        ee()->db->where('entry_id', $entry_id);
        $query = ee()->db->get('channel_data');

        if($query->num_rows() != null)
        {
            // Open the webinar in a new tab (thus the JavaScript) and redirect back to the page we where we came from
            echo '<script type="text/javascript">
                    window.open("'.$query->row()->field_id_3.'");
                    window.location.href = "/ingelogd/index";
                 </script>';

        }
    }

}