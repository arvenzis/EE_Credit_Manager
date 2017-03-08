<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Credit_manager_upd {

    var $version = '0.1';

    public $EE;

    function __construct(){
        // Make a local reference to the ExpressionEngine super object
        $this->EE = get_instance();
    }


    function install(){
        $data = array(
            'module_name' 	 => 'Credit Manager',
            'module_version' => $this->version,
            'has_cp_backend' => 'y',
            'has_publish_fields' => 'n'
        );

        $this->EE->db->insert('modules', $data);

        return TRUE;
    }
}