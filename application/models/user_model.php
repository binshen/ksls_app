<?php
/**
 * Created by PhpStorm.
 * User: bin.shen
 * Date: 5/9/16
 * Time: 13:40
 */

class User_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function __destruct()
    {
        parent::__destruct();
    }

    /**
     * 用户登录检查
     *
     * @return boolean
     */
    public function check_login()
    {
        $username = $this->input->post('username');
        $password = $this->input->post('password');
        $this->db->from('user');
        $this->db->where('username', $username);
        $this->db->where('password', sha1($password));
        $rs = $this->db->get();
        if ($rs->num_rows() > 0) {
            $res = $rs->row();
            if($res->flag==2){
                return 2;
            }
            $token = uniqid();
            $this->db->where('id',$res->id)->update('user',array('token'=>$token));
            $user_info['login_token'] = $token;
            $user_info['login_user_id'] = $res->id;
            $user_info['login_username'] = $username;
            $user_info['login_password'] = $res->password;
            $user_info['login_rel_name'] = $res->rel_name;
            $user_info['login_role_id'] = $res->role_id;
            $user_info['login_company_id'] = $res->company_id;
            $user_info['login_subsidiary_id'] = $res->subsidiary_id;
            $user_info['login_position_id'] = $res->position_id;
            $user_info['login_user_pic'] = $res->pic;
            $this->session->set_userdata($user_info);
            return 1;
        }
        return 0;
    }

    public function update_password()
    {
        $user_id = $this->session->userdata('login_user_id');
        $rs= $this->db->where('id', $user_id)->update('user', array('password'=>sha1($this->input->post('password'))));
        if ($rs) {
            return 1;
        } else {
            return $rs;
        }
    }

    public function update_tmp_pic($file_pic)
    {
        $user_id = $this->session->userdata('login_user_id');
        $rs= $this->db->where('id', $user_id)->update('user', array('tmp_pic'=>$file_pic));
        if ($rs) {
            return 1;
        } else {
            return $rs;
        }
    }

    public function update_user()
    {
        $rel_name = $this->input->post('rel_name');
        $user_info['login_rel_name'] = $this->input->post('rel_name');

        $user_id = $this->session->userdata('login_user_id');
        $user = $this->db->get_where('user', array('id' => $user_id))->row_array();
        if(!empty($user) && !empty($user['tmp_pic'])) {
            $rs= $this->db->where('id', $user_id)->update('user', array(
                'rel_name'=>$rel_name,
                'pic'=>$user['tmp_pic'],
                'tmp_pic'=>NULL
            ));
            $user_info['login_user_pic'] = $user['tmp_pic'];
        } else {
            $rs= $this->db->where('id', $user_id)->update('user', array('rel_name'=>$rel_name));
        }
        if ($rs) {
            $this->session->set_userdata($user_info);
            return 1;
        } else {
            return $rs;
        }
    }
}