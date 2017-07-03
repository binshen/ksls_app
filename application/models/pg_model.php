<?php
/**
 * Created by PhpStorm.
 * User: bin.shen
 * Date: 5/31/16
 * Time: 23:00
 */

class Pg_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function __destruct()
    {
        parent::__destruct();
    }

    public function pg_list($page){
        // 每页显示的记录条数，默认20条
        $numPerPage = $this->input->post('numPerPage') ? $this->input->post('numPerPage') : 10;
        $pageNum = $this->input->post('pageNum') ? $this->input->post('pageNum') : $page;

        $this->db->select('count(1) as num');
        $this->db->from('hire');
        $this->db->where('user_id',$this->session->userdata('login_user_id'));
        if ($this->input->post('xiaoqu')){
            $this->db->like('xiaoqu',trim($this->input->post('xiaoqu')));
        }
        $row = $this->db->get()->row_array();
        //总记录数
        $data['countPage'] = $row['num'];
        $data['xiaoqu'] = $this->input->post('xiaoqu') ? trim($this->input->post('xiaoqu')) : "";
        //list
        $this->db->select('*');
        $this->db->from('hire');
        $this->db->where('user_id',$this->session->userdata('login_user_id'));
        if ($this->input->post('xiaoqu')){
            $this->db->like('xiaoqu',trim($this->input->post('xiaoqu')));
        }
        $this->db->limit($numPerPage, ($pageNum - 1) * $numPerPage );
        $this->db->order_by('create_time', 'desc');
        $data['res_list'] = $this->db->get()->result_array();
        $data['pageNum'] = $pageNum;
        $data['numPerPage'] = $numPerPage;
        return $data;
    }
}