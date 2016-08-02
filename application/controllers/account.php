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

    public function company_account()
    {
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