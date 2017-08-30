<?php
/**
 * Created by PhpStorm.
 * User: bin.shen
 * Date: 5/31/16
 * Time: 16:23
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Finance_wx extends CI_Controller
{
    protected $wxconfig = array();
    public function __construct()
    {
        parent::__construct();
        ini_set('date.timezone','Asia/Shanghai');
        $this->load->model('finance_model');
        $this->load->model('wxserver_model');
        $this->load->helper('url');
        $this->load->config('wxpay_config');
        $this->wxconfig['appid']=$this->config->item('fin_appid');
        $this->wxconfig['appsecret']=$this->config->item('fin_appsecret');
        var_dump($this->wxconfig);
        if ( strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false ) {
            if(!$this->session->userdata('openid')){
                $appid = $this->wxconfig['appid'];
                $secret = $this->wxconfig['appsecret'];
                var_dump($appid);
                var_dump($secret);
                die();
                if(empty($_GET['code'])){
                    $url = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER["REQUEST_URI"]; //这是要回调地址可以有别的写法
                    redirect("https://open.weixin.qq.com/connect/oauth2/authorize?appid={$appid}&redirect_uri={$url}&response_type=code&scope=snsapi_base&state=STATE#wechat_redirect");
                    //重定向到以上网址,这是微信给的固定地址.必须格式一致
                }else{
                    //回调成功,获取code,再做请求,获取openid
                    $j_access_token=file_get_contents("https://api.weixin.qq.com/sns/oauth2/access_token?appid={$appid}&secret={$secret}&code={$_GET['code']}&grant_type=authorization_code");
                    $a_access_token=json_decode($j_access_token,true);
                    $access_token=$a_access_token["access_token"];//虽然这里 也获取了一个access_token,但是和获取用户详情,还有发送模板信息所使用的access_token不同
                    $openid=$a_access_token["openid"];
                    $this->session->set_userdata('openid', $openid);
                }
            }
        }
    }

    public function login(){
        /*$data['res'] = 0;
        $data['user_info'] = array();
        if($this->session->userdata('openid')){
            $res = $this->wxserver_model->check_openid();
            if($res){
                $data['user_info'] = $res;
            }
            $this->assign('data', $data);
            $this->display('finance/login.html');
        }*/
        echo "Hello FunMall";
    }

    public function bdwx(){
        $data['res'] = 0;
        $data['user_info'] = array();
        if($this->session->userdata('openid')){
            $res = $this->wxserver_model->check_openid();
            if($res){
                $data['user_info'] = $res;
            }
            $this->assign('data', $data);
            $this->display('wxhtml/login.html');
        }

    }

    public function save_openid(){
        $res = $this->wxserver_model->save_openid();
        $data['res'] = $res;
        $data['user_info'] = array();
        if($this->session->userdata('openid')){
            $res = $this->wxserver_model->check_openid();
            if($res){
                $data['user_info'] = $res;
            }
            $this->assign('data', $data);
            $this->display('finance/login.html');
        }
    }

}