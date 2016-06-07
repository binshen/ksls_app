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
            $pids = $this->db->select()->from('user_position')->where('user_id',$res->id)->get()->result_array();
            $ids = array();
            if($pids){
                foreach($pids as $id){
                    $ids[]=$id['pid'];
                }
            }
            $user_info['login_token'] = $token;
            $user_info['login_user_id'] = $res->id;
            $user_info['login_username'] = $username;
            $user_info['login_password'] = $res->password;
            $user_info['login_rel_name'] = $res->rel_name;
            $user_info['login_role_id'] = $res->role_id;
            $user_info['login_company_id'] = $res->company_id;
            $user_info['login_subsidiary_id'] = $res->subsidiary_id;
           // $user_info['login_position_id'] = $res->position_id; 此栏位暂不使用
            $user_info['login_position_id_array'] = $ids;
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

    public function get_icons($user_id=NULL) {
        $this->db->select('a.id, a.name, a.icon AS img, a.action AS url');
        $this->db->from('icon a');
        if(!empty($user_id)) {
            $this->db->select('b.user_id');
            $this->db->join('icon_config b', "a.id = b.icon_id AND b.user_id = {$user_id}", 'left');
            $this->db->order_by('b.user_id', "DESC");
        } else {
            $this->db->join('icon_config b', "a.id = b.icon_id", 'left');
        }
        $this->db->order_by('b.id', "ASC");
        $this->db->distinct();
        return $this->db->get()->result_array();
    }

    public function get_icon_count($user_id=NULL) {
        if(!empty($user_id)) {
            return $this->db->select('count(1) AS num')->where('user_id', $user_id)->get('icon_config')->row()->num;
        } else {
            return $this->db->select('count(1) AS num')->get('icon')->row()->num;
        }
    }

    public function reset_icon_config($user_id) {
        if(empty($user_id)) return;

        $this->db->trans_start();//--------开始事务

        $this->db->where('user_id', $user_id);
        $this->db->delete('icon_config');
        
        $icon_ids = $this->input->post("icon_id");
        foreach ($icon_ids as $icon_id) {
            $icon_config_data = array(
                'user_id' => $user_id,
                'icon_id' => $icon_id
            );
            $this->db->insert('icon_config', $icon_config_data);
        }

        $this->db->trans_complete();//------结束事务
        if ($this->db->trans_status() === FALSE) {
            return -1;
        } else {
            return 1;
        }
    }
}