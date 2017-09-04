<?php
/**
 * Created by PhpStorm.
 * User: bin.shen
 * Date: 5/31/16
 * Time: 16:23
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once "finwx_Controller.php";
class Finance_wx_borrower extends Finwx_Controller
{

    public function __construct()
    {
        parent::__construct();
        if(!$this->session->userdata('finance_id')){
            redirect('finance_wx/login');
        }

        $this->assign('finance_num',$this->session->userdata('finance_num'));
    }

    public function logout(){
        $this->finance_wx_model->logout();
        redirect('finance_wx/login');
    }

    public function index(){
        $this->display('finance/borrower_show.html');
    }



}