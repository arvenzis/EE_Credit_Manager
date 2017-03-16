<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Credit_manager_upd
 */
class Credit_manager_upd {

    /**
     * @var string
     */
    var $version = '1.0.1';

    /**
     * Credit_manager_upd constructor.
     */
    function __construct()
    {
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

        ee()->db->insert('modules', $data);

        $data = array(
            'class'     => 'Credit_manager' ,
            'method'    => 'buy_webinar'
        );

        ee()->db->insert('actions', $data);

        $this->create_table_credit_manager();
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
        ee()->db->where('module_name', 'Credit_manager')
                ->delete('modules');

        ee()->db->where('class', 'Credit_manager')
                ->delete('actions');

        ee()->dbforge->drop_table('credit_manager');

        ee()->db->where('m_field_name', 'credits');
        $credit_field_id = ee()->db->get('member_fields')->row()->m_field_id;

        ee()->dbforge->drop_column('member_data', 'm_field_id_' . $credit_field_id);
        ee()->dbforge->drop_column('member_data', 'm_field_ft_' . $credit_field_id);

        ee()->db->where('m_field_name', 'credits')
                ->delete('member_fields');

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

    /**
     *  Creates the custom member field 'Credits'
     */
    private function create_custom_member_field()
    {
        // Add the actual credits field
        $data = array(
            'm_field_name'  => 'credits',
            'm_field_label' => 'Credits',
            'm_field_type'  => 'text'
        );
        ee()->db->insert('member_fields', $data);

        // Get ID of column to be set
        ee()->db->select('m_field_id')
                ->from('member_fields')
                ->where('m_field_name', 'credits');
        $id = ee()->db->get()->row('m_field_id');

        // Add new columns in the member_data table
        ee()->dbforge->add_column('member_data', array('m_field_id_' . $id => array('type' => 'text')));
        ee()->dbforge->add_column('member_data', array('m_field_ft_' . $id => array('type' => 'tinytext')));
    }
}