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
        $this->buildWxData();
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

    public function code_login($code=null){
        if(!$code){
            $code = $this->input->post('finance_wx_num');
            /*$list = explode('/',$url_str);
            $code=$list[count($list)-1];*/
        }

        //$replace_str = $this->config->item('base_url_wx').'/finance_wx/code_login/';
        //var_dump($replace_str);
        //$code = str_replace($replace_str,'',$code);
        //die(var_dump($code));
        $finance_id = $this->set_base_code($code);
        if($finance_id==-1){
            $this->cismarty->assign('tabs',0);
            $this->cismarty->assign('flag',-4);
            $this->cismarty->display('finance/login.html');
        }
        if($finance_id==-2){
            $this->cismarty->assign('tabs',0);
            $this->cismarty->assign('flag',-5);
            $this->cismarty->display('finance/login.html');
        }
        $res = $this->finance_wx_model->code_login($finance_id);
        if($res==1){
            redirect('finance_wx_borrower/index');
        }else{
            $this->cismarty->assign('tabs',0);
            $this->cismarty->assign('flag',-3);
            $this->cismarty->display('finance/login.html');
        }
    }

}