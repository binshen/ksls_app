<?php
/**
 * Created by PhpStorm.
 * User: bin.shen
 * Date: 6/8/16
 * Time: 17:07
 */

class Examination_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function __destruct()
    {
        parent::__destruct();
    }

    public function get_question_type_list()
    {
        return $this->db->get('question_type')->result_array();
    }

    public function get_user_exam($user_id, $type_id)
    {
        return $this->db->get_where('self_exam', array(
            'user_id' => $user_id,
            'type_id' => $type_id,
            'complete' => 0))->row_array();
    }

    public function gen_exam_data($user_id, $type_id, $limit=20) {
        $this->db->trans_start();//--------开始事务

        $this->db->from('question');
        $this->db->where('type_id', $type_id);
        $this->db->order_by('RAND()');
        $this->db->limit($limit);
        $questions = $this->db->get()->result_array();
        $exam_data = array();
        if(!empty($questions)) {
            $data = array(
                'user_id' => $user_id,
                'type_id' => $type_id,
                'title' => $user_id . '-自助考试-' . date('YmdHis'),
                'complete' => 0,
                'created' => date("Y-m-d H:i:s")
            );
            $this->db->insert('self_exam', $data);
            $exam_id = $this->db->insert_id();

            foreach ($questions as $q) {
                $exam_data[] = array(
                    'exam_id' => $exam_id,
                    'question_id' => $q['id'],
                    'complete' => 0
                );
            }
            $this->db->insert_batch('self_exam_question', $exam_data);
        }

        $this->db->trans_complete();//------结束事务
        if ($this->db->trans_status() === FALSE) {
            return -1;
        } else {
            return $exam_id;
        }
    }

    public function get_exam_by_num($exam_id, $num) {
        $this->db->select('c.title, c.op1, c.op2, c.op3, c.op4, c.type_id, d.name AS question_type, b.as1, b.as2, b.as3, b.as4, b.id AS eq_id');
        $this->db->from('self_exam a');
        $this->db->join('self_exam_question b', 'a.id = b.exam_id', 'inner');
        $this->db->join('question c', 'b.question_id = c.id', 'inner');
        $this->db->join('question_type d', 'c.type_id = d.id', 'inner');
        $this->db->where('a.id', $exam_id);
        $this->db->order_by('b.id ASC');
        $this->db->limit(1);
        $this->db->offset($num-1);
        return $this->db->get()->row_array();
    }

    public function get_exam_question($exam_id) {
        return $this->db->get_where('self_exam_question', array('exam_id' => $exam_id))->result_array();
    }

    public function take_exam($eq_id, $data) {
        $this->db->trans_start();//--------开始事务

        $this->db->where('id', $eq_id);
        $this->db->update('self_exam_question', $data);

        $this->db->trans_complete();//------结束事务
        if ($this->db->trans_status() === FALSE) {
            return -1;
        } else {
            return 1;
        }
    }

    public function complete_examination($exam_id) {
        $this->db->trans_start();//--------开始事务

        $this->db->where('id', $exam_id);
        $this->db->update('self_exam', array('complete' => 1));

        $this->db->trans_complete();//------结束事务
        if ($this->db->trans_status() === FALSE) {
            return -1;
        } else {
            return 1;
        }
    }

    public function get_true_exam_question($exam_id) {
        $this->db->select('a.question_id,c.as1,c.as2,c.as3,c.as4,
        a.as1 self_as1,a.as2 self_as2,a.as3 self_as3,a.as4 self_as4');
        $this->db->from('self_exam_question a');
        $this->db->join('question c', 'a.question_id = c.id', 'inner');
        $this->db->where('a.exam_id',$exam_id);
        return $this->db->get()->result_array();
    }

    public function get_sub_exam_list($exam_id, $question_id = NULL) {
        $this->db->select('a.complete,b.id self_id,c.title, c.op1, c.op2, c.op3, c.op4,
        c.as1,c.as2,c.as3,c.as4,
        b.as1 self_as1,b.as2 self_as2,b.as3 self_as3,b.as4 self_as4,
        d.name AS question_type');
        $this->db->from('self_exam a');
        $this->db->join('self_exam_question b', 'a.id = b.exam_id', 'inner');
        $this->db->join('question c', 'b.question_id = c.id', 'inner');
        $this->db->join('question_type d', 'c.type_id = d.id', 'inner');
        $this->db->where('a.id', $exam_id);
        if(!empty($question_id)) {
            $this->db->where('c.id', $question_id);
        }
        $this->db->order_by('b.id ASC');
        $this->db->limit(1);
        $data['question_detail'] = $this->db->get()->row_array();
       // echo $this->db->last_query();
       //die(var_dump($data['question_detail']));
        $this->db->select('count(1) as num');
        $this->db->from('self_exam_question a');
        $this->db->where('a.exam_id',$exam_id);
        $this->db->where('a.id <=', $data['question_detail']['self_id']);
        $row = $this->db->get()->row_array();
        $this->db->select('count(1) as num');
        $this->db->from('self_exam_question a');
        $this->db->where('a.exam_id',$exam_id);
        $row_count = $this->db->get()->row_array();
        $data['No_question'] = $row['num'];
        $data['count'] = $row_count['num'];
        return $data;
    }

    public function chenge_option($eq_id,$val,$as){
        $data = array('as1' => 0, 'as2' => 0, 'as3' => 0, 'as4' => 0, 'complete' => 0);
        if($val == 'A') {
            $data['as1'] = $as;
            $data['complete'] = 1;
        } else if($val == 'B') {
            $data['as2'] = $as;
            $data['complete'] = 1;
        } else if($val == 'C') {
            $data['as3'] = $as;
            $data['complete'] = 1;
        } else if($val == 'D') {
            $data['as4'] = $as;
            $data['complete'] = 1;
        }
        $this->db->where('id', $eq_id);
        $this->db->update('self_exam_question', $data);
    }

    public function get_type(){
        $this->db->from('question_type');
        $data = $this->db->get()->result_array();
        if($data){
            return $data;
        }else{
            return 1;
        }

    }

    public function save_question(){
        $data = array(
            'type_id' => $this->input->post('type_id'),
            'style' => $this->input->post('style'),
            'title' => $this->input->post('title'),
            'op1' => $this->input->post('op1'),
            'op2' => $this->input->post('op2'),
            'op3' => $this->input->post('op3'),
            'op4' => $this->input->post('op4'),
            'as1' => $this->input->post('as1')?$this->input->post('as1'):0,
            'as2' => $this->input->post('as2')?$this->input->post('as2'):0,
            'as3' => $this->input->post('as3')?$this->input->post('as3'):0,
            'as4' => $this->input->post('as4')?$this->input->post('as4'):0
        );
        $this->db->insert('question',$data);
        var_dump($this->db->last_query());
    }

    public function get_my_score_list() {
        $user_id = $this->session->userdata('login_user_id');
        //TODO: 除了自测试卷的分数,还有参加过的所有统一考试的试卷
        return $this->db->where('user_id', $user_id)->order_by('id', 'desc')->get('self_exam')->result_array();
    }

    public function get_my_exam_list() {
        $user_id = $this->session->userdata('login_user_id');
        return $this->db->where('user_id', $user_id)->order_by('id', 'desc')->get('exam')->result_array();
    }
}