<?php
/**
 * Created by PhpStorm.
 * User: bin.shen
 * Date: 5/31/16
 * Time: 16:23
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once "agendawx_Controller.php";
class Agenda_wx_user extends Agendawx_Controller
{

    public function __construct()
    {
        parent::__construct();
        if(!$this->session->userdata('agewx_user_id')){
            redirect('agenda_wx/login');
        }
        $this->assign('rel_name',$this->session->userdata('agewx_rel_name'));
        $this->assign('role_name',$this->session->userdata('agewx_role_name'));
        $permission_id = $this->session->userdata('agewx_permission_id');
        $this->assign('permission_id', $permission_id);
        $position_id = $this->session->userdata('agewx_position_id_array');
        $this->assign('position_id', $position_id);
        $user_id = $this->session->userdata('agewx_user_id');
        $this->assign('user_id', $user_id);
    }

    public function logout(){
        $this->agenda_wx_model->logout();
        redirect('agenda_wx/login');
    }

    public function index(){

        $this->display('agenda/ok_login.html');
    }


}