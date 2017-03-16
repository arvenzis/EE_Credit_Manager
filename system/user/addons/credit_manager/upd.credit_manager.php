<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Credit_manager_upd {

    /**
     * @var string
     */
    var $version = '1.0';

    /**
     * Credit_manager_upd constructor.
     */
    function __construct()
    {
        // Make a local reference to the ExpressionEngine super object
        $this->EE = get_instance();

        ee()->load->dbforge();
    }


    /**
     * @return bool
     */
    function install()
    {
        $data = array(
            'module_name' 	 => 'Credit_manager',
            'module_version' => $this->version,
            'has_cp_backend' => 'n',
            'has_publish_fields' => 'n'
        );

        $this->EE->db->insert('modules', $data);

        $data = array(
            'class'     => 'Credit_manager' ,
            'method'    => 'buy_product'
        );

        ee()->db->insert('actions', $data);

        //Create a table to save the entry id of the webinar and the member id of users who bought the webinar
        $this->create_table_credit_manager();

        //Create a function that creates the custom member field credits
        $this->create_custom_member_field();

        return TRUE;
    }

    /**
     * @param string $current
     * @return bool
     */
    function update($current = '')
    {
        return TRUE;
    }

    /**
     * @return bool
     */
    function uninstall()
    {
        $this->EE->db->where('module_name', 'Credit_manager');
        $this->EE->db->delete('modules');

        ee()->db->where('class', 'Credit_manager');
        ee()->db->delete('actions');

        ee()->dbforge->drop_table('credit_manager');

        // Drop all information that is related to the 'Credits' field
        ee()->db->where('m_field_name', 'credits');
        $m_field_id = ee()->db->get('member_fields')->row()->m_field_id;

        ee()->dbforge->drop_column('member_data', 'm_field_id_' . $m_field_id);
        ee()->dbforge->drop_column('member_data', 'm_field_ft_' . $m_field_id);

        ee()->db->where('m_field_name', 'credits');
        ee()->db->delete('member_fields');

        return TRUE;
    }

    /**
     *  Creates the table 'credit_manager'
     */
    private function create_table_credit_manager()
    {
        if(!ee()->db->table_exists('credit_manager'))
        {
            $fields = array(
                'id'            => array('type' => 'int', 'constraint' => '10', 'unsigned' => TRUE, 'null' => FALSE, 'auto_increment' => TRUE),
                'entry_id'      => array('type' => 'int'),
                'member_id'     => array('type' => 'int')
            );

            ee()->dbforge->add_field($fields);
            ee()->dbforge->add_key('id', TRUE);
            ee()->dbforge->create_table('credit_manager');
        }
    }

    private function create_custom_member_field()
    {
        // This variable is to make the columns dynamic
        //$uniqueFieldId = count( ee()->db->get('member_data')->row() ) + 1;

        //ToDo: Find a way to calculate the columns, not the rows.
        //echo count( ee()->db->get('member_data')->row() );


        // Add new columns in the member_data table
        ee()->dbforge->add_column('member_data', array('m_field_id_999' => array('type' => 'text')));
        ee()->dbforge->add_column('member_data', array('m_field_ft_999' => array('type' => 'tinytext')));


        // Add the actual credits field
        ee()->db->insert('member_fields', array('m_field_id'    => '999',
                                                'm_field_name'  => 'credits',
                                                'm_field_label' => 'Credits',
                                                'm_field_type'  => 'text'
                                                ));
    }
}