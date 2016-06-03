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
        $this->load->model('news_model');
    }

    public function index($page=1) {

        $data = $this->news_model->list_news($page);
        $pager = $this->pagination->getPageLink('/index/index', $data['countPage'], $data['numPerPage']);
        $this->assign('pager', $pager);
        $this->assign('news_list', $data);

        $this->display('index.html');
    }

    public function login() {
        echo $this->user_model->check_login();
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
        $config['upload_path'] = './././uploadfiles/profile';
        $config['allowed_types'] = 'gif|jpg|png|jpeg';
        $config['max_size'] = '1000';
        $config['encrypt_name'] = true;
        $this->load->library('upload', $config);
        if($this->upload->do_upload()) {
            $img_info = $this->upload->data();
            $this->user_model->update_tmp_pic($img_info['file_name']);
        }
        die;
    }

    public function update_user() {
        echo $this->user_model->update_user();
        die;
    }

    public function test() {

        $response = array();
        $username = @$_POST['username'];
        $password = @$_POST['password'];
        if($username == '13913913999') {
            $response['success'] = true;
        } else {
            $response['success'] = false;
        }
        header("Content-type: application/json; charset=utf-8");
        //header("Content-type: text/html;charset=utf-8");
        //$response['success'] = true;
        
        echo json_encode($response);
        die;
    }
}