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

    function list_agenda($page,$user_id = null,$subsidiary_id=null,$company_id=null){
        // 每页显示的记录条数，默认20条
        $numPerPage = $this->input->post('numPerPage') ? $this->input->post('numPerPage') : 1;
        $pageNum = $this->input->post('pageNum') ? $this->input->post('pageNum') : $page;

        $this->db->select('count(1) as num');
        $this->db->from('agenda a');
        $this->db->join('user b','a.user_id = b.id','inner');
        if(!empty($user_id)){
            $this->db->where('a.user_id',$user_id);
        }
        if($this->input->post('status')){
            if($this->input->post('status')==2){
                $this->db->where_in('a.status',array(2,3));
            }else{
                $this->db->where('a.status',$this->input->post('status'));
            }
        }
        if($this->input->post('course')){
            $this->db->where('a.course',$this->input->post('course'));
        }
        if($this->input->POST('company')) {
            $this->db->where('b.company_id', $this->input->POST('company'));
        }
        if($this->input->POST('subsidiary')) {
            $this->db->where('b.subsidiary_id', $this->input->POST('subsidiary'));
        }
        if($this->input->POST('user')) {
            $this->db->where('b.id', $this->input->POST('user'));
        }
        if(!empty($subsidiary_id)) {
            $this->db->where('b.subsidiary_id', $subsidiary_id);
        }
        if(!empty($company_id)) {
            $this->db->where('b.company_id', $company_id);
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
            if($this->input->post('status')==2){
                $this->db->where_in('a.status',array(2,3));
            }else{
                $this->db->where('a.status',$this->input->post('status'));
            }

        }
        if($this->input->post('course')){
            $this->db->where('a.course',$this->input->post('course'));
        }
        if($this->input->POST('company')) {
            $this->db->where('b.company_id', $this->input->POST('company'));
        }
        if($this->input->POST('subsidiary')) {
            $this->db->where('b.subsidiary_id', $this->input->POST('subsidiary'));
        }
        if($this->input->POST('user')) {
            $this->db->where('b.id', $this->input->POST('user'));
        }
        if(!empty($subsidiary_id)) {
            $this->db->where('b.subsidiary_id', $subsidiary_id);
        }
        if(!empty($company_id)) {
            $this->db->where('b.company_id', $company_id);
        }

        $this->db->limit($numPerPage, ($pageNum - 1) * $numPerPage );
        $this->db->order_by('a.cdate', 'desc');
        $this->db->order_by('a.user_id', 'desc');
        $data['res_list'] = $this->db->get()->result();
        $data['pageNum'] = $pageNum;
        $data['numPerPage'] = $numPerPage;
        return $data;

    }

    function get_course(){
        $res = $this->db->select()->from('course')->get()->result_array();
        if(!$res){
            return 1;
        }else{
            return $res;
        }
    }
}