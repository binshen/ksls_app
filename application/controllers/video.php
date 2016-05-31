<?php
/**
 * Created by PhpStorm.
 * User: bin.shen
 * Date: 5/31/16
 * Time: 16:23
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Video extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function list_video() {

        $this->display('online_class.html');
    }

    public function view_video() {

        $this->display('video_play.html');
    }
}