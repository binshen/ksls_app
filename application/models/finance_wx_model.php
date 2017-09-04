<?php
/**
 * Created by PhpStorm.
 * User: bin.shen
 * Date: 5/31/16
 * Time: 23:00
 */

class Finance_wx_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function __destruct()
    {
        parent::__destruct();
    }

    public function logout(){
        $this->db->where('id',$this->session->userdata('user_id'))->update('user',array('openid'=>''));
        $this->db->where('id',$this->session->userdata('finance_id'))->update('finance',array('borrower_openid'=>''));
        $this->session->unset_userdata('user_id');
        $this->session->unset_userdata('finance_id');
        $this->session->sess_destroy();
    }

    public function check_openid(){
        $openid = $this->session->userdata('openid');

        $this->db->select('a.id,a.rel_name,b.name,b.sum,c.name role_name')->from('user a');
        $this->db->join('company b','a.company_id = b.id','left');
        $this->db->join('role c','c.id = a.role_id','left');
        $this->db->where('a.openid',$openid);
        $row=$this->db->get()->row_array();
        if($row){
            $res = $this->set_session_wx($row['id']);
            if($res==1)
                return 1;
        }else{
            $finance_row = $this->db->select("id,finance_num")->from("finance")->where("borrower_openid",$openid)->get()->row_array();
            if($finance_row){
                $this->session->set_userdata('finance_id',$finance_row['id']);
                $this->session->set_userdata('finance_num',$finance_row['finance_num']);
                return 2;
            }
        }
        return -1;
    }

    public function set_session_wx($id){
        $this->db->from('user');
        $this->db->where('id', $id);
        $rs = $this->db->get();
        if ($rs->num_rows() > 0) {
            $res = $rs->row();
            if($res->flag==2){
                return 2;
            }
            $role_p = $this->db->select()->where('id',$res->role_id)->from('role')->get()->row();
            $company_flag = $this->db->where('id',$res->company_id)->from('company')->get()->row_array();
            if($role_p->permission_id !=1){
                if($company_flag){
                    if($company_flag['flag']==2 && $role_p->permission_id !=1){
                        return 3;
                    }
                }else{
                    return 3;
                }
            }
            $token = uniqid();
            //$this->db->where('id',$res->id)->update('user',array('token'=>$token));
            $pids = $this->db->select()->from('user_position')->where('user_id',$res->id)->get()->result_array();
            $ids = array();
            if($pids){
                foreach($pids as $id){
                    $ids[]=$id['pid'];
                }
            }

            $subids = $this->db->select()->from('user_subsidiary')->where('user_id',$res->id)->get()->result_array();
            $sids = array();
            if($subids){
                foreach($subids as $id){
                    $sids[]=$id['subsidiary_id'];
                }
            }

            $user_info['token'] = $token;
            $user_info['user_id'] = $res->id;
            $user_info['username'] = $res->username;
            $user_info['password'] = $res->password;
            $user_info['rel_name'] = $res->rel_name;
            $user_info['role_id'] = $res->role_id;
            $user_info['role_name'] = $role_p->name;
            $user_info['permission_id'] = $role_p->permission_id;
            $user_info['company_id'] = $res->company_id;
            //  $user_info['login_subsidiary_id'] = $res->subsidiary_id;
            $user_info['subsidiary_id_array'] = $sids;
            // $user_info['login_position_id'] = $res->position_id; 此栏位暂不使用
            $user_info['position_id_array'] = $ids;
            $user_info['user_pic'] = $res->pic;
            $this->session->set_userdata($user_info);
            return 1;
        }
        return 0;
    }

    public function user_login(){
        $openid = $this->session->userdata('openid');
        $username = $this->input->post('username');
        $password = $this->input->post('password');
        $this->db->from('user');
        $this->db->where('username', $username);
        $this->db->where('password', sha1($password));
        $rs = $this->db->get();
        if ($rs->num_rows() > 0) {
            $this->db->where('openid',$openid)->set('openid','')->update('user');
            $res = $rs->row();
            $res_ret = $this->set_session_wx($res->id);
            if($res_ret==1){
                $this->db->where('id',$res->id)->update('user',array('openid'=>$openid));
                return 1;
            }
        }
        return -1;
    }

    public function get_main_data(){
        $this->db->select('count(1) num')->from('finance a');
        $this->db->where('a.flag',1);
        $this->db->where("a.user_id",$this->session->userdata('user_id'));
        $data['my_fin_count']=$this->db->get()->row()->num;
        $sql_7 = "select count(a.id) num
from finance a
where a.flag = 1 and a.user_id = ".$this->session->userdata('user_id')."
 and a.tijiao_date >= date_add(NOW(), INTERVAL - 7 DAY) ";
        $query = $this->db->query($sql_7);
        $data['my_fin_count7'] =  $query->row()->num;

        $sql_30 = "select count(a.id) num
from finance a
where a.flag = 1 and a.user_id = ".$this->session->userdata('user_id')."
 and a.tijiao_date >= date_add(NOW(), INTERVAL - 30 DAY) ";
        $query = $this->db->query($sql_30);
        $data['my_fin_count30'] =  $query->row()->num;

        $position_id = $this->session->userdata('position_id_array');
        $permission_id = $this->session->userdata('permission_id');
        $company_id = NULL;
        $subsidiary_id = NULL;
        $user_id = NULL;
        if($permission_id == 1 || in_array(12,$position_id)){ // 如果是管理员,或者金融管理专员

        }elseif($permission_id <= 3){ //总经理 和 区域经理可以查看不同门店
            $company_id = $this->session->userdata('company_id');
            if($permission_id == 2) {

            } else if($permission_id < 5) {
                $subsidiary_id = $this->session->userdata('subsidiary_id_array');
            }
        }else{
            $company_id = $this->session->userdata('company_id');
            $subsidiary_id = $this->session->userdata('subsidiary_id_array');
            $user_id = $this->session->userdata('user_id');

        }
        $data['fin_count'] = $this->get_count_finance($user_id,$subsidiary_id,$company_id,null,null);
        $data['fin_count7'] = $this->get_count_finance($user_id,$subsidiary_id,$company_id,null,1);
        $data['fin_count30'] = $this->get_count_finance($user_id,$subsidiary_id,$company_id,null,2);
        $data['ins_1'] = $this->get_count_finance($user_id,$subsidiary_id,$company_id,1,null);
        $data['ins_2'] = $this->get_count_finance($user_id,$subsidiary_id,$company_id,2,null);
        $data['ins_3'] = $this->get_count_finance($user_id,$subsidiary_id,$company_id,3,null);
        $data['ins_4'] = $this->get_count_finance($user_id,$subsidiary_id,$company_id,4,null);
        $data['ins_5'] = $this->get_count_finance($user_id,$subsidiary_id,$company_id,5,null);
        $data['ins_6'] = $this->get_count_finance($user_id,$subsidiary_id,$company_id,-1,null);

        return $data;
    }

    public function get_count_finance($user_id = null,$subsidiary_id=null,$company_id=null,$status=null,$tj_flag=null){
        $this->db->select('count(distinct(a.id)) as num',false);
        $this->db->from('finance a');
        $this->db->join('user b','a.user_id = b.id','inner');
        $this->db->join('user c','a.create_user = c.id','inner');
        if($user_id){
            $this->db->where('a.user_id',$user_id);
        }
        if($status){
            $this->db->where('a.status',$status);
        }
        if($company_id) {
            $this->db->where('a.company_id', $company_id);
        }
        if(!empty($subsidiary_id)) {
            $this->db->where_in('a.subsidiary_id', $subsidiary_id);
        }
        if($status==1) {
            $this->db->where('date_format(a.tijiao_date, \'%Y-%m-%d\') >=', date("Y-m-d",strtotime("-7 day")));
        }
        if($status==2) {
            $this->db->where('date_format(a.tijiao_date, \'%Y-%m-%d\') >=', date("Y-m-d",strtotime("-30 day")));
        }
        $this->db->where('a.flag',1);

        $row = $this->db->get()->row_array();
        if($row)
            return $row['num'];
        return 0;
    }

    public function code_login($id){
        $row = $this->db->select('id,finance_num')->from("finance")->where("id",$id)->get()->row_array();
        if($row){
            $this->db->where('borrower_openid',$this->session->userdata('openid'))->update('finance',array('borrower_openid'=>''));
            $this->db->where('id',$id)->update('finance',array('borrower_openid'=>$this->session->userdata('openid')));
            $this->session->set_userdata('finance_id',$id);
            $this->session->set_userdata('finance_num',$row['finance_num']);
            return 1;
        }else{
            return -1;
        }

    }

    public function get_borrower_openid($id){
        $row=$this->db->select("borrower_openid")->from("finance")->where("id",$id)->get()->row_array();
        if($row)
            return $row['borrower_openid'];
        return -1;
    }
}