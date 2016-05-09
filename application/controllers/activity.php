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

        //////////////
        //for test only
        $user_info['login_user_id'] = 5;
        $user_info['login_username'] = 'test';
        $user_info['login_rel_name'] = 'Test';
        $user_info['login_role_id'] = 1;
        $user_info['login_company_id'] = 1;
        $user_info['login_subsidiary_id'] = 2;
        $this->session->set_userdata($user_info);
        //////////////

        $this->load->model('activity_model');
    }

    public function list_activity($page=1) {
        $role_id = $this->session->userdata('login_role_id');
        $this->assign('role_id', $role_id);

        if($this->input->POST('start_date')) {
            $this->assign('start_date', $this->input->POST('start_date'));
        }
        if($this->input->POST('end_date')) {
            $this->assign('end_date', $this->input->POST('end_date'));
        }

        $data = $this->activity_model->list_activity($page, [1,2], $role_id > 4 ? $this->session->userdata('login_user_id') : NULL);
        $this->assign('activity_list', $data);

        $pager = $this->pagination->getPageLink('/activity/list_activity', $data['countPage'], $data['numPerPage']);
        $this->assign('pager', $pager);

        $this->display('list_activity.html');
    }

    public function list_review($page=1) {

        $role_id = $this->session->userdata('login_role_id');
        $this->assign('role_id', $role_id);
        if($role_id == 1) {
            $company_list = $this->activity_model->get_company_list();
            $this->assign('company_list', $company_list);
        }

        if($this->input->POST('company')) {
            $this->assign('company', $this->input->POST('company'));
            $subsidiary_list = $this->activity_model->get_subsidiary_list($this->input->POST('company'), NULL);
        } else {
            $company_id = $this->session->userdata('login_company_id');
            if($role_id < 4) {
                $subsidiary_list = $this->activity_model->get_subsidiary_list($company_id, NULL);
            } else if($role_id == 4) {
                $subsidiary_id = $this->session->userdata('login_subsidiary_id');
                $subsidiary_list = $this->activity_model->get_subsidiary_list($company_id, $subsidiary_id);
            }
        }
        $this->assign('subsidiary_list', $subsidiary_list);

        if($this->input->POST('subsidiary')) {
            $this->assign('subsidiary', $this->input->POST('subsidiary'));

            $user_list = $this->activity_model->get_subsidiary_user_list($this->input->POST('subsidiary'));
            $this->assign('user_list', $user_list);
        }
        if($this->input->POST('user')) {
            $this->assign('user', $this->input->POST('user'));
        }
        if($this->input->POST('start_date')) {
            $this->assign('start_date', $this->input->POST('start_date'));
        }
        if($this->input->POST('end_date')) {
            $this->assign('end_date', $this->input->POST('end_date'));
        }

        $data = $this->activity_model->list_activity($page, [2,3]);
        $this->assign('activity_list', $data);

        $pager = $this->pagination->getPageLink('/activity/list_review', $data['countPage'], $data['numPerPage']);
        $this->assign('pager', $pager);

        $this->display('list_review.html');
    }

    public function add_activity() {
        $this->display('add_activity.html');
    }

    public function edit_activity() {
        $this->display('add_activity.html');
    }

    public function inspect_activity() {
        $this->display('add_activity.html');
    }

    public function review_activity() {
        $this->display('add_activity.html');
    }

    public function get_subsidiary_list($company_id) {

        $subsidiary_list = $this->activity_model->get_subsidiary_list($company_id);
        echo json_encode($subsidiary_list);
        die;
    }

    public function get_subsidiary_user_list($subsidiary_id) {

        $subsidiary_user_list = $this->activity_model->get_subsidiary_user_list($subsidiary_id);
        echo json_encode($subsidiary_user_list);
        die;
    }
}