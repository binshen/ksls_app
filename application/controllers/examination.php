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
        //这里规定 $type_id = -1 时,就是 统一考试 选择题部分
        $user_id = $this->session->userdata('login_user_id');
        if(empty($exam_id)) {
                $exam = $this->examination_model->get_user_exam($user_id, $type_id);
                if(!empty($exam)) {
                    $exam_id = $exam['id'];
                } else {
                    $exam_id = $this->examination_model->gen_exam_data($user_id, $type_id);

                }
        }
        $check = $this->examination_model->check_user_exam($exam_id,$user_id);
        if($check == -1){
            redirect(site_url('/examination/self_examination'));
        }
       // die(var_dump($this->input->post('exam_id')));
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

    public function submit_examination($exam_id,$num=1)
    {
        $exam_data = $this->examination_model->get_exam_by_num($exam_id, $num);
        $this->assign('exam_data', $exam_data);
        $question_true = $this->examination_model->get_true_exam_question($exam_id);
        $this->assign('question_true', $question_true);
        $this->assign('exam_id', $exam_id);
        $this->assign('num', $num);
        $this->display('submit_examination.html');
    }

    public function unit_examination()
    {

        $exam_list = $this->examination_model->get_exam_list();
        $this->assign('exam_list', $exam_list);
        $this->display('unit_examination.html');
    }

    public function chenge_option($eq_id,$val,$as){
        $this->examination_model->chenge_option($eq_id,$val,$as);
    }

    public function enter_examination($information=null){
        $this->assign('information', $information?$information:-1);
        $type_list = $this->examination_model->get_type();
        $this->assign('type_list', $type_list);
        $this->display("entering_examination.html");
    }

    public function review_examination(){
        $exam_data = $this->examination_model->get_exam_data();
        $this->assign('exam_data', $exam_data);
        $this->display("review_examination.html");
    }
    public function setup_examination(){
        $res = $this->examination_model->get_news_exam_id();
        if($res == -1){
            $this->display("setup_examination1.html");
        }else{
            redirect(site_url('/examination/choose_list/1/1'));
        }

    }

    public function choose_items(){
        $res = $this->examination_model->save_exam_main();
        if($res != -1){
            redirect(site_url('/examination/choose_list/1/1'));
        }else{
            redirect(site_url('/examination/setup_examination'));
        }
       // $this->display("setup_examination2.html");

    }

    public function choose_list($page=1,$type=null){
        if(!$type){
            $type = $this->input->post('type');
        }
        $this->assign('type', $type);
        $type_list = $this->examination_model->get_type();
        $this->assign('type_list', $type_list);
        $exam_data = $this->examination_model->get_exam_data();
        $this->assign('exam_data', $exam_data);
        $question_data = $this->examination_model->list_question($page,$type);
        $this->assign('question_data', $question_data);
        $pager = $this->pagination->getPageLink('/examination/choose_list', $question_data['countPage'], $question_data['numPerPage']);
        $this->assign('pager', $pager);

        $this->display("setup_examination2.html");
    }
    public function examination_score(){
        $score_list = $this->examination_model->get_my_score_list();
        //die(var_dump($score_list));
        $this->assign('score_list', $score_list);
        $this->display("examination_score.html");
    }
    public function examination_list(){
        $exam_list = $this->examination_model->get_my_exam_list();
        $this->assign('exam_list', $exam_list);
        $this->display("examination_list.html");
    }

    public function save_question(){
      $res = $this->examination_model->save_question();
        if($res){
            redirect(site_url('/examination/enter_examination/1'));
        }else{
            redirect(site_url('/examination/enter_examination/2'));
        }

    }

    public function add_question($id){
        $res = $this->examination_model->add_question($id);
        echo json_encode($res);
    }

    public function delete_question($id){
        $res = $this->examination_model->delete_question($id);
        echo json_encode($res);
    }

    public function change_exam_flag(){
        $this->examination_model->change_exam_flag();
        redirect(site_url('/examination/examination_list'));
    }

    public function view_examination($exam_id) {
        $exam_data = $this->examination_model->view_examination($exam_id);
        $this->assign('exam_data', $exam_data);
        $this->display("view_examination.html");
    }
}