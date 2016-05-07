<?php
/**
 * Created by PhpStorm.
 * User: bin.shen
 * Date: 5/7/16
 * Time: 13:53
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Activity extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
    }

    public function list_activity() {
        $this->load->view('list_activity.html');
    }

    public function list_review() {
        $this->load->view('list_review.html');
    }
}