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
}