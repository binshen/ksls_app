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
        $this->load->model('video_model');
    }

    public function list_video() {

        $video_type_list = $this->video_model->get_video_type_list();
        $this->assign('video_type_list', $video_type_list);

        $top_video_list = $this->video_model->get_top_video_list();
        $this->assign('top_video_list', $top_video_list);



        $this->display('online_class.html');
    }

    public function view_video($id) {

        $video = $this->video_model->get_video($id);
        $this->assign('video', $video);

        $like_count = $this->video_model->get_like_count($id);
        $this->assign('like_count', $like_count[0]->count);

        $related_video_list = $this->video_model->get_related_video_list($video['type_id']);
        $this->assign('related_video_list', $related_video_list);

        $this->display('video_play.html');
    }
}