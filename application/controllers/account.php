<?php
/**
 * Created by PhpStorm.
 * User: bin.shen
 * Date: 5/7/16
 * Time: 13:53
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Account extends MY_Controller {

    public function company_account()
    {
          $this->display('company_account.html');
    }
     public function mo_recharge()
        {
              $this->display('mo_recharge.html');
        }
    public function recharge_list()
           {
                 $this->display('recharge_list.html');
           }
}