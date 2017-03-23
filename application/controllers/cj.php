<?php
/**
 * Created by PhpStorm.
 * User: bin.shen
 * Date: 6/2/16
 * Time: 21:22
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cj extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('cj_model');
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
        $this->display('choujiang/choujiang.html');
    }

    public function get_number(){
        $number = $this->cj_model->get_number();
        echo $number;
    }

    public function del_cj(){
        $this->cj_model->del_cj();
    }
}

