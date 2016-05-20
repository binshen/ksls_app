<?php
/**
 * Created by PhpStorm.
 * User: bin.shen
 * Date: 5/19/16
 * Time: 15:59
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Agenda extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->model('agenda_model');
    }

    public function list_agenda($page=1) {
        $course_list = $this->agenda_model->get_course();
        $this->assign('course_list', $course_list);
        if($this->input->POST('status')) {
            $this->assign('status', $this->input->POST('status'));
        }
        if($this->input->POST('course')) {
            $this->assign('course', $this->input->POST('course'));
        }
        $data = $this->agenda_model->list_agenda($page, $this->session->userdata('login_user_id'));
        $this->assign('agenda_list', $data);

        $pager = $this->pagination->getPageLink('/agenda/list_agenda', $data['countPage'], $data['numPerPage']);
        $this->assign('pager', $pager);
        $this->display('list_agenda.html');
    }

    function list_agenda_other($page=1){
        $role_id = $this->session->userdata('login_role_id');
        $this->assign('role_id', $role_id);
        if($role_id == 1) {
            $company_list = $this->agenda_model->get_company_list();
            $this->assign('company_list', $company_list);
        }

        if($this->input->POST('company')) {
            $this->assign('company', $this->input->POST('company'));
            $subsidiary_list = $this->agenda_model->get_subsidiary_list($this->input->POST('company'), NULL);
        } else {
            $company_id = $this->session->userdata('login_company_id');
            if($role_id < 4) {
                $subsidiary_list = $this->agenda_model->get_subsidiary_list($company_id, NULL);
            } else if($role_id == 4) {
                $subsidiary_id = $this->session->userdata('login_subsidiary_id');
                $subsidiary_list = $this->agenda_model->get_subsidiary_list($company_id, $subsidiary_id);
            }
        }
        $this->assign('subsidiary_list', $subsidiary_list);
        if($this->input->POST('subsidiary')) {
            $this->assign('subsidiary', $this->input->POST('subsidiary'));

            $user_list = $this->agenda_model->get_subsidiary_user_list($this->input->POST('subsidiary'));
            $this->assign('user_list', $user_list);
        }
        if($this->input->POST('user')) {
            $this->assign('user', $this->input->POST('user'));
        }

        $company_id = NULL;
        if($role_id > 1) {
            $company_id = $this->session->userdata('login_company_id');
        }
        $subsidiary_id = NULL;
        if($role_id >= 4) {
            $subsidiary_id = $this->session->userdata('login_subsidiary_id');
        }

        $course_list = $this->agenda_model->get_course();
        $this->assign('course_list',$course_list);
        if($this->input->POST('status')) {
            $this->assign('status', $this->input->POST('status'));
        }
        if($this->input->POST('course')) {
            $this->assign('course', $this->input->POST('course'));
        }
        $data = $this->agenda_model->list_agenda($page,null,$subsidiary_id,$company_id);
        $this->assign('agenda_list', $data);

        $pager = $this->pagination->getPageLink('/agenda/list_agenda_other', $data['countPage'], $data['numPerPage']);
        $this->assign('pager', $pager);
        $this->display('list_agenda_other.html');
    }

    public function add_agenda() {
        $this->display('add_agenda.html');
    }

    public function view_agenda() {
        $this->display('view_agenda.html');
    }
}