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

    public function get_subsidiary_list($company_id, $subsidiary_id=NULL) {
        if(empty($subsidiary_id)) {
            return $this->db->get_where('subsidiary', array('company_id' => $company_id))->result_array();
        } else {
            return $this->db->get_where('subsidiary', array('id' => $subsidiary_id))->result_array();
        }
    }

    public function get_subsidiary_user_list($subsidiary_id) {
        return $this->db->get_where('user', array('subsidiary_id' => $subsidiary_id))->result_array();
    }

    public function list_activity() {

        // 每页显示的记录条数，默认20条
        $numPerPage = $this->input->post('numPerPage') ? $this->input->post('numPerPage') : 20;
        $pageNum = $this->input->post('pageNum') ? $this->input->post('pageNum') : 1;

        //获得总记录数
        $this->db->select('count(1) as num');
        $this->db->from('activity');
        //$this->db->where('id', $this->session->userdata('subsidiary_id'));

        $rs_total = $this->db->get()->row();
        //总记录数
        $data['countPage'] = $rs_total->num;
        $data['company_id'] = null;

        //list
        $this->db->select('a.*, b.rel_name AS u_name, t1.name AS t1n, t2.name AS t2n, t3.name AS t3n, t4.name AS t4n, t5.name AS t5n');
        $this->db->select('ROUND(a.a1s*a1n+a.a2s*a2n+a.a3s*a3n+a.a4s*a4n+a.a5s*a5n, 1) AS a1t', false);
        $this->db->select('ROUND(a.a1s*b1n+a.a2s*b2n+a.a3s*b3n+a.a4s*b4n+a.a5s*b5n, 1) AS b1t', false);
        $this->db->select('ROUND(a.a1s*c1n+a.a2s*c2n+a.a3s*c3n+a.a4s*c4n+a.a5s*c5n, 1) AS c1t', false);
        $this->db->from('activity a');
        $this->db->join('user b', 'a.user_id = b.id', 'inner');
        $this->db->join('activity_type t1', 'a.a1 = t1.id', 'left');
        $this->db->join('activity_type t2', 'a.a2 = t2.id', 'left');
        $this->db->join('activity_type t3', 'a.a3 = t3.id', 'left');
        $this->db->join('activity_type t4', 'a.a4 = t4.id', 'left');
        $this->db->join('activity_type t5', 'a.a5 = t5.id', 'left');

        $this->db->limit($numPerPage, ($pageNum - 1) * $numPerPage );
        $this->db->order_by($this->input->post('orderField') ? $this->input->post('orderField') : 'a.id', $this->input->post('orderDirection') ? $this->input->post('orderDirection') : 'desc');
        $data['res_list'] = $this->db->get()->result();
        $data['pageNum'] = $pageNum;
        $data['numPerPage'] = $numPerPage;
        return $data;
    }
}