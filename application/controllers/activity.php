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
        $this->load->model('activity_model');
    }

    public function list_activity() {
        $this->load->view('list_activity.html');
    }

    public function list_review() {

        $role_id = $this->session->userdata('role_id');
        if($role_id == 1) {
            $company_list = $this->activity_model->get_company_list();
            $this->assign('company_list', $company_list);
        } else {
            $company_id = $this->session->userdata('company_id');
            $subsidiary_list = $this->activity_model->get_subsidiary_list($company_id);
            $this->assign('subsidiary_list', $subsidiary_list);
        }
        

        $this->display('list_review.html');
    }

    public function get_subsidiary_list($company_id) {

        $subsidiary_list = $this->activity_model->get_subsidiary_list($company_id);
        echo json_encode($subsidiary_list);
        die;
    }
}