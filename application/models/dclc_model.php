<?php
/**
 * Created by PhpStorm.
 * User: yangyang
 * Date: 16/6/3
 * Time: 下午3:22
 */
class Dclc_model extends MY_Model
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
     * 检查存储信息
     *
     * @return boolean
     */
    public function save_info(){
        if(!trim($this->input->post('username'))){
            return -1;
        }
        if(!trim($this->input->post('mobile'))){
            return -1;
        }
        if(!$this->isMobile(trim($this->input->post('mobile')))){
            return -1;
        }
        $data = array(
            'username'=>trim($this->input->post('username')),
            'mobile'=>trim($this->input->post('mobile')),
            'label'=>$this->input->post('label'),
            'demo'=>$this->input->post('demo'),
            'cdate'=>date('Y-m-d H:m:s')
        );

        //先检测是否保存过 相同电话号码

        $row = $this->db->from('dclc')->where('mobile',trim($this->input->post('mobile')))->get()->row();

        if($row){
            return -2;
        }

        //保存信息

        $res = $this->db->insert('dclc',$data);

        return 1;
    }

    public function isMobile($str){
        if(preg_match('/^[1]+[3,4,5,7,8]+\d{9}$/', $str))
            return true;
        return false;
    }

    public function get_result(){
        $res = array('status' => 1, 'result' => [], 'msg' => '');
        $keyword = trim($this->input->post('keyword'));
        $this->db->select()->from('exam_result');
        if($keyword){
            $this->db->where('ticket', $keyword);
            $this->db->or_where('code', $keyword);
        }else{
            $res['status'] = -1;
            $res['msg'] = '请输入信息再查询';
            return $res;
        }
        $data = $this->db->get()->row_array();
        //die(var_dump($this->db->last_query()));
        if(!$data){
            $res['status'] = -1;
            $res['msg'] = '未找到信息!';
            return $res;
        }
        $res['result'] = $data;
        return $res;
    }
}