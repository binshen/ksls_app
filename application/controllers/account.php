<?php
/**
 * Created by PhpStorm.
 * User: bin.shen
 * Date: 5/7/16
 * Time: 13:53
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Account extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('account_model');
    }

    public function company_account($page=1,$company_id=null)
    {
        if(in_array(7,$this->session->userdata('login_position_id_array'))){
            if($this->input->post('company_id')){
                $company_id = $this->input->post('company_id');
            }
        }else{
            $company_id = $this->session->userdata('login_company_id');
        }
        $data = $this->account_model->company_account($page,$company_id);
        $this->assign('company_account', $data);
        $pager = $this->pagination->getPageLink('/account/company_account', $data['countPage'], $data['numPerPage']);
        $this->assign('pager', $pager);
        $this->display('company_account.html');
    }

     public function mo_recharge()
     {
              $this->display('mo_recharge.html');
     }

    public function recharge_list($page=1){
        $data = $this->account_model->recharge_list($page);
        $this->assign('recharge_list', $data);
        $pager = $this->pagination->getPageLink('/account/recharge_list', $data['countPage'], $data['numPerPage']);
        $this->assign('pager', $pager);
        $this->display('recharge_list.html');
    }
}