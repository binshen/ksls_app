<?php
/**
 * Created by PhpStorm.
 * User: bin.shen
 * Date: 6/2/16
 * Time: 21:22
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class News extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('news_model');
        $this->load->library('image_lib');
        $this->load->helper('directory');
    }

    public function publish_news($id=null){
        $news = array();
        if($id){
            $news = $this->news_model->view_news($id);
        }
        $this->assign('news', $news);
        $this->display("publish_news.html");
    }
    
    public function view_news($id) {
        $this->news_model->increase_views($id);
        $news = $this->news_model->view_news($id);
        $this->assign('news', $news);
        $this->display("popup_news.html");
    }

    public function save_news() {
        if (is_readable('./././uploadfiles/news') == false) {
            mkdir('./././uploadfiles/news');
        }
        $config['upload_path'] = './././uploadfiles/news';
        $config['allowed_types'] = 'gif|jpg|png|jpeg';
        $config['max_size'] = '1000';
        $config['encrypt_name'] = true;
        if($this->input->post('news_id')){
            if(!$_FILES["userfile"]['tmp_name']){
                $this->news_model->update_user();
            }else{
                $this->load->library('upload', $config);
                if($this->upload->do_upload()){
                    $img_info = $this->upload->data();
                    $this->news_model->update_user($img_info['file_name']);
                }
            }
        }else{
            $this->load->library('upload', $config);
            if($this->upload->do_upload()){
                $img_info = $this->upload->data();
                $this->news_model->save_user($img_info['file_name']);
            }
        }
        redirect('/');
    }

    public function upload_news_pic(){
        if (is_readable('./././uploadfiles/news_pic') == false) {
            mkdir('./././uploadfiles/news_pic');
        }
        $path = './././uploadfiles/news_pic/';
        $path_out = '/uploadfiles/news_pic/';
        $msg = '';

        //设置原图限制
        $config['upload_path'] = $path;
        $config['allowed_types'] = 'gif|jpg|png|jpeg';
        $config['max_size'] = '1000';
        $config['encrypt_name'] = true;
        $this->load->library('upload', $config);

        if($this->upload->do_upload('filedata')){
            $data = $this->upload->data();
            $targetPath = $path_out.$data['file_name'];
            $msg="{'url':'".$targetPath."','localname':'','id':'1'}";
            $err = '';
        }else{
            $err = $this->upload->display_errors();
        }
        echo "{'err':'".$err."','msg':".$msg."}";
    }
}