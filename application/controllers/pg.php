<?php
/**
 * Created by PhpStorm.
 * User: bin.shen
 * Date: 5/31/16
 * Time: 16:23
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Pg extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('pg_model');
    }

    function _remap($method,$params = array()) {
        if(!$this->session->userdata('login_user_id') || in_array(1,$this->session->userdata('login_position_id_array'))) {
            redirect(site_url('/'));
        } else {
            return call_user_func_array(array($this, $method), $params);
        }
    }

    public function pg_list($page=1){
        /*$data = $this->hire_model->hire_list($page);
        $this->assign('hire_list', $data);
        $pager = $this->pagination->getPageLink('/hire/hire_list', $data['countPage'], $data['numPerPage']);
        $this->assign('pager', $pager);*/
        $this->display('pg_list.html');
    }
}