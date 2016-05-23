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

        $this->load->model('agenda_model');

        $this->load->library('image_lib');
        $this->load->helper('directory');
    }

    function _remap($method,$params = array()) {
        if(!$this->session->userdata('login_user_id')) {
            redirect(site_url('/'));
        } else {
            return call_user_func_array(array($this, $method), $params);
        }
    }

    public function list_agenda($page=1) {
        $course_list = $this->agenda_model->get_course();
        $this->assign('course_list', $course_list);
        if($this->input->POST('status')) {
            $this->assign('status', $this->input->POST('status'));
        }
        if($this->input->POST('course')) {
            $this->assign('course', $this->input->POST('course'));
        }
        $data = $this->agenda_model->list_agenda($page, $this->session->userdata('login_user_id'));
        $this->assign('agenda_list', $data);

        $pager = $this->pagination->getPageLink('/agenda/list_agenda', $data['countPage'], $data['numPerPage']);
        $this->assign('pager', $pager);
        $this->display('list_agenda.html');
    }

    function list_agenda_other($page=1){
        $role_id = $this->session->userdata('login_role_id');
        $this->assign('role_id', $role_id);
        if($role_id == 1) {
            $company_list = $this->agenda_model->get_company_list();
            $this->assign('company_list', $company_list);
        }

        if($this->input->POST('company')) {
            $this->assign('company', $this->input->POST('company'));
            $subsidiary_list = $this->agenda_model->get_subsidiary_list($this->input->POST('company'), NULL);
        } else {
            $company_id = $this->session->userdata('login_company_id');
            if($role_id < 4) {
                $subsidiary_list = $this->agenda_model->get_subsidiary_list($company_id, NULL);
            } else if($role_id == 4) {
                $subsidiary_id = $this->session->userdata('login_subsidiary_id');
                $subsidiary_list = $this->agenda_model->get_subsidiary_list($company_id, $subsidiary_id);
            }
        }
        $this->assign('subsidiary_list', $subsidiary_list);
        if($this->input->POST('subsidiary')) {
            $this->assign('subsidiary', $this->input->POST('subsidiary'));

            $user_list = $this->agenda_model->get_subsidiary_user_list($this->input->POST('subsidiary'));
            $this->assign('user_list', $user_list);
        }
        if($this->input->POST('user')) {
            $this->assign('user', $this->input->POST('user'));
        }

        $company_id = NULL;
        if($role_id > 1) {
            $company_id = $this->session->userdata('login_company_id');
        }
        $subsidiary_id = NULL;
        if($role_id >= 4) {
            $subsidiary_id = $this->session->userdata('login_subsidiary_id');
        }

        $course_list = $this->agenda_model->get_course();
        $this->assign('course_list',$course_list);
        if($this->input->POST('status')) {
            $this->assign('status', $this->input->POST('status'));
        }
        if($this->input->POST('course')) {
            $this->assign('course', $this->input->POST('course'));
        }
        $data = $this->agenda_model->list_agenda($page,null,$subsidiary_id,$company_id);
        $this->assign('agenda_list', $data);

        $pager = $this->pagination->getPageLink('/agenda/list_agenda_other', $data['countPage'], $data['numPerPage']);
        $this->assign('pager', $pager);
        $this->display('list_agenda_other.html');
    }

    public function add_agenda($id=NULL) {

        if(!empty($id)) {
            $agenda = $this->agenda_model->get_agenda($id);
            $this->assign('agenda', $agenda);

            $agenda_image = $this->agenda_model->get_agenda_image($id);
            $agenda_images = array();
            foreach ($agenda_image as $img) {
                $agenda_images[$img->style][] = $img;
            }
            $this->assign('agenda_images', $agenda_images);
        }

        $this->assign('time', date('YmdHis'));
        $this->display('add_agenda.html');
    }

    public function view_agenda($id) {

        $agenda = $this->agenda_model->get_agenda($id);
        $this->assign('agenda', $agenda);

        $agenda_course = $this->agenda_model->get_agenda_course($id);
        $this->assign('agenda_course', $agenda_course);


        $agenda_image = $this->agenda_model->get_agenda_image($id);
        $agenda_images = array();
        foreach ($agenda_image as $img) {
            $agenda_images[$img->style][] = $img;
        }
        $this->assign('agenda_images', $agenda_images);

        $course_list = $this->agenda_model->get_course_list();
        $this->assign('course_list', $course_list);

        $this->display('view_agenda.html');
    }

    public function save_agenda() {

        $this->agenda_model->save_agenda();

        redirect(site_url('agenda/list_agenda'));
    }


    ///////////////////////////////////////////////////////////////////
    public function save_pics($time, $style){
        if (is_readable('./././uploadfiles/agenda') == false) {
            mkdir('./././uploadfiles/agenda');
        }
        if (is_readable('./././uploadfiles/agenda/'.$time) == false) {
            mkdir('./././uploadfiles/agenda/'.$time);
        }

        if (is_readable('./././uploadfiles/agenda/'.$time.'/'.$style) == false) {
            mkdir('./././uploadfiles/agenda/'.$time.'/'.$style);
        }

        $path = './././uploadfiles/agenda/'.$time.'/'.$style;

        //设置缩小图片属性
        $config_small['image_library'] = 'gd2';
        $config_small['create_thumb'] = TRUE;
        $config_small['quality'] = 80;
        $config_small['maintain_ratio'] = TRUE; //保持图片比例
        $config_small['new_image'] = $path;
        $config_small['width'] = 300;
        $config_small['height'] = 190;

        //设置原图限制
        $config['upload_path'] = $path;
        $config['allowed_types'] = 'gif|jpg|png|jpeg';
        $config['max_size'] = '10000';
        $config['encrypt_name'] = true;
        $this->load->library('upload', $config);

        if($this->upload->do_upload()){
            $data = $this->upload->data();//返回上传文件的所有相关信息的数组
            $config_small['source_image'] = $data['full_path']; //文件路径带文件名
            $this->image_lib->initialize($config_small);
            $this->image_lib->resize();

            echo 1;
        }else{
            echo -1;
        }
        exit;
    }

    //ajax获取图片信息
    public function get_pics($time, $style){
        $path = './././uploadfiles/agenda/'.$time.'/'.$style;
        $map = directory_map($path);
        $data = array();
        //整理图片名字，取缩略图片
        foreach($map as $v){
            if(substr(substr($v,0,strrpos($v,'.')),-5) == 'thumb'){
                $data['img'][] = $v;
            }
        }
        $data['time'] = $time;
        $data['style'] = $style;
        echo json_encode($data);
    }

    //ajax删除图片
    public function del_pic($folder,$style,$pic,$id=null){
        $data = $this->agenda_model->del_pic($folder,$style,$pic,$id);
        echo json_encode($data);
    }
}