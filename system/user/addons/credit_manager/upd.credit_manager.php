<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Credit_manager_upd {

    var $version = '1.0';

    function __construct(){
        // Make a local reference to the ExpressionEngine super object
        $this->EE = get_instance();
    }


    function install(){
        $data = array(
            'module_name' 	 => 'Credit_manager',
            'module_version' => $this->version,
            'has_cp_backend' => 'n',
            'has_publish_fields' => 'n'
        );

        $this->EE->db->insert('modules', $data);

        $data = array(
            'class'     => 'Credit_manager' ,
            'method'    => 'test'
        );

        ee()->db->insert('actions', $data);

        return TRUE;
    }

    function update($current = '')
    {

    }

    function uninstall(){
        $this->EE->db->where('module_name', 'Credit_manager');
        $this->EE->db->delete('modules');

        ee()->db->where('class', 'Credit_manager');
        ee()->db->delete('actions');

        return TRUE;
    }
}