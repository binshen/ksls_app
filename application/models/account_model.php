<?php
/**
 * Created by PhpStorm.
 * User: bin.shen
 * Date: 5/31/16
 * Time: 23:00
 */

class Account_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function __destruct()
    {
        parent::__destruct();
    }

    public function recharge_list($page){
        // 每页显示的记录条数，默认20条
        $numPerPage = $this->input->post('numPerPage') ? $this->input->post('numPerPage') : 10;
        $pageNum = $this->input->post('pageNum') ? $this->input->post('pageNum') : $page;

        $this->db->select('count(1) as num');
        $this->db->from('company');
        $this->db->where('flag',1);
        if ($this->input->post('company')){
            $this->db->like('name',trim($this->input->post('company')));
        }
        $row = $this->db->get()->row_array();
        //总记录数
        $data['countPage'] = $row['num'];
        $data['company'] = $this->input->post('company') ? trim($this->input->post('company')) : "";
        //list
        $this->db->select('*');
        $this->db->from('company');
        $this->db->where('flag',1);
        if ($this->input->post('company')){
            $this->db->like('name',trim($this->input->post('company')));
        }
        $this->db->limit($numPerPage, ($pageNum - 1) * $numPerPage );
        $this->db->order_by('id', 'desc');
        $data['res_list'] = $this->db->get()->result_array();
        $data['pageNum'] = $pageNum;
        $data['numPerPage'] = $numPerPage;
        return $data;
    }

    public function company_account($page,$company_id){
        // 每页显示的记录条数，默认20条
        $numPerPage = $this->input->post('numPerPage') ? $this->input->post('numPerPage') : 10;
        $pageNum = $this->input->post('pageNum') ? $this->input->post('pageNum') : $page;

        $this->db->select('count(1) as num');
        $this->db->from('sum_log');
        $this->db->where('company_id',$company_id);
        $row = $this->db->get()->row_array();
        //总记录数
        $data['countPage'] = $row['num'];
        $data['company_id'] = $company_id;
        //list
        $this->db->select('*');
        $this->db->from('sum_log');
        $this->db->where('company_id',$company_id);
        $this->db->limit($numPerPage, ($pageNum - 1) * $numPerPage );
        $this->db->order_by('created', 'desc');
        $data['res_list'] = $this->db->get()->result_array();
        $data['company'] = $this->db->select()->from('company')->where('id',$company_id)->get()->row_array();
        $data['pageNum'] = $pageNum;
        $data['numPerPage'] = $numPerPage;
        return $data;
    }


}