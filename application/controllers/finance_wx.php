<?php
/**
 * Created by PhpStorm.
 * User: bin.shen
 * Date: 5/31/16
 * Time: 16:23
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once "finwx_Controller.php";
class Finance_wx extends Finwx_Controller
{
    protected $wxconfig = array();
    public function __construct()
    {
        parent::__construct();
        if($this->session->userdata('user_id')){
            redirect('finance_wx_user/index');
        }else{
            if($this->session->userdata('finance_id')){
                redirect('finance_wx_borrower/index');
            }
        }
    }

    public function login(){

        $this->assign('tabs',0);
        $this->assign('flag',1);
        $this->display('finance/login.html');

    }

    public function user_login(){
        $res = $this->finance_wx_model->user_login();
        if($res==1){
            redirect('finance_wx_user/index');
        }else{
            $this->cismarty->assign('tabs',1);
            $this->cismarty->assign('flag',-1);
            $this->cismarty->display('finance/login.html');
        }

    }

    public function finance_login(){
        $res = $this->finance_wx_model->finance_login();
        if($res==1){
            redirect('finance_wx_borrower/index');
        }else{
            $this->cismarty->assign('tabs',0);
            $this->cismarty->assign('flag',-2);
            $this->cismarty->display('finance/login.html');
        }
    }

    public function code_login($code){
        $res = $this->finance_wx_model->code_login($code);
        if($res==1){
            redirect('finance_wx_borrower/index');
        }else{
            $this->cismarty->assign('tabs',0);
            $this->cismarty->assign('flag',-3);
            $this->cismarty->display('finance/login.html');
        }
    }

}