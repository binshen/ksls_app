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
            if($this->input->post('status')==1){
                $this->db->where_in('a.status',array(1,3));
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
            if($this->input->post('status')==1){
                $this->db->where_in('a.status',array(1,3));
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
        //die(var_dump($this->db->last_query()));
        $this->db->limit($numPerPage, ($pageNum - 1) * $numPerPage );
        $this->db->order_by('a.cdate', 'desc');
        $this->db->order_by('a.user_id', 'desc');
        $data['res_list'] = $this->db->get()->result();
        $data['detail'] = 1;
        if(!$data['res_list']){
            foreach($data['res_list'] as $v){
                $ids[] = $v['id'];
            }
            if (isset($ids)){
                $this->db->select('b.a_id,b.created,c.name')->from('agenda a');
                $this->db->join('agenda_course b','a.id = b.a_id','left');
                $this->db->join('course c','c.id = b.c_id','left');
                $this->db->where_in('a.id',$ids);
                $agenda_detail = $this->db->get()->result_array();
                if ($agenda_detail){
                    $data['detail'] = $agenda_detail;
                }
            }
        }
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

    public function get_agenda($id) {
        return $this->db->get_where('agenda', array('id' => $id))->row();
    }

    public function save_agenda() {

        $now = date('Y-m-d H:i:s');

        $company_id = $this->session->userdata('login_company_id');

        $this->db->select_max('max_num');
        $result = $this->db->get_where('agenda', array('company_id' => $company_id))->row();
        $max_num = 1;
        if(!empty($max_num['max_num'])) {
            $max_num += $max_num['max_num'];
        }
        $num = 'D' . str_pad($company_id, 3, "0", STR_PAD_LEFT) . str_pad($max_num, 4, "0", STR_PAD_LEFT);

        $this->db->trans_start();//--------开始事务

        $agenda = array(
            'user_id' => $this->session->userdata('login_user_id'),
            'xq_name' => $this->input->post('xq_name'),
            'landlord_name' => $this->input->post('landlord_name'),
            'customer_name' => $this->input->post('customer_name'),
            'customer_income' => $this->input->post('customer_income'),
            'acreage' => $this->input->post('acreage'),
            'two_year_flag' => $this->input->post('two_year_flag'),
            'amount' => $this->input->post('amount'),
            'rest_load' => $this->input->post('rest_load'),
            'payment_method' => $this->input->post('payment_method'),
            'down_payment' => $this->input->post('down_payment'),
            'mortgage' => $this->input->post('mortgage'),
            'style' => $this->input->post('style'),
            'payment_node' => $this->input->post('payment_node'),
            'mark' => $this->input->post('mark'),
            'status' => 1,
            'course' => 1,
            'cdate' => $now,
            'num' => $num,
            'errtext' => '',
            'company_id' => $company_id,
            'max_num' => $max_num
        );
        $this->db->insert('agenda', $agenda);
        $a_id = $this->db->insert_id();

        for($i=1; $i<=6; $i++) {
            $pic_short = $this->input->post('pic_short_' . $i);
            foreach($pic_short as $idx => $pic) {
                $agenda_image = array(
                    'a_id' => $a_id,
                    'style' => $i,
                    'pic' => str_replace('_thumb', '', $pic),
                    'pic_short' => $pic
                );
                $this->db->insert('agenda_image', $agenda_image);
            }
        }

        $agenda_course = array(
            'a_id' => $a_id,
            'c_id' => 1,
            'created' => $now
        );
        $this->db->insert('agenda_course', $agenda_course);

        $this->db->trans_complete();//------结束事务
        if ($this->db->trans_status() === FALSE) {
            return -1;
        } else {
            return 1;
        }
    }


    ////////////////////////////////////////////////////////////////////////////////
    //ajax删除图片
    public function del_pic($folder,$style,$pic,$id){
        //echo $id;die;
        if($id){
            $this->db->where('pic_short',$pic);
            $this->db->delete('agenda_image');
        }
        @unlink('./././uploadfiles/agenda/'.$folder.'/'.$style.'/'.$pic);
        @unlink('./././uploadfiles/agenda/'.$folder.'/'.$style.'/'.str_replace('_thumb', '', $pic));
        $data = array(
            'flag'=>1,
            'pic'=>$pic
        );
        return $data;
    }
}