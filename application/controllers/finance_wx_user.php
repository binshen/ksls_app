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
        if(!$this->session->userdata('user_id')){
            redirect('finance_wx/login');
        }
        $this->assign('rel_name',$this->session->userdata('rel_name'));
        $this->assign('role_name',$this->session->userdata('role_name'));
        $permission_id = $this->session->userdata('permission_id');
        $this->assign('permission_id', $permission_id);
        $position_id = $this->session->userdata('position_id_array');
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

    /*public function list_finance($page=1){
        $main_data = $this->finance_wx_model->get_main_data();
        $this->cismarty->assign('main_data',$main_data);
        // $this->cismarty->assign('jindu_type',$jindu_type);
        $data = $this->finance_model->finance_list($page,$this->session->userdata('user_id'));
        $base_url = "/finance_wx_user/list_finance/";
        $pager = $this->pagination->getPageLink($base_url, $data['countPage'], $data['numPerPage']);
        $this->cismarty->assign('pager',$pager);
        $this->cismarty->assign('data',$data);
        $this->cismarty->display('finance/user_finance_list.html');
    }*/

    public function list_finance_loaddata($page=1){
        $data = $this->finance_model->finance_list($page,$this->session->userdata('user_id'),null,null,6);
        $this->cismarty->assign('data',$data);
        $this->cismarty->display('finance/weixin/index_loaddata.html');
    }

}