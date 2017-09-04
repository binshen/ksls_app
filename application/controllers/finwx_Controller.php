<?php
/**
 * Created by PhpStorm.
 * User: bin.shen
 * Date: 5/31/16
 * Time: 16:23
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Finwx_Controller extends CI_Controller
{
    protected $wxconfig = array();
    public function __construct()
    {
        parent::__construct();
        ini_set('date.timezone','Asia/Shanghai');
        $this->load->model('finance_model');
        $this->load->model('finance_wx_model');
        $this->load->helper('url');
        $this->load->config('wxpay_config');
        $this->wxconfig['appid']=$this->config->item('fin_appid');
        $this->wxconfig['appsecret']=$this->config->item('fin_appsecret');
        //var_dump($this->wxconfig);
        if ( strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false ) {
            if(!$this->session->userdata('openid')){
                $appid = $this->wxconfig['appid'];
                $secret = $this->wxconfig['appsecret'];
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
                    $res = $this->finance_wx_model->check_openid($openid);

                }
            }
        }

    }

    //重载smarty方法assign
    public function assign($key,$val) {
        $this->cismarty->assign($key,$val);
    }

    //重载smarty方法display
    public function display($html) {
        $this->cismarty->display($html);
    }

    public function set_base_code($token){
        require_once (APPPATH . 'libraries/Base64.php');
        try{
            $token = base64_decode($token);
            $token = base64::decrypt($token, $this->config->item('token_key'));
            $token = explode('_', $token);
            if($token[0]!= 'FIN') return -1;
            $t = time() - $token[2];
            if($t >= 60 * 60) return -2;
        }catch(Exception $e){
            return -3;
        }
        return (int)$token[1];
    }

    public function buildWxData(){

        $signPackage = $this->wxgetSignPackage_fin();
        //变量
        $this->cismarty->assign('wxappId',$this->wxconfig['appid']);
        $this->cismarty->assign('wxtimestamp',$signPackage["timestamp"]);
        $this->cismarty->assign('wxnonceStr',$signPackage["nonceStr"]);
        $this->cismarty->assign('wxsignature',$signPackage["signature"]);
    }

    public function wxgetSignPackage_fin() {
        echo 'yy';
        $jsapiTicket = $this->finance_model->get_ticket($this->wxconfig['appid'],$this->wxconfig['appsecret']);
        die(var_dump($jsapiTicket));
        // 注意 URL 一定要动态获取，不能 hardcode.
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        $timestamp = time();
        $nonceStr = $this->wxcreateNonceStr();

        // 这里参数的顺序要按照 key 值 ASCII 码升序排序
        $string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";

        $signature = sha1($string);

        $signPackage = array(
            "appId"     => $this->appId,
            "nonceStr"  => $nonceStr,
            "timestamp" => $timestamp,
            "url"       => $url,
            "signature" => $signature,
            "rawString" => $string
        );
        return $signPackage;
    }

    private function wxcreateNonceStr($length = 16) {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

}