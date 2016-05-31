<?php
if (! defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * 网站后台模型
 *
 * @package		app
 * @subpackage	core
 * @category	model
 * @author		yaobin<645894453@qq.com>
 *        
 */
class Manage_model extends MY_Model
{
    public function __construct ()
    {
        parent::__construct();
    }

    public function __destruct ()
    {
        parent::__destruct();
    }
    
    /**
     * 用户登录检查
     * 
     * @return boolean
     */
    public function check_login ()
    {
        $username = $this->input->post('username');
        $password = $this->input->post('password');
        $this->db->from('user');
        $this->db->where('username', $username);
        $this->db->where('password', sha1($password));
        $this->db->where('role_id <= 4');
        $rs = $this->db->get();
        if ($rs->num_rows() > 0) {
        	$res = $rs->row();
        	$user_info['user_id'] = $res->id;
            $user_info['username'] = $username;
            $user_info['rel_name'] = $res->rel_name;
            $user_info['role_id'] = $res->role_id;
            $user_info['company_id'] = $res->company_id;
            $user_info['subsidiary_id'] = $res->subsidiary_id;
            $this->session->set_userdata($user_info);
            return true;
        }
        return false;
    }
    
    /**
     * 修改密码
     * 
     */
    public function change_pwd ()
    {
        $username = $this->input->post('username');
        $newpassword = $this->input->post('newpassword');
        
		$rs=$this->db->where('username', $username)->update('user', array('password'=>sha1($newpassword)));
        if ($rs) {
            return 1;
        } else {
            return $rs;
        }
    }

    /**
     * 公司信息
     */
    public function list_company(){
        // 每页显示的记录条数，默认20条
        $numPerPage = $this->input->post('numPerPage') ? $this->input->post('numPerPage') : 20;
        $pageNum = $this->input->post('pageNum') ? $this->input->post('pageNum') : 1;

        //获得总记录数
        $this->db->select('count(1) as num');
        $this->db->from('company');
        if($this->session->userdata('role_id') > 1) {
            $this->db->where('id', $this->session->userdata('company_id'));
        }

        $rs_total = $this->db->get()->row();
        //总记录数
        $data['countPage'] = $rs_total->num;

        //list
        $this->db->select('*')->from('company');
        if($this->session->userdata('role_id') > 1) {
            $this->db->where('id', $this->session->userdata('company_id'));
        }
        $this->db->limit($numPerPage, ($pageNum - 1) * $numPerPage );
        $this->db->order_by($this->input->post('orderField') ? $this->input->post('orderField') : 'id', $this->input->post('orderDirection') ? $this->input->post('orderDirection') : 'desc');
        $data['res_list'] = $this->db->get()->result();
        $data['pageNum'] = $pageNum;
        $data['numPerPage'] = $numPerPage;
        return $data;
    }

    public function save_company() {
        $data = array(
            'name' => $this->input->post('name'),
            'address' => $this->input->post('address'),
            'tel' => $this->input->post('tel')
        );
        $this->db->trans_start();//--------开始事务

        if($this->input->post('id')){//修改
            $this->db->where('id', $this->input->post('id'));
            $this->db->update('company', $data);
        } else {
            $this->db->insert('company', $data);
        }
        $this->db->trans_complete();//------结束事务
        if ($this->db->trans_status() === FALSE) {
            return -1;
        } else {
            return 1;
        }
    }

    public function get_company($id) {
        return $this->db->get_where('company', array('id' => $id))->row_array();
    }

    public function delete_company($id) {
        $this->db->where('id', $id);
        return $this->db->delete('company');
    }

    /**
     * 分店信息
     */
    public function list_subsidiary(){
        // 每页显示的记录条数，默认20条
        $numPerPage = $this->input->post('numPerPage') ? $this->input->post('numPerPage') : 20;
        $pageNum = $this->input->post('pageNum') ? $this->input->post('pageNum') : 1;

        //获得总记录数
        $this->db->select('count(1) as num');
        $this->db->from('subsidiary');
        if($this->session->userdata('role_id') == 2) {
            $this->db->where('company_id', $this->session->userdata('company_id'));
        } else if($this->session->userdata('role_id') > 2) {
            $this->db->where('id', $this->session->userdata('subsidiary_id'));
        }

        $rs_total = $this->db->get()->row();
        //总记录数
        $data['countPage'] = $rs_total->num;
        $data['company_id'] = null;

        //list
        $this->db->select('a.*, b.name AS company_name');
        $this->db->from('subsidiary a');
        $this->db->join('company b', 'a.company_id = b.id', 'left');
        if($this->session->userdata('role_id') == 2) {
            $this->db->where('a.company_id', $this->session->userdata('company_id'));
        } else if($this->session->userdata('role_id') > 2) {
            $this->db->where('a.id', $this->session->userdata('subsidiary_id'));
        }

        $this->db->limit($numPerPage, ($pageNum - 1) * $numPerPage );
        $this->db->order_by($this->input->post('orderField') ? $this->input->post('orderField') : 'a.id', $this->input->post('orderDirection') ? $this->input->post('orderDirection') : 'desc');
        $data['res_list'] = $this->db->get()->result();
        $data['pageNum'] = $pageNum;
        $data['numPerPage'] = $numPerPage;
        return $data;
    }

    public function save_subsidiary() {

        $data = array(
            'company_id' => $this->input->post('company_id'),
            'name' => $this->input->post('name')
        );

        $this->db->trans_start();//--------开始事务

        if($this->input->post('id')){//修改
            $this->db->where('id', $this->input->post('id'));
            $this->db->update('subsidiary', $data);
        } else {
            $this->db->insert('subsidiary', $data);
        }
        $this->db->trans_complete();//------结束事务
        if ($this->db->trans_status() === FALSE) {
            return -1;
        } else {
            return 1;
        }
    }

    public function get_subsidiary($id) {
        return $this->db->get_where('subsidiary', array('id' => $id))->row_array();
    }

    public function delete_subsidiary($id) {
        $this->db->where('id', $id);
        return $this->db->delete('subsidiary');
    }

    public function get_company_list() {
        if($this->session->userdata('role_id') == 1) {
            return $this->db->get('company')->result();
        } else {
            return $this->db->get_where('company', array('id' => $this->session->userdata('company_id')))->result();
        }
    }

    public function get_subsidiary_list_by_company($id) {
        if($this->session->userdata('role_id') < 6) {
            return $this->db->get_where('subsidiary', array('company_id' => $id))->result_array();
        } else {
            return $this->db->get_where('subsidiary', array('company_id' => $id, 'id' => $this->session->userdata('subsidiary_id')))->result_array();
        }
    }

    /**
     * 角色信息
     */
    public function list_role(){
        // 每页显示的记录条数，默认20条
        $numPerPage = $this->input->post('numPerPage') ? $this->input->post('numPerPage') : 20;
        $pageNum = $this->input->post('pageNum') ? $this->input->post('pageNum') : 1;

        //获得总记录数
        $this->db->select('count(1) as num');
        $this->db->from('role');

        $rs_total = $this->db->get()->row();
        //总记录数
        $data['countPage'] = $rs_total->num;

        //list
        $this->db->select('*')->from('role');
        $this->db->limit($numPerPage, ($pageNum - 1) * $numPerPage );
        $this->db->order_by($this->input->post('orderField') ? $this->input->post('orderField') : 'id', $this->input->post('orderDirection') ? $this->input->post('orderDirection') : 'desc');
        $data['res_list'] = $this->db->get()->result();
        $data['pageNum'] = $pageNum;
        $data['numPerPage'] = $numPerPage;
        return $data;
    }

    public function save_role() {
        $data = array(
            'name' => $this->input->post('name')
        );
        $this->db->trans_start();//--------开始事务

        if($this->input->post('id')){//修改
            $this->db->where('id', $this->input->post('id'));
            $this->db->update('role', $data);
        } else {
            $this->db->insert('role', $data);
        }
        $this->db->trans_complete();//------结束事务
        if ($this->db->trans_status() === FALSE) {
            return -1;
        } else {
            return 1;
        }
    }

    public function get_role($id) {
        return $this->db->get_where('role', array('id' => $id))->row_array();
    }

    public function delete_role($id) {
        $this->db->where('id', $id);
        return $this->db->delete('role');
    }

    /**
     * 行程选项
     */
    public function list_activity_type(){
        // 每页显示的记录条数，默认20条
        $numPerPage = $this->input->post('numPerPage') ? $this->input->post('numPerPage') : 20;
        $pageNum = $this->input->post('pageNum') ? $this->input->post('pageNum') : 1;

        //获得总记录数
        $this->db->select('count(1) as num');
        $this->db->from('activity_type');

        $rs_total = $this->db->get()->row();
        //总记录数
        $data['countPage'] = $rs_total->num;

        //list
        $this->db->select('*')->from('activity_type');
        $this->db->limit($numPerPage, ($pageNum - 1) * $numPerPage );
        $this->db->order_by($this->input->post('orderField') ? $this->input->post('orderField') : 'id', $this->input->post('orderDirection') ? $this->input->post('orderDirection') : 'desc');
        $data['res_list'] = $this->db->get()->result();
        $data['pageNum'] = $pageNum;
        $data['numPerPage'] = $numPerPage;
        return $data;
    }

    public function save_activity_type() {
        $data = array(
            'name' => $this->input->post('name')
        );
        $this->db->trans_start();//--------开始事务

        if($this->input->post('id')){//修改
            $this->db->where('id', $this->input->post('id'));
            $this->db->update('activity_type', $data);
        } else {
            $this->db->insert('activity_type', $data);
        }
        $this->db->trans_complete();//------结束事务
        if ($this->db->trans_status() === FALSE) {
            return -1;
        } else {
            return 1;
        }
    }

    public function get_activity_type($id) {
        return $this->db->get_where('activity_type', array('id' => $id))->row_array();
    }

    public function delete_activity_type($id) {
        $this->db->where('id', $id);
        return $this->db->delete('activity_type');
    }

    /**
     * 经纪人管理
     */
    public function list_user(){
        // 每页显示的记录条数，默认20条
        $numPerPage = $this->input->post('numPerPage') ? $this->input->post('numPerPage') : 20;
        $pageNum = $this->input->post('pageNum') ? $this->input->post('pageNum') : 1;

        //获得总记录数
        $this->db->select('count(1) as num');
        $this->db->from('user');
        if($this->session->userdata('role_id') == 2) {
            $this->db->where('company_id', $this->session->userdata('company_id'));
        } else if($this->session->userdata('role_id') > 2) {
            $this->db->where('subsidiary_id', $this->session->userdata('subsidiary_id'));
        }

        $rs_total = $this->db->get()->row();
        //总记录数
        $data['countPage'] = $rs_total->num;

        $data['rel_name'] = null;
        //list
        $this->db->select('a.*, b.name AS company_name, c.name AS subsidiary_name, d.name AS role_name,e.name position_name');
        $this->db->from('user a');
        $this->db->join('company b', 'a.company_id = b.id', 'left');
        $this->db->join('subsidiary c', 'a.subsidiary_id = c.id', 'left');
        $this->db->join('role d', 'a.role_id = d.id', 'left');
        $this->db->join('position e', 'a.position_id = e.id', 'left');
        if($this->session->userdata('role_id') == 2) {
            $this->db->where('a.company_id', $this->session->userdata('company_id'));
        } else if($this->session->userdata('role_id') > 2) {
            $this->db->where('a.subsidiary_id', $this->session->userdata('subsidiary_id'));
        }
        $this->db->limit($numPerPage, ($pageNum - 1) * $numPerPage );
        $this->db->order_by($this->input->post('orderField') ? $this->input->post('orderField') : 'id', $this->input->post('orderDirection') ? $this->input->post('orderDirection') : 'desc');
        $data['res_list'] = $this->db->get()->result();
        $data['pageNum'] = $pageNum;
        $data['numPerPage'] = $numPerPage;
        return $data;
    }

    public function save_user($pic = NULL) {
        $data = array(
            'username' => $this->input->post('tel'),
            'password' => sha1('888888'),
            'tel' => $this->input->post('tel'),
            'company_id' => $this->input->post('company_id'),
            'subsidiary_id' => $this->input->post('subsidiary_id'),
            'rel_name' => $this->input->post('rel_name'),
            'role_id' => $this->input->post('role_id'),
            'position_id' => $this->input->post('position_id'),
            'flag'=>$this->input->post('flag')
        );
        if(!empty($pic)) {
            $data['pic'] = $pic;
        }

        $this->db->trans_start();//--------开始事务

        if($this->input->post('id')){//修改
            $this->db->where('id', $this->input->post('id'));
            $this->db->update('user', $data);
        } else {
            $this->db->insert('user', $data);
        }

        $this->db->trans_complete();//------结束事务
        if ($this->db->trans_status() === FALSE) {
            return -1;
        } else {
            return 1;
        }
    }

    public function get_user($id) {
        return $this->db->get_where('user', array('id' => $id))->row_array();
    }

    public function delete_user($id) {
        $this->db->where('id', $id);
        return $this->db->delete('user');
    }

    public function get_user_by_tel($tel) {
        return $this->db->get_where('user', array('tel' => $tel))->row_array();
    }

    public function get_role_list() {
        return $this->db->get_where('role', array('id >' => 1))->result_array();
    }

    public function get_position_list() {
        return $this->db->get_where('position', array('id >=' => 1))->result_array();
    }
    /**
     * 获取职务列表
     */
    public function list_position(){
        // 每页显示的记录条数，默认20条
        $numPerPage = $this->input->post('numPerPage') ? $this->input->post('numPerPage') : 20;
        $pageNum = $this->input->post('pageNum') ? $this->input->post('pageNum') : 1;

        //获得总记录数
        $this->db->select('count(1) as num');
        $this->db->from('position');
        $rs_total = $this->db->get()->row();
        //总记录数
        $data['countPage'] = $rs_total->num;

        //list
        $this->db->select('*');
        $this->db->from('position');
        $this->db->limit($numPerPage, ($pageNum - 1) * $numPerPage );
        $this->db->order_by($this->input->post('orderField') ? $this->input->post('orderField') : 'id', $this->input->post('orderDirection') ? $this->input->post('orderDirection') : 'asc');
        $data['res_list'] = $this->db->get()->result();
        $data['pageNum'] = $pageNum;
        $data['numPerPage'] = $numPerPage;
        return $data;
    }

    /**
     * 保存职务
     */
    public function save_position(){
        $this->db->trans_start();
        if($this->input->post('id')){//修改
            $this->db->where('id', $this->input->post('id'));
            $this->db->update('position', $this->input->post());
        }else{//新增
            $data = $this->input->post();
            $this->db->insert('position', $data);
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            return $this->db_error;
        } else {
            return 1;
        }
    }

    /**
     * 删除职务
     */
    public function delete_position($id){
        $rs = $this->db->delete('position', array('id' => $id));
        if($rs){
            return 1;
        }else{
            return $this->db_error;
        }
    }

    /**
     * 获取职务详情
     */
    public function get_position($id){
        $this->db->select('*')->from('position')->where('id', $id);
        $data = $this->db->get()->row();
        return $data;
    }

    /**
     * 获取代办进程列表
     */
    public function list_course(){
        // 每页显示的记录条数，默认20条
        $numPerPage = $this->input->post('numPerPage') ? $this->input->post('numPerPage') : 20;
        $pageNum = $this->input->post('pageNum') ? $this->input->post('pageNum') : 1;

        //获得总记录数
        $this->db->select('count(1) as num');
        $this->db->from('course');
        $rs_total = $this->db->get()->row();
        //总记录数
        $data['countPage'] = $rs_total->num;

        //list
        $this->db->select('*');
        $this->db->from('course');
        $this->db->limit($numPerPage, ($pageNum - 1) * $numPerPage );
        $this->db->order_by($this->input->post('orderField') ? $this->input->post('orderField') : 'id', $this->input->post('orderDirection') ? $this->input->post('orderDirection') : 'asc');
        $data['res_list'] = $this->db->get()->result();
        $data['pageNum'] = $pageNum;
        $data['numPerPage'] = $numPerPage;
        return $data;
    }

    /**
     * 保存代办进程
     */
    public function save_course(){
        $this->db->trans_start();
        if($this->input->post('id')){//修改
            $this->db->where('id', $this->input->post('id'));
            $this->db->update('course', $this->input->post());
        }else{//新增
            $data = $this->input->post();
            $this->db->insert('course', $data);
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            return $this->db_error;
        } else {
            return 1;
        }
    }

    /**
     * 删除代办进程
     */
    public function delete_course($id){
        $rs = $this->db->delete('course', array('id' => $id));
        if($rs){
            return 1;
        }else{
            return $this->db_error;
        }
    }

    /**
     * 获取代办进程详情
     */
    public function get_course($id){
        $this->db->select('*')->from('course')->where('id', $id);
        $data = $this->db->get()->row();
        return $data;
    }

    /**
     * 获取资料类别列表
     */
    public function list_forum_type(){
        // 每页显示的记录条数，默认20条
        $numPerPage = $this->input->post('numPerPage') ? $this->input->post('numPerPage') : 20;
        $pageNum = $this->input->post('pageNum') ? $this->input->post('pageNum') : 1;

        //获得总记录数
        $this->db->select('count(1) as num');
        $this->db->from('forum_type');
        $rs_total = $this->db->get()->row();
        //总记录数
        $data['countPage'] = $rs_total->num;

        //list
        $this->db->select('*');
        $this->db->from('forum_type');
        $this->db->limit($numPerPage, ($pageNum - 1) * $numPerPage );
        $this->db->order_by($this->input->post('orderField') ? $this->input->post('orderField') : 'id', $this->input->post('orderDirection') ? $this->input->post('orderDirection') : 'asc');
        $data['res_list'] = $this->db->get()->result();
        $data['pageNum'] = $pageNum;
        $data['numPerPage'] = $numPerPage;
        return $data;
    }

    /**
     * 保存资料类别
     */
    public function save_forum_type(){
        $this->db->trans_start();
        if($this->input->post('id')){//修改
            $this->db->where('id', $this->input->post('id'));
            $this->db->update('forum_type', $this->input->post());
        }else{//新增
            $data = $this->input->post();
            $this->db->insert('forum_type', $data);
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            return $this->db_error;
        } else {
            return 1;
        }
    }

    /**
     * 删除资料类别
     */
    public function delete_forum_type($id){
        $rs = $this->db->delete('forum_type', array('id' => $id));
        if($rs){
            return 1;
        }else{
            return $this->db_error;
        }
    }

    /**
     * 获取资料类别详情
     */
    public function get_forum_type($id){
        $this->db->select('*')->from('forum_type')->where('id', $id);
        $data = $this->db->get()->row();
        return $data;
    }
}
