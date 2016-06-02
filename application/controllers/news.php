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
    }

    public function publish_news(){
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
        $this->load->library('upload', $config);
        if($this->upload->do_upload()){
            $img_info = $this->upload->data();
            $this->news_model->save_user($img_info['file_name']);
        }
        redirect('/');
    }
}