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
            return call_user_func_array(array($this, $method), $params);
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
            $data = $this->finance_model->get_detail($id);
            if($data)
                $this->assign('data', $data);
        }
        $this->display('finance.html');
    }

    //保存基本信息页面 第一页
    public function save_finance_1(){
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
        $res = $this->finance_model->save_finance_2();
        if($res > 0){
            redirect(site_url('/finance/edit_finance_3')."/".$res);
        }else{
            redirect(site_url('/finance/add_finance'));
        }
    }

    //打开基本信息页面 第二页
    public function edit_finance_2($id){
        if(!$id)
            redirect(site_url('/finance/add_finance'));
        $data = $this->finance_model->get_detail($id);
        $this->assign('data', $data);
        $this->display('finance - step2.html');
    }

    public function edit_finance_3($id){
        if(!$id)
            redirect(site_url('/finance/add_finance'));
        $data = $this->finance_model->get_detail($id);
        $this->assign('data', $data);
        $this->display('finance - step3.html');
    }

}