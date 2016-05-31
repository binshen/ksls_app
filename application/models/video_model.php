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

    public function get_video_list($type_id) {
        return $this->db->get_where('video', array('type_id' => $type_id))->order_by('created', 'desc')->result_array();
    }
}