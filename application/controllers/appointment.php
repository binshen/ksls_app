<?php
/**
 * Created by PhpStorm.
 * User: bin.shen
 * Date: 5/26/16
 * Time: 12:57
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Appointment extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->model('agenda_model');

        $this->load->library('image_lib');
        $this->load->helper('directory');
    }

    function _remap($method, $params = array())
    {
        if(!$this->session->userdata('login_user_id')) {
            redirect(site_url('/'));
        } else {
            return call_user_func_array(array($this, $method), $params);
        }
    }

    function book_room() {

        $this->display('booking_room.html');
    }
}