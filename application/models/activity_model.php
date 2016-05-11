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

    public function list_activity($page, $status, $user_id=NULL) {

        // 每页显示的记录条数，默认20条
        $numPerPage = $this->input->post('numPerPage') ? $this->input->post('numPerPage') : 5;
        $pageNum = $this->input->post('pageNum') ? $this->input->post('pageNum') : $page;

        //获得总记录数
        $this->db->select('count(1) as num');
        $this->db->from('activity a');
        $this->db->join('user b', 'a.user_id = b.id', 'inner');
        $this->db->where_in('a.status', $status);
        if($this->input->POST('company')) {
            $this->db->where('b.company_id', $this->input->POST('company'));
        }
        if($this->input->POST('subsidiary')) {
            $this->db->where('b.subsidiary_id', $this->input->POST('subsidiary'));
        }
        if($this->input->POST('user')) {
            $this->db->where('b.id', $this->input->POST('user'));
        }
        if($this->input->POST('start_date')) {
            $this->db->where('a.date >=', $this->input->POST('start_date'));
        }
        if($this->input->POST('end_date')) {
            $this->db->where('a.date <=', $this->input->POST('end_date'));
        }
        if(!empty($user_id)) {
            $this->db->where('a.user_id', $user_id);
        }

        $rs_total = $this->db->get()->row();
        //总记录数
        $data['countPage'] = $rs_total->num;

        //list
        $this->db->select('a.*, b.rel_name AS u_name');
        $this->db->select('t1.name AS t1n, t2.name AS t2n, t3.name AS t3n, t4.name AS t4n, t5.name AS t5n');
        $this->db->select('t1.unit AS t1u, t2.unit AS t2u, t3.unit AS t3u, t4.unit AS t4u, t5.unit AS t5u');
        $this->db->select('t1.icon AS t1c, t2.icon AS t2c, t3.icon AS t3c, t4.icon AS t4c, t5.icon AS t5c');
        $this->db->select('ROUND(a.a1s*a1n+a.a2s*a2n+a.a3s*a3n+a.a4s*a4n+a.a5s*a5n, 1) AS a1t', false);
        $this->db->select('ROUND(a.b1s*b1n+a.b2s*b2n+a.b3s*b3n+a.b4s*b4n+a.b5s*b5n, 1) AS b1t', false);
        $this->db->select('ROUND(a.c1s*c1n+a.c2s*c2n+a.c3s*c3n+a.c4s*c4n+a.c5s*c5n, 1) AS c1t', false);
        $this->db->from('activity a');
        $this->db->join('user b', 'a.user_id = b.id', 'inner');
        $this->db->join('activity_type t1', 'a.a1 = t1.id', 'left');
        $this->db->join('activity_type t2', 'a.a2 = t2.id', 'left');
        $this->db->join('activity_type t3', 'a.a3 = t3.id', 'left');
        $this->db->join('activity_type t4', 'a.a4 = t4.id', 'left');
        $this->db->join('activity_type t5', 'a.a5 = t5.id', 'left');
        $this->db->where_in('a.status', $status);
        if($this->input->POST('company')) {
            $this->db->where('b.company_id', $this->input->POST('company'));
        }
        if($this->input->POST('subsidiary')) {
            $this->db->where('b.subsidiary_id', $this->input->POST('subsidiary'));
        }
        if($this->input->POST('user')) {
            $this->db->where('b.id', $this->input->POST('user'));
        }
        if($this->input->POST('start_date')) {
            $this->db->where('a.date >=', $this->input->POST('start_date'));
        }
        if($this->input->POST('end_date')) {
            $this->db->where('a.date <=', $this->input->POST('end_date'));
        }
        if(!empty($user_id)) {
            $this->db->where('a.user_id', $user_id);
        }

        $this->db->limit($numPerPage, ($pageNum - 1) * $numPerPage );
        $this->db->order_by($this->input->post('orderField') ? $this->input->post('orderField') : 'a.id', $this->input->post('orderDirection') ? $this->input->post('orderDirection') : 'desc');
        $data['res_list'] = $this->db->get()->result();
        $data['pageNum'] = $pageNum;
        $data['numPerPage'] = $numPerPage;
        return $data;
    }

    public function add_activity() {
        $data = array(
            'user_id' => $this->session->userdata('login_user_id'),
            'date' => $this->input->post('date'),
            'status' => 1,
            'a1' => $this->input->post('a1'),
            'a1s' => $this->input->post('a1s'),
            'a1n' => $this->input->post('a1n'),
            'a1m' => $this->input->post('a1m'),
            'a2' => $this->input->post('a2'),
            'a2s' => $this->input->post('a2s'),
            'a2n' => $this->input->post('a2n'),
            'a2m' => $this->input->post('a2m'),
            'a3' => $this->input->post('a3'),
            'a3s' => $this->input->post('a3s'),
            'a3n' => $this->input->post('a3n'),
            'a3m' => $this->input->post('a3m'),
            'a4' => $this->input->post('a4'),
            'a4s' => $this->input->post('a4s'),
            'a4n' => $this->input->post('a4n'),
            'a4m' => $this->input->post('a4m'),
            'a5' => $this->input->post('a5'),
            'a5s' => $this->input->post('a5s'),
            'a5n' => $this->input->post('a5n'),
            'a5m' => $this->input->post('a5m')
        );
        $this->db->trans_start();//--------开始事务

        if($this->input->post('id')){//修改
            $this->db->where('id', $this->input->post('id'));
            $this->db->update('activity', $data);
        } else {
            $this->db->insert('activity', $data);
        }
        $this->db->trans_complete();//------结束事务
        if ($this->db->trans_status() === FALSE) {
            return -1;
        } else {
            return 1;
        }
    }

    public function assess_activity() {
        if(!$this->input->post('id')){
            return -1;
        }

        $data = array(
            'status' => 2,
            'b1' => $this->input->post('b1'),
            'b1s' => $this->input->post('b1s'),
            'b1n' => $this->input->post('b1n'),
            'b1m' => $this->input->post('b1m'),
            'b2' => $this->input->post('b2'),
            'b2s' => $this->input->post('b2s'),
            'b2n' => $this->input->post('b2n'),
            'b2m' => $this->input->post('b2m'),
            'b3' => $this->input->post('b3'),
            'b3s' => $this->input->post('b3s'),
            'b3n' => $this->input->post('b3n'),
            'b3m' => $this->input->post('b3m'),
            'b4' => $this->input->post('b4'),
            'b4s' => $this->input->post('b4s'),
            'b4n' => $this->input->post('b4n'),
            'b4m' => $this->input->post('b4m'),
            'b5' => $this->input->post('b5'),
            'b5s' => $this->input->post('b5s'),
            'b5n' => $this->input->post('b5n'),
            'b5m' => $this->input->post('b5m')
        );
        $this->db->trans_start();//--------开始事务

        $this->db->where('id', $this->input->post('id'));
        $this->db->update('activity', $data);
        $this->db->trans_complete();//------结束事务
        if ($this->db->trans_status() === FALSE) {
            return -1;
        } else {
            return 1;
        }
    }

    public function review_activity() {
        if(!$this->input->post('id')){
            return -1;
        }

        $data = array(
            'status' => 3,
            'c1' => $this->input->post('c1'),
            'c1s' => $this->input->post('c1s'),
            'c1n' => $this->input->post('c1n'),
            'c1m' => $this->input->post('c1m'),
            'c2' => $this->input->post('c2'),
            'c2s' => $this->input->post('c2s'),
            'c2n' => $this->input->post('c2n'),
            'c2m' => $this->input->post('c2m'),
            'c3' => $this->input->post('c3'),
            'c3s' => $this->input->post('c3s'),
            'c3n' => $this->input->post('c3n'),
            'c3m' => $this->input->post('c3m'),
            'c4' => $this->input->post('c4'),
            'c4s' => $this->input->post('c4s'),
            'c4n' => $this->input->post('c4n'),
            'c4m' => $this->input->post('c4m'),
            'c5' => $this->input->post('c5'),
            'c5s' => $this->input->post('c5s'),
            'c5n' => $this->input->post('c5n'),
            'c5m' => $this->input->post('c5m')
        );
        $this->db->trans_start();//--------开始事务

        $this->db->where('id', $this->input->post('id'));
        $this->db->update('activity', $data);
        $this->db->trans_complete();//------结束事务
        if ($this->db->trans_status() === FALSE) {
            return -1;
        } else {
            return 1;
        }
    }

    public function get_activity_type_list() {
        return $this->db->get('activity_type')->result_array();
    }

    public function get_activity_by_id($id) {

        $this->db->select('a.*');
        $this->db->select('t1.name AS t1n, t2.name AS t2n, t3.name AS t3n, t4.name AS t4n, t5.name AS t5n');
        $this->db->select('t1.unit AS t1u, t2.unit AS t2u, t3.unit AS t3u, t4.unit AS t4u, t5.unit AS t5u');
        $this->db->select('t1.icon AS t1c, t2.icon AS t2c, t3.icon AS t3c, t4.icon AS t4c, t5.icon AS t5c');
        $this->db->select('ROUND(a.a1s*a1n+a.a2s*a2n+a.a3s*a3n+a.a4s*a4n+a.a5s*a5n, 1) AS a1t', false);
        $this->db->select('ROUND(a.b1s*b1n+a.b2s*b2n+a.b3s*b3n+a.b4s*b4n+a.b5s*b5n, 1) AS b1t', false);
        $this->db->select('ROUND(a.c1s*c1n+a.c2s*c2n+a.c3s*c3n+a.c4s*c4n+a.c5s*c5n, 1) AS c1t', false);
        $this->db->from('activity a');
        $this->db->join('activity_type t1', 'a.a1 = t1.id', 'left');
        $this->db->join('activity_type t2', 'a.a2 = t2.id', 'left');
        $this->db->join('activity_type t3', 'a.a3 = t3.id', 'left');
        $this->db->join('activity_type t4', 'a.a4 = t4.id', 'left');
        $this->db->join('activity_type t5', 'a.a5 = t5.id', 'left');
        $this->db->where('a.id', $id);
        return $this->db->get()->row_array();
        //return $this->db->get_where('activity', array('id' => $id))->row_array();
    }
}