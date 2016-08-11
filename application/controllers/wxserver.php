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
        ini_set('date.timezone','Asia/Shanghai');
        $this->load->model('wxserver_model');
        $this->load->helper('url');
        if ( strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false ) {
            if(!$this->session->userdata('openid')){
                $appid = APP_ID; //我把微信的appid 写成了全局变量,一般放在application/config/constant.php 中
                $secret = APP_SECRET;//同上

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
        }else{
           // $this->session->set_userdata('openid', '123123');
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
            $this->display('wxhtml/login.html');
        }
    }

    public function text(){
        /*$dataxml['first'] = array('value'=>'数据提交成功');
        $dataxml['keynote1'] = array('value'=>$this->input->post('title'));
        $dataxml['keynote2'] = array('value'=>date("Y-m-d H:i:s"));
        $dataxml['remark'] = array('value'=>'');

        $data = array(
            "touser"=>'oFzKgwbFEyC40jU6bS_HQ5sxM4X8',
            "template_id"=>'GCLMW8LVj59vIBGfAnoTjo-98pcxBcZak_4eFornX0g',
            "url"=>"http://weixin.qq.com/download",
            'data' => urldecode(json_encode($dataxml))
        );

        die(var_dump(json_encode($data)));*/

        $access_token = 'KS3N4n80ZPeLsxPQIlgicPC5fGfyjhXAILK4Nv5QbV4xm4uuOnoYYJUbu89p1g0fqVmWZjdsg3ypfvnJ3CzcSXUwd7q1K9RPSMsNqRHl_e8';
        $url = '改成接口URL ?access_token=' . $access_token;//access_token改成你的有效值

        $data = array(
            'first' => array(
                'value' => '有一名客户进行了一次预约！',
                'color' => '#FF0000'
            ),
            'keyword1' => array(
                'value' => '2015/10/5 14:00~14:45',
                'color' => '#FF0000'
            ),
            'keyword2' => array(
                'value' => '都会型SPA',
                'color' => '#FF0000'
            ),
            'remark' => array(
                'value' => '请您务必准时到场为客户提供SPA服务！',
                'color' => '#FF0000'
            )
        );
        $template_msg=array('touser'=>'oFzKgwbFEyC40jU6bS_HQ5sxM4X8','template_id'=>'GCLMW8LVj59vIBGfAnoTjo-98pcxBcZak_4eFornX0g','topcolor'=>'#FF0000','data'=>$data);
        die(var_dump($template_msg));
        $curl = curl_init($url);
        $header = array();
        $header[] = 'Content-Type: application/x-www-form-urlencoded';
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
// 不输出header头信息
        curl_setopt($curl, CURLOPT_HEADER, 0);
// 伪装浏览器
        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2272.118 Safari/537.36');
// 保存到字符串而不是输出
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
// post数据
        curl_setopt($curl, CURLOPT_POST, 1);
// 请求数据
        curl_setopt($curl,CURLOPT_POSTFIELDS,json_encode($template_msg));
        $response = curl_exec($curl);
        curl_close($curl);
        echo $response;
    }

    public function test2(){
        phpinfo();
    }
}