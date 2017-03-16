<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Credit Manager
 *
 * This ExpressionEngine 3 modules makes it possible for
 * members to buy webinars through credits
 *
 * @author Karen Bosch-Brakband
 *
 * Class Credit_manager
 */

class Credit_manager {

    /**
     * Gets the dynamic URL to the method that buys the webinar and opens it
     *
     * @return string
     */
    public function get_webinar_url()
    {
        return '/?ACT='.ee()->functions->fetch_action_id('Credit_manager', 'buy_webinar');
    }

    /**
     * Checks if the webinar has been bought yet by
     * looking at the custom table in the database
     *
     * PARAMETERS:
     * entry_id - helps retrieving the entry_id linked to a webinar
     *
     * @return string
     */
    public function check_if_webinar_is_already_bought()
    {
        // Get the parameter(s) that the user gave the method
        $entry_id = ee()->TMPL->fetch_param('entry_id');
        $current_member_id = ee()->session->userdata('member_id');

        //ToDo: Put this in a method
        ee()->db->where('member_id', $current_member_id)
                ->where('entry_id', $entry_id);

        $webinar = ee()->db->get('credit_manager');
        //END ToDo: Put this in a method

        if($webinar->num_rows() == 0)
        {
            // You can use this as a class for showing a modal pop-up for example
            return 'not-bought';
        }
    }

    /**
     * Method for buying a webinar with 1 credit when the member has
     * enough credits and when the webinar hasn't been bought yet
     *
     * @return bool
     */
    public function buy_webinar()
    {
        $current_member_id = ee()->session->userdata('member_id');
        $entry_id = $_GET['entry_id'];

        //ToDo: Put this in a method
        ee()->db->where('member_id', $current_member_id)
                ->where('entry_id', $entry_id);

        $webinar = ee()->db->get('credit_manager');
        //END ToDo: Put this in a method

        // Execute this when the webinar has already been bought
        if($webinar->num_rows() != 0)
        {
            $this->open_webinar($entry_id);

            return FALSE;
        }

        // Get the id of the credits field
        ee()->db->select('m_field_id')
                ->from('member_fields')
                ->where('m_field_name', 'credits');

        $id = ee()->db->get()->row('m_field_id');
        
        ee()->db->select('m_field_id_' . $id)
                ->where('member_id', $current_member_id);

        $current_member_credits = ee()->db->get('member_data');

        // Check if the member credits are equal to or below 0
        if ($current_member_credits->row('m_field_id_'.$id) <= 0)
        {
            // Show error about not having enough credits
            ee()->functions->redirect('/ingelogd/index/error');

            return FALSE;
        }

        $new_credit_amount =  $current_member_credits->row('m_field_id_'.$id) - 1;
        ee()->db->where('member_id', $current_member_id)
                ->update('member_data', array('m_field_id_' . $id => $new_credit_amount));

        // Add a column to the table credit_manager with the member id and entry_id so the module knows this specific webinar has been bought
        ee()->db->insert('credit_manager', array('member_id' => $current_member_id,
                                                 'entry_id' => $entry_id));

        $this->open_webinar($entry_id);
    }

    /**
     * Opens the webinar in a new tab
     *
     * @param $entry_id
     */
    private function open_webinar($entry_id)
    {
        ee()->db->select('field_id')
                ->from('channel_fields')
                ->where('field_name', 'webinar_gotomeeting_url');
        $webinar_url_id = ee()->db->get()->row('field_id');

        ee()->db->select('field_id_' . $webinar_url_id);
        ee()->db->where('entry_id', $entry_id);
        $webinar_url = ee()->db->get('channel_data');

        if($webinar_url->num_rows() != null)
        {
            // Open the webinar in a new tab (thus the JavaScript) and redirect back to the page we where we came from
            echo '<script type="text/javascript">            
                    window.open("' . $webinar_url->row('field_id_' . $webinar_url_id) . '");
                    window.location.href = "/ingelogd/index";
                 </script>';

        }
    }

}