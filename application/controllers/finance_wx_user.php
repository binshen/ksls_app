<?php
/**
 * Created by PhpStorm.
 * User: bin.shen
 * Date: 5/31/16
 * Time: 16:23
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once "finwx_Controller.php";
class Finance_wx_user extends Finwx_Controller
{

    public function __construct()
    {
        parent::__construct();
        if(!$this->session->userdata('wx_user_id')){
            redirect('finance_wx/login');
        }
        $this->assign('rel_name',$this->session->userdata('wx_rel_name'));
        $this->assign('role_name',$this->session->userdata('wx_role_name'));
        $permission_id = $this->session->userdata('wx_permission_id');
        $this->assign('permission_id', $permission_id);
        $position_id = $this->session->userdata('wx_position_id_array');
        $this->assign('position_id', $position_id);
    }

    public function logout(){
        $this->finance_wx_model->logout();
        redirect('finance_wx/login');
    }

    public function index(){
        $main_data = $this->finance_wx_model->get_main_data();
        $this->assign('search_info_hidden',$this->input->post('search_info')?$this->input->post('search_info'):'');
        $this->assign('main_data',$main_data);
        $this->display('finance/weixin/index.html');
    }

    public function index_status($status=null){
        if(!$status)
            $status = $this->input->post('status');
        $main_data = $this->finance_wx_model->get_main_data();
        $this->assign('search_info_hidden',$this->input->post('search_info')?$this->input->post('search_info'):'');
        $this->assign('main_data',$main_data);
        $this->assign('status',$status);
        $this->display('finance/weixin/index-classify.html');
    }

    /*public function list_finance($page=1){
        $main_data = $this->finance_wx_model->get_main_data();
        $this->cismarty->assign('main_data',$main_data);
        // $this->cismarty->assign('jindu_type',$jindu_type);
        $data = $this->finance_model->finance_list($page,$this->session->userdata('wx_user_id'));
        $base_url = "/finance_wx_user/list_finance/";
        $pager = $this->pagination->getPageLink($base_url, $data['countPage'], $data['numPerPage']);
        $this->cismarty->assign('pager',$pager);
        $this->cismarty->assign('data',$data);
        $this->cismarty->display('finance/user_finance_list.html');
    }*/

    public function list_finance_loaddata($page=1){
        $data = $this->finance_model->finance_list($page,$this->session->userdata('wx_user_id'),null,null,6);
        $this->cismarty->assign('data',$data);
        $this->cismarty->display('finance/weixin/index_loaddata.html');
    }

    public function add_finance(){
        $finance_wx_num = time()."_".rand(1000000,9000000);
        $this->cismarty->assign('finance_wx_num',$finance_wx_num);
        $this->cismarty->display('finance/weixin/admin-form.html');
    }

    public function save_finance_1(){
        if($this->input->post('id')){
            $power_ = $this->finance_wx_model->save_power($this->input->post('id'));
            if($power_ != 1){
                $this->show_message('服务已提交,或无保存权限！',site_url('finance_wx_user/index'));
            }
        }
        $rs = $this->finance_wx_model->save_finance_1();
        if($rs >= 1){
            redirect(site_url('/finance_wx_user/edit_finance_detail/'.$rs.'/2'));
        }else if($rs == -2){
            $this->show_message('服务已申请！');
        }else{
            $this->show_message('操作失败！',site_url('finance_wx_user/index'));
        }
    }

    public function save_finance_2(){
        if($this->input->post('id')){
            $power_ = $this->finance_wx_model->save_power($this->input->post('id'));
            if($power_ != 1){
                $this->show_message('服务已提交,或无保存权限！',site_url('finance_wx_user/index'));
            }
        }
        $rs = $this->finance_wx_model->save_finance_2();
        if($rs >= 1){
            redirect(site_url('/finance_wx_user/edit_finance_detail/'.$rs.'/3'));
        }else if($rs == -2){
            $this->show_message('服务已申请！');
        }else{
            $this->show_message('操作失败！');
        }
    }

    public function save_finance_3(){
        if($this->input->post('id')){
            $power_ = $this->finance_wx_model->save_power($this->input->post('id'));
            if($power_ != 1){
                $this->show_message('服务已提交,或无保存权限！',site_url('finance_wx_user/index'));
            }
        }
        $rs = $this->finance_wx_model->save_finance_3();
        if($rs >= 1){
            redirect(site_url('/finance_wx_user/edit_finance_detail/'.$rs.'/4'));
        }else if($rs == -2){
            $this->show_message('服务已申请！');
        }else{
            $this->show_message('操作失败！');
        }
    }

    public function save_finance_4(){
        if($this->input->post('id')){
            $power_ = $this->finance_wx_model->save_power($this->input->post('id'));
            if($power_ != 1){
                $this->show_message('服务已提交,或无保存权限！',site_url('finance_wx_user/index'));
            }
        }
        $rs = $this->finance_wx_model->save_finance_4($this->wxconfig['appid'],$this->wxconfig['appsecret']);
        if($rs >= 1){
            redirect(site_url('finance_wx_user/index'));
        }else if($rs == -2){
            $this->show_message('服务已申请！');
        }else{
            $this->show_message('操作失败！');
        }
    }

    public function tj_finance(){
        if($this->input->post('id')){
            $power_ = $this->finance_wx_model->save_power($this->input->post('id'));
            if($power_ != 1){
                $this->show_message('服务已提交,或无保存权限！',site_url('finance_wx_user/index'));
            }
        }
        $rs = $this->finance_wx_model->save_finance_4($this->wxconfig['appid'],$this->wxconfig['appsecret']);
        if($rs >= 1){
            $tj = $this->finance_model->save_finance_tj();
            if($tj == 1){
                redirect(site_url('/finance_wx_user/index'));
            }else{
                redirect(site_url('/finance_wx_user/index'));//预留,如果在提交前需要 判断一些验证可在这里做处理
            }
        }else if($rs == -2){
            $this->show_message('服务已申请！');
        }else{
            $this->show_message('操作失败！');
        }
    }

    public function edit_finance_detail($id,$html=null){
        if($id){
            $power_ = $this->finance_wx_model->save_power($id);
            if($power_ != 1){
                $this->show_message('服务已提交,或无保存权限！',site_url('finance_wx_user/index'));
            }
        }else{
            redirect(site_url('finance_wx_user/index'));
        }
        $data = $this->finance_model->get_detail($id);
        $this->cismarty->assign('data',$data);
        $this->cismarty->assign('finance_wx_num',$data['finance_wx_num']);
        if(!$html)
            redirect(site_url('finance_wx_user/index'));
        if($html == 2 && $data['borrower_marriage']==2)
            $html = 3;
        if($html == 3 && $data['borrower_hasP']==2)
            $html = 4;
        switch($html){
            case 1:
                $this->cismarty->display('finance/weixin/admin-form.html');
                break;
            case 2:
                $this->cismarty->display('finance/weixin/admin-form-1.html');
                break;
            case 3:
                $this->cismarty->display('finance/weixin/admin-form-2.html');
                break;
            case 4:
                $this->buildWxData();
                $this->cismarty->display('finance/weixin/admin-form-4.html');
                break;
            default:
                redirect(site_url('finance_wx_user/index'));
        }

    }
}