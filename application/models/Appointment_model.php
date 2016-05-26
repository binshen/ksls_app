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
              e.user_id
            FROM room a
            LEFT JOIN appointment e 
            ON a.id = e.room_id
            AND e.date = '$date'
            AND e.time_frame_id = $tf_id
            WHERE a.open = 1
        ";
        return $this->db->query($sql)->result();
    }

    public function get_appointment_detail($date, $tf_id) {
        $sql = "
            SELECT 
              a.*, 
              b.rel_name AS user_name,
              b.tel,
              c.name AS company_name,
              d.name AS subsidiary_name, 
              e.name AS room_name
            FROM appointment a
            JOIN user b ON a.user_id = b.id
            LEFT JOIN company c ON b.company_id = c.id
            LEFT JOIN subsidiary d ON b.subsidiary_id = d.id
            LEFT JOIN room e ON a.room_id = e.id
            WHERE a.date = '$date' 
            AND a.time_frame_id = $tf_id
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