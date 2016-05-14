<?php
/**
 * Created by PhpStorm.
 * User: bin.shen
 * Date: 5/2/16
 * Time: 09:56
 */

 if ( ! defined('BASEPATH')) exit('No direct script access allowed');

//test test3
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

    public function check_pass($pass) {
        $login_password = $this->session->userdata('login_password');
        echo $login_password == sha1($pass) ? 1 : 0;
        die;
    }

    public function update_password() {
        echo $this->user_model->update_password();
        die;
    }

    public function upload_pic() {
        var_dump($_FILES);
        die;
    }

    public function update_user() {
        echo $this->user_model->update_user();
        die;
    }
}