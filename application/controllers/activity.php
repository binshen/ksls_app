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

        $data = $this->activity_model->list_activity($page, array(1,2,3), $role_id > 4 ? $this->session->userdata('login_user_id') : NULL);
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

        $data = $this->activity_model->list_activity($page, array(2,3));
        $this->assign('activity_list', $data);

        $pager = $this->pagination->getPageLink('/activity/list_review', $data['countPage'], $data['numPerPage']);
        $this->assign('pager', $pager);

        $this->display('list_review.html');
    }

    public function add_activity() {
        $activity_type_list = $this->activity_model->get_activity_type_list();
        $this->assign('activity_type_list', json_encode($activity_type_list));

        $this->display('add_activity.html');
    }

    public function edit_activity($id) {
        $activity_type_list = $this->activity_model->get_activity_type_list();
        $this->assign('activity_type_list', json_encode($activity_type_list));

        $activity = $this->activity_model->get_activity_by_id($id);

        $activity['a1t'] = $activity['a1n'] * $activity['a1s'];
        $activity['a2t'] = $activity['a2n'] * $activity['a2s'];
        $activity['a3t'] = $activity['a3n'] * $activity['a3s'];
        $activity['a4t'] = $activity['a4n'] * $activity['a4s'];
        $activity['a5t'] = $activity['a5n'] * $activity['a5s'];
        $activity['att'] = $activity['a1t'] + $activity['a2t'] + $activity['a3t'] + $activity['a4t'] + $activity['a5t'];
        $this->assign('activity', $activity);

        $this->display('add_activity.html');
    }

    public function inspect_activity($id) {
        $activity_type_list = $this->activity_model->get_activity_type_list();
        $this->assign('activity_type_list', json_encode($activity_type_list));

        $activity = $this->activity_model->get_activity_by_id($id);
        $status = $activity['status'];
        if($status == 1) {
            $activity['b1'] = $activity['a1'];
            $activity['b1s'] = $activity['a1s'];
            $activity['b1n'] = $activity['a1n'];
            $activity['b1m'] = '';
            $activity['b2'] = $activity['a2'];
            $activity['b2s'] = $activity['a2s'];
            $activity['b2n'] = $activity['a2n'];
            $activity['b2m'] = '';
            $activity['b3'] = $activity['a3'];
            $activity['b3s'] = $activity['a3s'];
            $activity['b3n'] = $activity['a3n'];
            $activity['b3m'] = '';
            $activity['b4'] = $activity['a4'];
            $activity['b4s'] = $activity['a4s'];
            $activity['b4n'] = $activity['a4n'];
            $activity['b4m'] = '';
            $activity['b5'] = $activity['a5'];
            $activity['b5s'] = $activity['a5s'];
            $activity['b5n'] = $activity['a5n'];
            $activity['b5m'] = '';
        }

        $activity['a1t'] = $activity['a1n'] * $activity['a1s'];
        $activity['a2t'] = $activity['a2n'] * $activity['a2s'];
        $activity['a3t'] = $activity['a3n'] * $activity['a3s'];
        $activity['a4t'] = $activity['a4n'] * $activity['a4s'];
        $activity['a5t'] = $activity['a5n'] * $activity['a5s'];
        $activity['att'] = $activity['a1t'] + $activity['a2t'] + $activity['a3t'] + $activity['a4t'] + $activity['a5t'];

        $activity['b1t'] = $activity['b1n'] * $activity['b1s'];
        $activity['b2t'] = $activity['b2n'] * $activity['b2s'];
        $activity['b3t'] = $activity['b3n'] * $activity['b3s'];
        $activity['b4t'] = $activity['b4n'] * $activity['b4s'];
        $activity['b5t'] = $activity['b5n'] * $activity['b5s'];
        $activity['btt'] = $activity['b1t'] + $activity['b2t'] + $activity['b3t'] + $activity['b4t'] + $activity['b5t'];
        $this->assign('activity', $activity);

        $this->display('inspect_activity.html');
    }

    public function review_activity($id) {
        $activity_type_list = $this->activity_model->get_activity_type_list();
        $this->assign('activity_type_list', json_encode($activity_type_list));

        $activity = $this->activity_model->get_activity_by_id($id);

        $activity['c1'] = $activity['b1'];
        $activity['c1s'] = $activity['b1s'];
        $activity['c1n'] = $activity['b1n'];
        $activity['c1m'] = '';
        $activity['c2'] = $activity['b2'];
        $activity['c2s'] = $activity['b2s'];
        $activity['c2n'] = $activity['b2n'];
        $activity['c2m'] = '';
        $activity['c3'] = $activity['b3'];
        $activity['c3s'] = $activity['b3s'];
        $activity['c3n'] = $activity['b3n'];
        $activity['c3m'] = '';
        $activity['c4'] = $activity['b4'];
        $activity['c4s'] = $activity['b4s'];
        $activity['c4n'] = $activity['b4n'];
        $activity['c4m'] = '';
        $activity['c5'] = $activity['b5'];
        $activity['c5s'] = $activity['b5s'];
        $activity['c5n'] = $activity['b5n'];
        $activity['c5m'] = '';

        $activity['a1t'] = $activity['a1n'] * $activity['a1s'];
        $activity['a2t'] = $activity['a2n'] * $activity['a2s'];
        $activity['a3t'] = $activity['a3n'] * $activity['a3s'];
        $activity['a4t'] = $activity['a4n'] * $activity['a4s'];
        $activity['a5t'] = $activity['a5n'] * $activity['a5s'];
        $activity['att'] = $activity['a1t'] + $activity['a2t'] + $activity['a3t'] + $activity['a4t'] + $activity['a5t'];

        $activity['b1t'] = $activity['b1n'] * $activity['b1s'];
        $activity['b2t'] = $activity['b2n'] * $activity['b2s'];
        $activity['b3t'] = $activity['b3n'] * $activity['b3s'];
        $activity['b4t'] = $activity['b4n'] * $activity['b4s'];
        $activity['b5t'] = $activity['b5n'] * $activity['b5s'];
        $activity['btt'] = $activity['b1t'] + $activity['b2t'] + $activity['b3t'] + $activity['b4t'] + $activity['b5t'];

        $activity['c1t'] = $activity['c1n'] * $activity['c1s'];
        $activity['c2t'] = $activity['c2n'] * $activity['c2s'];
        $activity['c3t'] = $activity['c3n'] * $activity['c3s'];
        $activity['c4t'] = $activity['c4n'] * $activity['c4s'];
        $activity['c5t'] = $activity['c5n'] * $activity['c5s'];
        $activity['ctt'] = $activity['c1t'] + $activity['c2t'] + $activity['c3t'] + $activity['c4t'] + $activity['c5t'];
        $this->assign('activity', $activity);

        $this->display('review_activity.html');
    }

    public function save_activity() {
        $this->activity_model->add_activity();

        redirect(site_url('activity/list_activity'));
    }

    public function assess_activity() {
        $this->activity_model->assess_activity();

        redirect(site_url('activity/list_activity'));
    }

    public function confirm_activity() {
        $this->activity_model->assess_activity();

        redirect(site_url('activity/list_review'));
    }
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
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