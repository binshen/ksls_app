<?php
/**
 * Created by PhpStorm.
 * User: bin.shen
 * Date: 5/31/16
 * Time: 23:00
 */

class Video_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function __destruct()
    {
        parent::__destruct();
    }

    public function get_video_type_list() {
        return $this->db->get('video_type')->result_array();
    }

    public function get_top_video_list() {
        $this->db->select('a.*, b.name as type_name');
        $this->db->from('video a');
        $this->db->join('video_type b', 'a.type_id = b.id', 'inner');
        $this->db->where('a.is_top', 1);
        $this->db->order_by('a.created', 'desc');
        $this->db->limit(3);
        $this->db->distinct();
        return $this->db->get('video')->result_array();
    }

    public function get_video($id) {
        $this->db->select('a.*, b.name as type_name');
        $this->db->from('video a');
        $this->db->join('video_type b', 'a.type_id = b.id', 'inner');
        $this->db->where('a.id', $id);
        $this->db->order_by('a.created', 'desc');
        $this->db->distinct();
        return $this->db->get('video')->row_array();
    }

    public function get_like_count($id) {
        return $this->db->select("count(1) AS count")->get_where('video_like', array('video_id' => $id))->result();
    }

    public function get_related_video_list($type_id) {
        return $this->db->order_by('is_top', 'desc')->order_by('created', 'desc')->limit(5)->get_where('video', array('type_id' => $type_id))->result_array();
    }

    public function get_video_list($type_id) {
        return $this->db->order_by('created', 'desc')->get_where('video', array('type_id' => $type_id))->result_array();
    }
}