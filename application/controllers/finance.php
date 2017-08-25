<?php
/**
 * Created by PhpStorm.
 * User: bin.shen
 * Date: 5/31/16
 * Time: 16:23
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Finance extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('finance_model');
    }

    function _remap($method,$params = array()) {
        if(!$this->session->userdata('login_user_id') || in_array(1,$this->session->userdata('login_position_id_array'))) {
            redirect(site_url('/'));
        } else {
            if($this->session->userdata('login_permission_id') < 3){
                if($method == 'add_finance'
                    || $method == 'save_finance_1'
                    || $method == 'save_finance_2'
                    || $method == 'edit_finance_2'
                    || $method == 'edit_finance_2'
                    || $method == 'save_finance_tj'
                    || $method == 'save_finance_3'
                    || $method == 'go_finance_1'){
                    redirect(site_url('/finance/finance_list'));
                    exit();
                }
            }
            return call_user_func_array(array($this, $method), $params);
        }
    }
    //上传图片
    public function upload_image($finance_num){
        if (is_readable('./././uploadfiles/finance') == false) {
            mkdir('./././uploadfiles/finance');
        }
        if (is_readable('./././uploadfiles/finance/'.$finance_num) == false) {
            mkdir('./././uploadfiles/finance/'.$finance_num);
        }
        $config['upload_path'] = './uploadfiles/finance/'.$finance_num;
        $config['allowed_types'] = 'gif|jpg|png|jpeg';
        $config['encrypt_name'] = true;
        $config['max_size'] = '1000';
        $this->load->library('upload', $config);

        if ( ! $this->upload->do_upload('userfile')){
            echo 1;
        }else{
            $pic_arr = $this->upload->data();
            echo $pic_arr['file_name'];
        }
    }
    //获取列表 等待开发
    public function finance_list($page=1){
        $data = $this->finance_model->finance_list($page);
        $this->assign('finance_list', $data);
        $pager = $this->pagination->getPageLink('/finance/finance_list', $data['countPage'], $data['numPerPage']);
        $this->assign('pager', $pager);
        $this->display('finance_list.html');
    }

    //获取详情
    public function finance_detail($id){
        $data = $this->finance_model->get_detail($id);
        $this->assign('data', $data);
        $this->display('tax_calculate.html');
    }

    //打开基本信息页面 第一页
    public function add_finance($id = ""){
        if($id != ""){
            $power_ = $this->finance_model->save_power($id);
            if($power_ == 1){
                $data = $this->finance_model->get_detail($id);
                if($data)
                    $this->assign('data', $data);
            }
        }
        $this->display('finance/finance.html');
    }

    public function go_finance_1(){
        $id = $this->input->post("id");
        if(!$id)
            redirect(site_url('/finance/add_finance'));
        $power_ = $this->finance_model->save_power($id);
        if($power_ != 1){
            redirect(site_url('/'));
        }
        $res = $this->finance_model->save_finance_2();
        if($res > 0){
            redirect(site_url('/finance/add_finance')."/".$id );
        }else{
            redirect(site_url('/finance/add_finance'));
        }
    }

    //保存基本信息页面 第一页
    public function save_finance_1(){
        $power_ = $this->finance_model->save_power($this->input->post('id'));
        if($power_ != 1){
            redirect(site_url('/'));
        }
        $res = $this->finance_model->save_finance_1();
        if($res > 0){
            redirect(site_url('/finance/edit_finance_2')."/".$res);
        }else{
            redirect(site_url('/finance/add_finance'));
        }
    }

    //保存基本信息页面 第二页
    public function save_finance_2(){
        $id = $this->input->post("id");
        if(!$id)
            redirect(site_url('/finance/add_finance'));
        $power_ = $this->finance_model->save_power($id);
        if($power_ != 1){
            redirect(site_url('/'));
        }
        $res = $this->finance_model->save_finance_2();
        if($res > 0){
            redirect(site_url('/finance/edit_finance_3')."/".$id );
        }else{
            redirect(site_url('/finance/add_finance'));
        }
    }

    //打开基本信息页面 第二页
    public function edit_finance_2($id){
        if(!$id)
            redirect(site_url('/finance/add_finance'));
        $power_ = $this->finance_model->save_power($id);
        if($power_ != 1){
            redirect(site_url('/'));
        }
        $data = $this->finance_model->get_detail($id);
        $this->assign('data', $data);
        $this->display('finance/finance - step2.html');
    }

    public function edit_finance_3($id){
        if(!$id)
            redirect(site_url('/finance/add_finance'));
        $power_ = $this->finance_model->save_power($id);
        if($power_ != 1){
            redirect(site_url('/'));
        }
        $data = $this->finance_model->get_detail($id);
        $this->assign('data', $data);
        $this->display('finance/finance - step3.html');
    }

    public function save_finance_tj(){
        $id = $this->input->post("id");
        if(!$id)
            redirect(site_url('/finance/add_finance'));
        $power_ = $this->finance_model->save_power($id);
        if($power_ != 1){
            redirect(site_url('/'));
        }
        $res = $this->finance_model->save_finance_3();
        if($res > 0){
            $tj = $this->finance_model->save_finance_tj();
            if($tj == 1){
                redirect(site_url('/finance/edit_finance_3')."/".$id );
            }else{
                redirect(site_url('/finance/add_finance'));
            }
        }else{
            redirect(site_url('/finance/add_finance'));
        }
    }

    public function save_finance_3(){
        $id = $this->input->post("id");
        if(!$id)
            redirect(site_url('/finance/add_finance'));
        $power_ = $this->finance_model->save_power($id);
        if($power_ != 1){
            redirect(site_url('/'));
        }
        $res = $this->finance_model->save_finance_3();
        if($res > 0){
            redirect(site_url('/finance/edit_finance_2')."/".$id );
        }else{
            redirect(site_url('/finance/add_finance'));
        }
    }

}