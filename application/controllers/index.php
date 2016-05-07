<?php
/**
 * Created by PhpStorm.
 * User: bin.shen
 * Date: 5/2/16
 * Time: 09:56
 */

 if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Index extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
    }

    public function index() {
        $this->load->view('index.html');
    }

    public function login() {

    }

    public function logout() {

    }
}