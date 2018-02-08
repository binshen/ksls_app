<?php
/**
 * Created by PhpStorm.
 * User: bin.shen
 * Date: 6/2/16
 * Time: 21:22
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');
//用于开讯
class Kaixun extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        ini_set('date.timezone','Asia/Shanghai');
        //$this->load->model('cj_model');
    }

    //重载smarty方法assign
    public function assign($key,$val) {
        $this->cismarty->assign($key,$val);
    }

    //重载smarty方法display
    public function display($html) {
        $this->cismarty->display($html);
    }

    public function index(){
        $this->display('download.html');
    }
}

