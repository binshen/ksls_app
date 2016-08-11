<?php
/**
 * Created by PhpStorm.
 * User: bin.shen
 * Date: 5/31/16
 * Time: 23:00
 */

class Wxserver_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function __destruct()
    {
        parent::__destruct();
    }

    public function check_openid(){
        $openid = $this->session->userdata('openid');

        $this->db->select('a.rel_name,b.name,b.sum,c.name role_name')->from('user a');
        $this->db->join('company b','a.company_id = b.id','left');
        $this->db->join('role c','c.id = a.role_id','left');
        $this->db->where('a.openid',$openid);
        $row=$this->db->get()->row_array();
        return $row;
    }

    public function save_openid(){
        $openid = $this->session->userdata('openid');
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
            $insert = $this->db->where('id',$res->id)->update('user',array('openid'=>$openid));
            if($insert){
                return 1;
            }else{
                return 4;
            }
        }
        return -1;
    }

    public function save_order(){
        $row = $this->db->select()->from('user')->where('openid',$this->session->userdata('openid'))->get()->row_array();
        if(!$row){
            return -1;
        }
        $data = array(
            'company_id' => $row['company_id'],
            'qty' => $this->input->post('qty'),
            'style' => 1,
            'demo' => '微信充值',
            'user_id' => $row['id'],
            't_id' => -1,
            't_name'=>null,
            'flag'=>2,
            'created' => date("Y-m-d H:i:s")
        );
       $res = $this->db->insert('sum_log',$data);
        if($res){
            return $this->db->insert_id();
        }else{
            return -1;
        }
    }
}