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

    public function __construct()
    {
        parent::__construct();

        $this->load->model('examination_model');
    }

    function _remap($method, $params = array())
    {
        if(!$this->session->userdata('login_user_id')) {
            redirect(site_url('/'));
        } else {
            return call_user_func_array(array($this, $method), $params);
        }
    }

    public function self_examination()
    {

        $question_type_list = $this->examination_model->get_question_type_list();
        $this->assign('question_type_list', $question_type_list);
        
        $this->display('self_examination.html');
    }

    public function do_examination($type_id=1, $question_id=NULL)
    {
        $user_id = $this->session->userdata('login_user_id');
        $exam = $this->examination_model->get_user_exam($user_id, $type_id);
        if(!empty($exam)) {
            $exam_id = $exam['id'];
        } else {
            $exam_id = $this->examination_model->gen_exam_data($user_id, $type_id);
        }
        $exam_data = $this->examination_model->get_exam_list($exam_id, $question_id);
        $this->assign('exam_data', $exam_data);

        $question_data = $this->examination_model->get_exam_question($exam_id);
        $this->assign('question_data', $question_data);

        $this->assign('type_id', $type_id);

        $this->display('do_examination.html');
    }

    public function get_examination($type_id=1) {
        
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