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
        $this->assign('main_data',$main_data);
        $this->display('finance/user_search.html');
    }



}