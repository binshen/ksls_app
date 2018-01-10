<?php
/**
 * Created by PhpStorm.
 * User: yangyang
 * Date: 16/5/20
 * Time: 上午9:02
 */
class Agenda_wx_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function __destruct()
    {
        parent::__destruct();
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
            $this->db->where('agenda_openid',$openid)->set('openid','')->update('user');
            $res = $rs->row();
            $res_ret = $this->set_session_wx($res->id);
            if($res_ret==1){
                $this->db->where('id',$res->id)->update('user',array('agenda_openid'=>$openid));
                return 1;
            }
        }
        return -1;
    }

    public function check_openid(){
        $openid = $this->session->userdata('agewx_openid');

        $this->db->select('a.id,a.rel_name,b.name,b.sum,c.name role_name')->from('user a');
        $this->db->join('company b','a.company_id = b.id','left');
        $this->db->join('role c','c.id = a.role_id','left');
        $this->db->where('a.agenda_openid',$openid);
        $row=$this->db->get()->row_array();
        if($row){
            $res = $this->set_session_wx($row['id']);
            if($res==1)
                return 1;
        }else{
           //预留保存 申请人的openid绑定,和金融服务一样
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

            $user_info['agewx_token'] = $token;
            $user_info['agewx_user_id'] = $res->id;
            $user_info['agewx_username'] = $res->username;
            $user_info['agewx_password'] = $res->password;
            $user_info['agewx_rel_name'] = $res->rel_name;
            $user_info['agewx_role_id'] = $res->role_id;
            $user_info['agewx_role_name'] = $role_p->name;
            $user_info['agewx_permission_id'] = $role_p->permission_id;
            $user_info['agewx_company_id'] = $res->company_id;
            //  $user_info['login_subsidiary_id'] = $res->subsidiary_id;
            $user_info['agewx_subsidiary_id_array'] = $sids;
            // $user_info['login_position_id'] = $res->position_id; 此栏位暂不使用
            $user_info['agewx_position_id_array'] = $ids;
            $user_info['agewx_user_pic'] = $res->pic;
            $this->session->set_userdata($user_info);
            return 1;
        }
        return 0;
    }
}