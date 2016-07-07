<?php
/**
 * Created by PhpStorm.
 * User: bin.shen
 * Date: 5/31/16
 * Time: 16:23
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Hire extends MY_Controller
{
    public function add_hire() {
        $this->display('add_hire.html');
    }
   public function hire_list(){
        $this->display('hire_list.html');
    }
   public function hire_deadline_list(){
        $this->display('hire_deadline_list.html');
   }
}