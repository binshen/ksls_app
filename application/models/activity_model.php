<?php
/**
 * Created by PhpStorm.
 * User: bin.shen
 * Date: 5/7/16
 * Time: 15:03
 */

class Activity_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function __destruct()
    {
        parent::__destruct();
    }

    public function get_company_list($id = NULL) {
        if(empty($id)) {
            return $this->db->get('company')->result_array();
        }
        return $this->db->get_where('company', array('id' => $id))->result_array();
    }

    public function get_subsidiary_list($company_id) {
        return $this->db->get_where('subsidiary', array('company_id' => $company_id))->result_array();
    }

    public function get_user_list($subsidiary_id) {
        return $this->db->get_where('user', array('subsidiary_id' => $subsidiary_id))->result_array();
    }

    public function list_activity() {
        
    }
}