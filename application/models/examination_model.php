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
            'complete' => 0))->result_array();
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
            return 1;
        }
    }
}