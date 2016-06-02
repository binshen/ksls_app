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
}