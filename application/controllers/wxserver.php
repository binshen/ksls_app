<?php
/**
 * Created by PhpStorm.
 * User: yangyang
 * Date: 16/8/1
 * Time: 上午11:36
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

//t
class Wxserver extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        if ( strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false ) {
            if(!$this->session->userdata('openid')){
                $appid = APP_ID;
                $secret = APP_SECRET;

                if(empty($_GET['code'])){
                    $url = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER["REQUEST_URI"];
                    $url = urlencode($url);
                    die($url);
                    redirect("https://open.weixin.qq.com/connect/oauth2/authorize?appid={$appid}&redirect_uri={$url}&response_type=code&scope=snsapi_base&state=STATE#wechat_redirect");
                }else{
                    $j_access_token=file_get_contents("https://api.weixin.qq.com/sns/oauth2/access_token?appid={$appid}&secret={$secret}&code={$_GET['code']}&grant_type=authorization_code");
                    $a_access_token=json_decode($j_access_token,true);
                    $access_token=$a_access_token["access_token"];
                    $openid=$a_access_token["openid"];
                    $rs = $this->sysconfig_model->check_openid($openid);
                    $this->session->set_userdata('openid', $openid);
                }
            }
        }
    }

    public function index()
    {
        $echoStr = $_GET["echostr"];

        //valid signature , option
        if($this->checkSignature()){
            echo $echoStr;
            exit;
        }
    }

    public function responseMsg()
    {
        //get post data, May be due to the different environments
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];

        //extract post data
        if (!empty($postStr)){

            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $fromUsername = $postObj->FromUserName;
            $toUsername = $postObj->ToUserName;
            $keyword = trim($postObj->Content);
            $time = time();
            $textTpl = "<xml>
							<ToUserName><![CDATA[%s]]></ToUserName>
							<FromUserName><![CDATA[%s]]></FromUserName>
							<CreateTime>%s</CreateTime>
							<MsgType><![CDATA[%s]]></MsgType>
							<Content><![CDATA[%s]]></Content>
							<FuncFlag>0</FuncFlag>
							</xml>";
            if(!empty( $keyword ))
            {
                $msgType = "text";
                $contentStr = "Welcome to wechat world!";
                $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                echo $resultStr;
            }else{
                echo "Input something...";
            }

        }else {
            echo "";
            exit;
        }
    }

    private function checkSignature()
    {
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];

        $token = TOKEN;
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );

        if( $tmpStr == $signature ){
            return true;
        }else{
            return false;
        }
    }

    public function bdwx(){
        echo ('123');
       // $this->display('wxhtml/login.html');
    }
}