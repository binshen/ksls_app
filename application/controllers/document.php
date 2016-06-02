<?php
/**
 * Created by PhpStorm.
 * User: bin.shen
 * Date: 5/31/16
 * Time: 16:23
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Document extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('document_model');
    }

    function _remap($method,$params = array()) {
        if(!$this->session->userdata('login_user_id')) {
            redirect(site_url('/'));
        } else {
            return call_user_func_array(array($this, $method), $params);
        }
    }

    //$typeid 除了用来判断首次进入页面时需要保留的 资料类别外,-1表示我的收藏,-2表示我的发布
    public function list_doc($page=1,$typeid=null) {
        $type = $this->document_model->get_forum_type();
        $data = $this->document_model->list_doc($page,$typeid);
        $this->assign('typeid', $typeid ? $typeid : $this->input->post('type'));
        $type_name = $this->document_model->get_type_name($typeid ? $typeid : $this->input->post('type'));
        $this->assign('type_name', $type_name);
        $this->assign('title', $this->input->post('title') ? $this->input->post('title') : null);
        $this->assign('list_doc', $data);
        $pager = $this->pagination->getPageLink('/document/list_doc', $data['countPage'], $data['numPerPage']);
        $this->assign('pager', $pager);
        $this->assign('type_list', $type);
        $this->display('online_doc.html');
    }

    public function view_doc($id) {
        $data = $this->document_model->view_doc($id);
        if($data['type']==6){
            $this->document_model->download($id);
        }else{
            $recommend = $this->document_model->recomment_doc();  //推荐的文档/
            $house_likes = $this->document_model->house_likes($id);
            $this->assign('recommend', $recommend);
            $this->assign('house_likes', $house_likes);
            $this->assign('data', $data);
            $this->display('doc_view.html');
        }

    }

    public function publish_doc() {
        $type = $this->document_model->get_forum_type();
        $this->assign('type_list', $type);
        $this->display('publish_doc.html');
    }

    public function upload_data(){

        $this->display('upload_data.html');
    }

    public function save_ticket(){
        $res = $this->document_model->save_ticket();
        redirect(site_url('document/list_doc/1/-2'));
    }

    public function likes_one_time($id){
        $res = $this->document_model->likes_doc_one_time($id);
        echo json_encode($res);
    }

    public function house_one_time($id){
        $res = $this->document_model->house_doc_one_time($id);
        echo json_encode($res);
    }

    public function save_file(){
        $res = $this->document_model->save_file();
        if($res == 3){
            $type = $this->document_model->get_forum_type();
            $this->assign('type_list', $type);
            $this->assign('title', $this->input->post('title'));
            $this->assign('type', $this->input->post('type'));
            $this->display('upload_data.html');
        }
        else{
            redirect(site_url('document/list_doc/1/-2'));
        }

    }
}