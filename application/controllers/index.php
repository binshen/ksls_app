<?php
/**
 * Created by PhpStorm.
 * User: bin.shen
 * Date: 5/2/16
 * Time: 09:56
 */

 if ( ! defined('BASEPATH')) exit('No direct script access allowed');

//t
class Index extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('user_model');
    }

    public function index() {
        $this->display('index.html');
    }

    public function login() {
        echo $this->user_model->check_login() ? 1 : 0;
        die;
    }

    public function logout() {
        $this->session->sess_destroy();
        redirect(site_url('/'));
    }

    public function check_login() {
        echo $this->session->userdata('login_user_id') ? 1 : 0;
        die;
    }
}