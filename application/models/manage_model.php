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
        $this->db->select('a.*,b.permission_id');
        $this->db->from('user a');
        $this->db->join('role b','a.role_id = b.id','inner');
        $this->db->where('a.username', $username);
        $this->db->where('a.password', sha1($password));
        $this->db->where('b.permission_id <= 4');
        $rs = $this->db->get();
        if ($rs->num_rows() > 0) {
        	$res = $rs->row();
        	$user_info['user_id'] = $res->id;
            $user_info['username'] = $username;
            $user_info['rel_name'] = $res->rel_name;
          //  $user_info['role_id'] = $res->role_id;
            $user_info['permission_id'] = $res->permission_id;
            $user_info['company_id'] = $res->company_id;
            $subids = $this->db->select()->from('user_subsidiary')->where('user_id',$res->id)->get()->result_array();
            $sids = array();
            if($subids){
                foreach($subids as $id){
                    $sids[]=$id['subsidiary_id'];
                }
            }
            $user_info['subsidiary_id_array'] = $sids;
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
        if($this->session->userdata('permission_id') > 1) {
            $this->db->where('id', $this->session->userdata('company_id'));
        }

        $rs_total = $this->db->get()->row();
        //总记录数
        $data['countPage'] = $rs_total->num;

        //list
        $this->db->select('*')->from('company');
        if($this->session->userdata('permission_id') > 1) {
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
        if($this->session->userdata('permission_id') == 2) {
            $this->db->where('company_id', $this->session->userdata('company_id'));
        } else if($this->session->userdata('permission_id') > 2) {
            $this->db->where_in('id', $this->session->userdata('subsidiary_id_array'));
        }

        $rs_total = $this->db->get()->row();
        //总记录数
        $data['countPage'] = $rs_total->num;
        $data['company_id'] = null;

        //list
        $this->db->select('a.*, b.name AS company_name');
        $this->db->from('subsidiary a');
        $this->db->join('company b', 'a.company_id = b.id', 'left');
        if($this->session->userdata('permission_id') == 2) {
            $this->db->where('a.company_id', $this->session->userdata('company_id'));
        } else if($this->session->userdata('permission_id') > 2) {
            $this->db->where_in('a.id', $this->session->userdata('subsidiary_id_array'));
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
        if($this->session->userdata('permission_id') == 1) {
            return $this->db->get('company')->result();
        } else {
            return $this->db->get_where('company', array('id' => $this->session->userdata('company_id')))->result();
        }
    }

    public function get_subsidiary_list_by_company($id) {
        if($this->session->userdata('permission_id') <=2) {
            return $this->db->get_where('subsidiary', array('company_id' => $id))->result_array();
        } else {
            return $this->db->where_in('id', $this->session->userdata('subsidiary_id_array'))->from('subsidiary')->get()->result_array();
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
        $mysql = "
              SELECT DISTINCT  a.id from user a
               LEFT JOIN user_position b on a.id = b.user_id
               LEFT JOIN user_subsidiary d on d.user_id = a.id
               LEFT JOIN role e on e.id = a.role_id
              where  e.permission_id > {$this->session->userdata('permission_id')}
               ";
        if($this->session->userdata('permission_id') == 2) {
            $mysql.=" and a.company_id = ".$this->session->userdata('company_id');
        } else if($this->session->userdata('permission_id') > 2) {
            $string_in='';
            if(is_array($this->session->userdata('subsidiary_id_array'))){
                foreach($this->session->userdata('subsidiary_id_array') as $key=>$item){
                    if($key==0){
                        $string_in.=$item;
                    }else{
                        $string_in.=','.$item;
                    }

                }
            }else{
                $string_in = $this->session->userdata('subsidiary_id_array');
            }

            $mysql .= " AND d.subsidiary_id in (".$string_in.")";
        }
        if($this->input->post('rel_name'))
            $mysql .= " AND a.rel_name like '%".$this->input->post('rel_name')."%'";
        if($this->input->post('tel'))
            $mysql .= " AND a.tel like '%".$this->input->post('tel')."%'";
        if($this->input->post('flag'))
            $mysql .= " AND a.flag = '".$this->input->post('flag')."'";
        if($this->input->post('position_id'))
            $mysql .= " AND b.pid = '".$this->input->post('position_id')."'";
        if($this->input->post('role_id'))
            $mysql .= " AND a.role_id = '".$this->input->post('role_id')."'";
        if($this->input->post('company_id'))
            $mysql .= " AND a.company_id = '".$this->input->post('company_id')."'";
        if($this->input->post('subsidiary_id')){
            $string_in='';
            if(is_array($this->input->post('subsidiary_id'))){
                foreach($this->input->post('subsidiary_id') as $key=>$item){
                    if($key==0){
                        $string_in.=$item;
                    }else{
                        $string_in.=','.$item;
                    }

                }
            }else{
                $string_in = $this->input->post('subsidiary_id');
            }

            $mysql .= " AND d.subsidiary_id in (".$string_in.")";
        }

        $mainsql = "select count(1) as num from (".$mysql.") a";
        $rs_total = $this->db->query($mainsql)->row();
       /* $this->db->select('count(1) as num');
        $this->db->from('user a');
        $this->db->join('user_position b','a.id = b.user_id','left');
        $this->db->join('user_subsidiary d','d.user_id = a.id','left');
        if($this->session->userdata('permission_id') == 2) {
            $this->db->where('a.company_id', $this->session->userdata('company_id'));

        } else if($this->session->userdata('permission_id') > 2) {
            $this->db->where_in('d.subsidiary_id', $this->session->userdata('subsidiary_id_array'));
        }
        if($this->input->post('rel_name'))
            $this->db->like('a.rel_name',$this->input->post('rel_name'));
        if($this->input->post('tel'))
            $this->db->like('a.tel',$this->input->post('tel'));
        if($this->input->post('flag'))
            $this->db->where('a.flag',$this->input->post('flag'));
        if($this->input->post('position_id'))
            $this->db->where('b.pid',$this->input->post('position_id'));
        if($this->input->post('role_id'))
            $this->db->where('a.role_id',$this->input->post('role_id'));
        if($this->input->post('company_id'))
            $this->db->where('a.company_id',$this->input->post('company_id'));
        if($this->input->post('subsidiary_id'))
            $this->db->where_in('d.subsidiary_id',$this->input->post('subsidiary_id'));
        //$this->db->group_by('a.id');
        $rs_total = $this->db->get()->row();*/
       //die(var_dump($this->db->last_query()));
        //总记录数
        $data['relname'] = $this->input->post('rel_name')?$this->input->post('rel_name'):null;
        $data['tel'] = $this->input->post('tel')?$this->input->post('tel'):null;
        $data['flag'] = $this->input->post('flag')?$this->input->post('flag'):null;
        $data['positionid'] = $this->input->post('position_id')?$this->input->post('position_id'):null;
        $data['roleid'] = $this->input->post('role_id')?$this->input->post('role_id'):null;
        $data['companyid'] = $this->input->post('company_id')?$this->input->post('company_id'):null;
        $data['subsidiaryid'] = $this->input->post('subsidiary_id')?$this->input->post('subsidiary_id'):null;
        $data['countPage'] = $rs_total->num?$rs_total->num:0;

        $data['rel_name'] = null;
        //list
        $this->db->select('a.*, b.name AS company_name, c.name AS subsidiary_name, d.name AS role_name,d.permission_id');
        //$this->db->distinct('a.id');
        $this->db->from('user a');
        $this->db->join('company b', 'a.company_id = b.id', 'left');
        $this->db->join('role d', 'a.role_id = d.id', 'left');
        $this->db->join('user_position e', 'a.id = e.user_id', 'left');
        $this->db->join('user_subsidiary f','f.user_id = a.id','left');
        $this->db->join('subsidiary c', 'f.subsidiary_id = c.id', 'left');
        if($this->session->userdata('permission_id') == 2) {
            $this->db->where('a.company_id', $this->session->userdata('company_id'));
        } else if($this->session->userdata('permission_id') > 2) {
            $this->db->where_in('f.subsidiary_id', $this->session->userdata('subsidiary_id_array'));
        }
        if($this->input->post('rel_name'))
            $this->db->like('a.rel_name',$this->input->post('rel_name'));
        if($this->input->post('tel'))
            $this->db->like('a.tel',$this->input->post('tel'));
        if($this->input->post('flag'))
            $this->db->where('a.flag',$this->input->post('flag'));
        if($this->input->post('position_id'))
            $this->db->where('e.pid',$this->input->post('position_id'));
        if($this->input->post('role_id'))
            $this->db->where('a.role_id',$this->input->post('role_id'));
        if($this->input->post('company_id'))
            $this->db->where('a.company_id',$this->input->post('company_id'));
        if($this->input->post('subsidiary_id'))
            $this->db->where_in('f.subsidiary_id',$this->input->post('subsidiary_id'));
        $this->db->where('d.permission_id >',$this->session->userdata('permission_id'));
        $this->db->group_by('a.id');
        $this->db->limit($numPerPage, ($pageNum - 1) * $numPerPage );
        $this->db->order_by($this->input->post('orderField') ? $this->input->post('orderField') : 'id', $this->input->post('orderDirection') ? $this->input->post('orderDirection') : 'desc');
        $data['res_list'] = $this->db->get()->result();
        //die(var_dump($this->db->last_query()));
        $data['pageNum'] = $pageNum;
        $data['numPerPage'] = $numPerPage;
        return $data;
    }

    public function password_reset($id){
        $res = $this->db->where('id',$id)->update('user',array('password'=>sha1('888888')));
        if($res){
            return 1;
        }else{
            return 2;
        }

    }

    public function save_user($pic = NULL) {
        $data = array(
            'username' => $this->input->post('tel'),
            'password' => sha1('888888'),
            'tel' => $this->input->post('tel'),
            'company_id' => $this->input->post('company_id'),
            'rel_name' => $this->input->post('rel_name'),
            'role_id' => $this->input->post('role_id'),
            'flag'=>$this->input->post('flag')
        );
        if(!empty($pic)) {
            $data['pic'] = $pic;
        }

        $this->db->trans_start();//--------开始事务

        if($this->input->post('id')){//修改
            $this->db->where('id', $this->input->post('id'));
            $this->db->update('user', $data);
            $user_id = $this->input->post('id');

        } else {
            $this->db->insert('user', $data);
            $user_id = $this->db->insert_id();
        }
        $this->db->where('user_id',$user_id)->delete('user_position');
        if($this->input->post('pid')){
            $pid=$this->input->post('pid');
            foreach($pid as $id){
                $this->db->insert('user_position', array(
                    'pid'=>$id,
                    'user_id'=>$user_id
                ));
            }
        }
        $this->db->where('user_id',$user_id)->delete('user_subsidiary');
        if($this->input->post('sub_id')){
            $subid=$this->input->post('sub_id');
            foreach($subid as $id){
                $this->db->insert('user_subsidiary', array(
                    'subsidiary_id'=>$id,
                    'user_id'=>$user_id
                ));
            }
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

    public function get_user_pid($id) {
        return $this->db->get_where('user_position', array('user_id' => $id))->result_array();
    }

    public function get_user_subid($id) {
        return $this->db->get_where('user_subsidiary', array('user_id' => $id))->result_array();
    }

    public function delete_user($id) {
        $this->db->where('id', $id);
        return $this->db->delete('user');
    }

    public function get_user_by_tel($tel) {
        return $this->db->get_where('user', array('tel' => $tel))->row_array();
    }

    public function get_role_list() {
        return $this->db->get_where('role', array('id >' => 1,'permission_id >'=>$this->session->userdata('permission_id')))->result_array();
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

    public function list_ticket(){
        // 每页显示的记录条数，默认20条
        $numPerPage = $this->input->post('numPerPage') ? $this->input->post('numPerPage') : 20;
        $pageNum = $this->input->post('pageNum') ? $this->input->post('pageNum') : 1;

        //获得总记录数
        $this->db->select('count(1) as num');
        $this->db->from('ticket');

        if($this->input->post('title'))
            $this->db->like('title',$this->input->post('title'));
        if($this->input->post('type'))
            $this->db->where('type',$this->input->post('type'));

        $rs_total = $this->db->get()->row();
        //总记录数
        $data['countPage'] = $rs_total->num;

        $data['title'] = $this->input->post('title')?$this->input->post('title'):null;
        $data['type'] = $this->input->post('type')?$this->input->post('type'):null;
        //list
        $this->db->select("a.*,b.name type_name");
        $this->db->from('ticket a');
        $this->db->join('forum_type b','a.type = b.id','inner');
        if($this->input->post('title'))
            $this->db->like('a.title',$this->input->post('title'));
        if($this->input->post('type'))
            $this->db->where('a.type',$this->input->post('type'));

        $this->db->limit($numPerPage, ($pageNum - 1) * $numPerPage );
        $this->db->order_by($this->input->post('orderField') ? $this->input->post('orderField') : 'id', $this->input->post('orderDirection') ? $this->input->post('orderDirection') : 'desc');
        $data['res_list'] = $this->db->get()->result();
        $data['type_list'] = $this->db->from('forum_type')->get()->result();
        $data['pageNum'] = $pageNum;
        $data['numPerPage'] = $numPerPage;
        return $data;
    }

    public function delete_ticket($id){
        $rs = $this->db->delete('ticket', array('id' => $id));
        if($rs){
            return 1;
        }else{
            return $this->db_error;
        }
    }

    public function get_ticket($id){
        $this->db->select('a.*,b.name type_name,c.rel_name user_name')->from('ticket a');
        $this->db->join('forum_type b','a.type = b.id','left');
        $this->db->join('user c','c.id = a.user_id','left');
        $this->db->where('a.id',$id);
        $data['head'] = $this->db->get()->row();
        $data['id'] = $id;
        //die(var_dump($data));
        return $data;
    }

    public function download($id){

        $this->load->helper('download');
        $this->load->helper('file');
        $data=$this->db->select()->from('ticket')->where('id',$id)->get()->row_array();
        if ($data){
            $string = read_file('./uploadfiles/doc/'.$data['file']);
            //   $file_name='./uploadfiles/'.$data['url'];//需要下载的文件
            force_download($data['oldfile'],$string);
        }
    }
}
