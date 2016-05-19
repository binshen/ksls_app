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

        $this->load->model('activity_model');
    }

    public function list_agenda() {

        $this->display('list_agenda.html');
    }
}