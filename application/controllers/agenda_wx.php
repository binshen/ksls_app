<?php
/**
 * Created by PhpStorm.
 * User: bin.shen
 * Date: 5/31/16
 * Time: 16:23
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once "agendawx_Controller.php";
class Agenda_wx extends Agendawx_Controller
{
    public function __construct()
    {
        parent::__construct();
        if($this->session->userdata('agewx_user_id')){
            redirect('agenda_wx_user/index');
        }else{
            //if($this->session->userdata('agewx_agenda_id')){
              //  redirect('agenda_wx_borrower/index');
            //}
        }
        $this->buildWxData();
    }

    public function login(){
        $this->assign('tabs',0);
        $this->assign('flag',1);
        //$this->display('finance/login.html');
        $this->display('agenda_wx/wx_login.html');
    }

    public function user_login(){

        die(var_dump('123'));
        $res = $this->agenda_wx_model->user_login();
        if($res==1){
            redirect('agenda_wx_user/index');
        }else{
            $this->cismarty->assign('tabs',1);
            $this->cismarty->assign('flag',-1);
            //$this->cismarty->display('finance/login.html');
            $this->display('agenda_wx/wx_login.html');
        }

    }

    public function agenda_login(){

    }

    public function code_login($code=null){
        //此方法是用于 申请人 扫码关注微信
        $access_token = $this->agenda_wx_model->get_token($this->wxconfig['appid'],$this->wxconfig['appsecret']);
        $rs = file_get_contents("https://api.weixin.qq.com/cgi-bin/user/info?access_token={$access_token}&openid={$this->session->userdata('openid')}&lang=zh_CN");
        $rs = json_decode($rs,true);


    }

    private function get_or_create_ticket($access_token,$action_name = 'QR_LIMIT_STR_SCENE') {



        $url = 'https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=' . $access_token;
        @$post_data->expire_seconds = 2592000;
        @$post_data->action_name = $action_name;
        @$post_data->action_info->scene->scene_str = 'yy';
        $ticket_data = json_decode($this->post($url, $post_data));
        $ticket = $ticket_data->ticket;
        $img_url = "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=".urlencode($ticket);
        return $img_url;
    }

    private function post($url, $post_data, $timeout = 300){
        $options = array(
            'http' => array(
                'method' => 'POST',
                'header' => 'Content-type:application/json;encoding=utf-8',
                'content' => urldecode(json_encode($post_data)),
                'timeout' => $timeout
            )
        );
        $context = stream_context_create($options);
        return file_get_contents($url, false, $context);
    }

}