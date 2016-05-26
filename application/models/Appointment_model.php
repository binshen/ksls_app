<?php
/**
 * Created by PhpStorm.
 * User: bin.shen
 * Date: 5/26/16
 * Time: 12:59
 */

class Appointment_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function __destruct()
    {
        parent::__destruct();
    }

    public function get_time_frame_list() {
        return $this->db->get('time_frame')->result();
    }

    public function get_room_list() {
        return $this->db->get('room')->result();
    }

    public function book_room($start, $end, $user_id = NULL) {
        $sql = "
            SELECT * FROM appointment WHERE date >= '$start' AND date <= '$end'
        ";
        if(!empty($user_id)) {
            $sql .= " AND user_id = $user_id";
        }
        return $this->db->query($sql)->result();
    }

    public function get_appointment_info($date, $tf_id) {
        $sql = "
            SELECT 
              a.id, 
              a.name AS room_name,
              b.user_id
            FROM room a
            LEFT JOIN appointment b 
            ON a.id = b.room_id
            AND b.date = '$date'
            AND b.time_frame_id = $tf_id
            WHERE a.open = 1
        ";
        return $this->db->query($sql)->result();
    }

    public function get_appointment_detail($date, $tf_id) {
        $sql = "
            SELECT
              a.name AS room_name,
              a.clz,
              b.date,
              c.rel_name AS user_name,
              c.tel,
              d.name AS company_name,
              e.name AS subsidiary_name
            FROM room a
            LEFT JOIN appointment b ON a.id = b.room_id AND b.date = '$date' AND b.time_frame_id = $tf_id
            LEFT JOIN user c ON b.user_id = c.id
            LEFT JOIN company d ON c.company_id = d.id
            LEFT JOIN subsidiary e ON c.subsidiary_id = e.id
            WHERE a.open = 1
        ";
        return $this->db->query($sql)->result();
    }

    public function save_appointment() {

        $this->db->where('date', $this->input->post('date'));
        $this->db->where('time_frame_id', $this->input->post('time_frame_id'));
        $this->db->where('room_id', $this->input->post('room_id'));
        $data = $this->db->get('appointment')->row();
        if(!empty($data)) {
            return -2;
        }
        $data = array(
            'user_id' => $this->input->post('user_id'),
            'date' => $this->input->post('date'),
            'time_frame_id' => $this->input->post('time_frame_id'),
            'room_id' => $this->input->post('room_id')
        );
        $this->db->trans_start();//--------开始事务
        $this->db->insert('appointment', $data);
        $this->db->trans_complete();//------结束事务

        if ($this->db->trans_status() === FALSE) {
            return -1;
        } else {
            return 1;
        }
    }

    public function check_room($date, $user_id) {
        $this->db->where('date', $date);
        $this->db->where('user_id', $user_id);
        return $this->db->get('appointment')->result();
    }

    public function unbook_room($date, $tf_id, $user_id) {
        $this->db->where('user_id', $user_id);
        $this->db->where('date', $date);
        $this->db->where('time_frame_id', $tf_id);
        return $this->db->delete('appointment');
    }
}