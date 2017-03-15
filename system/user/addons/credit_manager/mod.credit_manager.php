<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Credit_manager {

    public function buy_product()
    {
        //The user got here through this URL: ?ACT=30&entry_id={entry_id}
        $member_id = ee()->session->userdata('member_id');
        $entry_id = $_GET['entry_id'];

        // Check if the webinar has been bought yet
        ee()->db->where('member_id', $member_id);
        ee()->db->where('entry_id', $entry_id);
        $query = ee()->db->get('credit_manager');

        if($query->num_rows() != 0)
        {
            //Open the webinar in a new tab
            return FALSE;
        }



                //Get current member
                //Get current member credits
                //If current member credits is not 0
                    //Remove one member credit from the current member credits
                    //Open the webinar in a new tab
                    //Update the database; the column of this specific webinar should receive the value of '1', meaning that it has been bought
                //Else
                    //Show error about not having enough credits

    }

}