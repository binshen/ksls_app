<?php
/**
 * Created by PhpStorm.
 * User: yangyang
 * Date: 16/5/20
 * Time: 上午9:02
 */
class Agenda_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function __destruct()
    {
        parent::__destruct();
    }

    function list_agenda($page,$user_id = null){
        // 每页显示的记录条数，默认20条
        $numPerPage = $this->input->post('numPerPage') ? $this->input->post('numPerPage') : 1;
        $pageNum = $this->input->post('pageNum') ? $this->input->post('pageNum') : $page;

        $this->db->select('count(1) as num');
        $this->db->from('agenda');
        if(!empty($user_id)){
            $this->db->where('user_id',$user_id);
        }
        if($this->input->post('status')){
            $this->db->where('status',$this->input->post('status'));
        }
        if($this->input->post('course')){
            $this->db->where('course',$this->input->post('course'));
        }
        $row = $this->db->get()->row_array();
        //总记录数
        $data['countPage'] = $row['num'];

        //list
        $this->db->select('a.*,b.rel_name');
        $this->db->from('agenda a');
        $this->db->join('user b','a.user_id = b.id','inner');
        if(!empty($user_id)){
            $this->db->where('a.user_id',$user_id);
        }
        if($this->input->post('status')){
            $this->db->where('a.status',$this->input->post('status'));
        }
        if($this->input->post('course')){
            $this->db->where('a.course',$this->input->post('course'));
        }
        $this->db->limit($numPerPage, ($pageNum - 1) * $numPerPage );
        $this->db->order_by('a.cdate', 'desc');
        $this->db->order_by('a.user_id', 'desc');
        $data['res_list'] = $this->db->get()->result();
        $data['pageNum'] = $pageNum;
        $data['numPerPage'] = $numPerPage;
        return $data;

    }
}