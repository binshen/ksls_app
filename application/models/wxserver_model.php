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

    public function fasongxc(){
        $this->db->select('a.id,a.openid,a.rel_name,d.date');
        $this->db->select('t1.name AS t1n, t2.name AS t2n, t3.name AS t3n, t4.name AS t4n, t5.name AS t5n');
        $this->db->select('t1.unit AS t1u, t2.unit AS t2u, t3.unit AS t3u, t4.unit AS t4u, t5.unit AS t5u');
        $this->db->select('t1.icon AS t1c, t2.icon AS t2c, t3.icon AS t3c, t4.icon AS t4c, t5.icon AS t5c');
        $this->db->select('b.name');
        $this->db->from('user a');
        $this->db->join('company b','a.company_id = b.id','left');
        $this->db->join('activity d','a.id = d.user_id','inner');
        $this->db->join('activity_type t1', 'd.a1 = t1.id', 'left');
        $this->db->join('activity_type t2', 'd.a2 = t2.id', 'left');
        $this->db->join('activity_type t3', 'd.a3 = t3.id', 'left');
        $this->db->join('activity_type t4', 'd.a4 = t4.id', 'left');
        $this->db->join('activity_type t5', 'd.a5 = t5.id', 'left');
        $this->db->where(array(
            'a.flag'=>1,
            'b.flag'=>1,
            'a.openid <>'=>'',
            'd.date'=>date('Y-m-d')
        ));
        $this->db->where('a.openid is not null');
        $res = $this->db->get()->result_array();
        foreach($res as $item){
            if(date('H') < 9){
                $keyword2 = $item['t1n'];
                $keyword3 = '9:00-10:30';
            }elseif(date('H') < 11){
                $keyword2 = $item['t2n'];
                $keyword3 = '10:30-13:00';
            }elseif(date('H') < 13){
                $keyword2 = $item['t3n'];
                $keyword3 = '13:00-15:00';
            }elseif(date('H') < 15){
                $keyword2 = $item['t4n'];
                $keyword3 = '15:00-16:30';
            }else{
                $keyword2 = $item['t5n'];
                $keyword3 = '16:30-18:00';
            }

            $data = array(
                'first' => array(
                    'value' => $item['rel_name'].' 工作辛苦了,房猫服务中心提醒您,下一阶段行程安排',
                    'color' => '#FF0000'
                ),
                'keyword1' => array(
                    'value' => $item['date'],
                    'color' => '#FF0000'
                ),
                'keyword2' => array(
                    'value' => $keyword2,
                    'color' => '#FF0000'
                ),
                'keyword3' => array(
                    'value' => $keyword3,
                    'color' => '#FF0000'
                ),
                'remark' => array(
                    'value' => '祝您工作顺利！',
                    'color' => '#FF0000'
                )
            );
            var_dump($data);
           // $this->wxpost($this->config->item('WX_XC'),$data,$item['id']);
        }

      /*  var_dump($this->db->last_query());
        die(var_dump($res));*/


    }
}