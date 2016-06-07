<?php
/**
 * Created by PhpStorm.
 * User: bin.shen
 * Date: 5/31/16
 * Time: 16:23
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Examination extends MY_Controller
{
    public function self_examination()
    {

        $this->display('self_examination.html');
    }
    public function do_examination()
        {

            $this->display('do_examination.html');
        }
    public function submit_examination()
        {

            $this->display('submit_examination.html');
        }
     public function unit_examination()
            {

                $this->display('unit_examination.html');
            }
}