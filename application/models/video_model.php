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

    public function get_video_list($page, $type_id=NULL) {
        // 每页显示的记录条数，默认20条
        $numPerPage = $this->input->post('numPerPage') ? $this->input->post('numPerPage') : 10;
        $pageNum = $this->input->post('pageNum') ? $this->input->post('pageNum') : $page;

        //获得总记录数
        $this->db->select('count(1) as num');
        $this->db->from('video');
        if(!empty($type_id)) {
            $this->db->where('type_id', $type_id);
        }
        $rs_total = $this->db->get()->row();
        //总记录数
        $data['countPage'] = $rs_total->num;

        //list
        $this->db->select('*');
        $this->db->from('video');
        if(!empty($type_id)) {
            $this->db->where('type_id', $type_id);
        }

        $this->db->limit($numPerPage, ($pageNum - 1) * $numPerPage );
        $this->db->order_by('created', 'desc');
        $data['res_list'] = $this->db->get()->result_array();
        //die(var_dump($this->db->last_query()));
        $data['pageNum'] = $pageNum;
        $data['numPerPage'] = $numPerPage;
        return $data;
    }
}