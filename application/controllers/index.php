<?php
/**
 * Created by PhpStorm.
 * User: bin.shen
 * Date: 5/2/16
 * Time: 09:56
 */

 if ( ! defined('BASEPATH')) exit('No direct script access allowed');

//test test222
class Index extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('user_model');
    }

    public function index() {
        $this->load->view('index.html');
    }

    public function login() {
        if($this->user_model->check_login()){

        }
    }

    public function logout() {

    }
}