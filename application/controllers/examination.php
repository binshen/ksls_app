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

    public function do_examination($type_id=1, $exam_id=NULL, $num=1)
    {
        $user_id = $this->session->userdata('login_user_id');
        if(empty($exam_id)) {
            $exam = $this->examination_model->get_user_exam($user_id, $type_id);
            if(!empty($exam)) {
                $exam_id = $exam['id'];
            } else {
                $exam_id = $this->examination_model->gen_exam_data($user_id, $type_id);
            }
        }
        $this->assign('exam_id', $exam_id);

        if(!empty($_POST['eq_id']) && !empty($_POST['option'])) {
            $op = $_POST['option'];
            $data = array('as1' => 0, 'as2' => 0, 'as3' => 0, 'as4' => 0, 'complete' => 0);
            if($op == 'A') {
                $data['as1'] = 1;
                $data['complete'] = 1;
            } else if($op == 'B') {
                $data['as2'] = 1;
                $data['complete'] = 1;
            } else if($op == 'C') {
                $data['as3'] = 1;
                $data['complete'] = 1;
            } else if($op == 'D') {
                $data['as4'] = 1;
                $data['complete'] = 1;
            }
            $this->examination_model->take_exam($_POST['eq_id'], $data);
        }

        $exam_data = $this->examination_model->get_exam_by_num($exam_id, $num);
        $this->assign('exam_data', $exam_data);

        $question_data = $this->examination_model->get_exam_question($exam_id);
        $this->assign('question_data', $question_data);

        $this->assign('num', $num);

        $this->display('do_examination.html');
    }

    public function complete_examination($exam_id) {

        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->examination_model->complete_examination($exam_id);
            redirect(site_url('/examination/submit_examination/' . $exam_id));
        } else {
            redirect(site_url('/examination/self_examination'));
        }
    }

    public function submit_examination($exam_id,$question_id=null)
    {
        $exam_data = $this->examination_model->get_sub_exam_list($exam_id, $question_id);
        $this->assign('exam_data', $exam_data);
        $question_true = $this->examination_model->get_true_exam_question($exam_id);
        $this->assign('question_true', $question_true);
        $this->assign('exam_id', $exam_id);
        $this->display('submit_examination.html');
    }

    public function unit_examination()
    {

        $this->display('unit_examination.html');
    }

    public function chenge_option($eq_id,$val,$as){
        $this->examination_model->chenge_option($eq_id,$val,$as);
    }

    public function enter_examination(){
        $type_list = $this->examination_model->get_type();
        $this->assign('type_list', $type_list);
        $this->display("entering_examination.html");
    }

    public function review_examination(){
        $this->display("review_examination.html");
    }
    public function setup_examination(){
        $this->display("setup_examination1.html");
    }
    public function choose_items(){
        $this->display("setup_examination2.html");
    }
    public function examination_score(){
        $score_list = $this->examination_model->get_my_score_list();
        $this->assign('score_list', $score_list);
        $this->display("examination_score.html");
    }
    public function examination_list(){
        $exam_list = $this->examination_model->get_my_exam_list();
        $this->assign('exam_list', $exam_list);
        $this->display("examination_list.html");
    }

    public function save_question(){
        $this->examination_model->save_question();
        redirect(site_url('/examination/enter_examination'));
    }
}