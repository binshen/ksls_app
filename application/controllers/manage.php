<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 后台画面控制器
 *
 * @package		app
 * @subpackage	core
 * @category	controller
 * @author		yaobin<645894453@qq.com>
 *
 */
class Manage extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('manage_model');
		$this->load->library('image_lib');
	}

	function _remap($method,$params = array())
	{
		if(!$this->session->userdata('user_id'))
		{
			if($this->input->is_ajax_request()){
				header('Content-type: text/json');
				echo '{
                        "statusCode":"301",
                        "message":"\u4f1a\u8bdd\u8d85\u65f6\uff0c\u8bf7\u91cd\u65b0\u767b\u5f55\u3002"
                    }';
			}else{
				redirect(site_url('manage_login/login'));
			}

		}else{
			return call_user_func_array(array($this, $method), $params);
		}
	}

	public function index()
	{
		$this->load->view('manage/index.php');
	}

	/**
	 * 公司信息
	 */
	public function list_company() {
		$data = $this->manage_model->list_company();
		$this->load->view('manage/list_company.php',$data);
	}

	public function add_company() {
		$this->load->view('manage/add_company.php');
	}

	public function save_company() {
		$ret = $this->manage_model->save_company();
		if($ret == 1){
			form_submit_json("200", "操作成功", 'list_company');
		} else {
			form_submit_json("300", "保存失败");
		}
	}

	public function edit_company($id) {
		$data = $this->manage_model->get_company($id);
		$this->load->view('manage/add_company.php', $data);
	}

	public function delete_company($id) {
		$ret = $this->manage_model->delete_company($id);
		if($ret == 1) {
			form_submit_json("200", "操作成功", 'list_company', '', '');
		} else {
			form_submit_json("300", "删除失败");
		}
	}

	/**
	 * 分店信息
	 */
	public function list_subsidiary() {
		$data = $this->manage_model->list_subsidiary();
		$this->load->view('manage/list_subsidiary.php', $data);
	}

	public function add_subsidiary() {
		$data = array();
		$data['company_list'] = $this->manage_model->get_company_list();
		$this->load->view('manage/add_subsidiary.php', $data);
	}

	public function save_subsidiary() {
		$ret = $this->manage_model->save_subsidiary();
		if($ret == 1){
			form_submit_json("200", "操作成功", 'list_subsidiary');
		}else{
			form_submit_json("300", "保存失败");
		}
	}

	public function edit_subsidiary($id) {
		$data = $this->manage_model->get_subsidiary($id);
		$data['company_list'] = $this->manage_model->get_company_list();
		$this->load->view('manage/add_subsidiary.php', $data);
	}

	public function delete_subsidiary($id) {
		$ret = $this->manage_model->delete_subsidiary($id);
		if($ret == 1) {
			form_submit_json("200", "操作成功", 'list_subsidiary', '', '');
		} else {
			form_submit_json("300", "删除失败");
		}
	}

	/**
	 * 角色信息
	 */
	public function list_role() {
		$data = $this->manage_model->list_role();
		$this->load->view('manage/list_role.php',$data);
	}

	public function add_role() {
		$this->load->view('manage/add_role.php');
	}

	public function save_role() {
		$ret = $this->manage_model->save_role();
		if($ret == 1){
			form_submit_json("200", "操作成功", 'list_role');
		} else {
			form_submit_json("300", "保存失败");
		}
	}

	public function edit_role($id) {
		$data = $this->manage_model->get_role($id);
		$this->load->view('manage/add_role.php', $data);
	}

	public function delete_role($id) {
		$ret = $this->manage_model->delete_role($id);
		if($ret == 1) {
			form_submit_json("200", "操作成功", 'list_role', '', '');
		} else {
			form_submit_json("300", "删除失败");
		}
	}

	/**
	 * 行程选项
	 */
	public function list_activity_type() {
		$data = $this->manage_model->list_activity_type();
		$this->load->view('manage/list_activity_type.php',$data);
	}

	public function add_activity_type() {
		$this->load->view('manage/add_activity_type.php');
	}

	public function save_activity_type() {
		$ret = $this->manage_model->save_activity_type();
		if($ret == 1){
			form_submit_json("200", "操作成功", 'list_activity_type');
		} else {
			form_submit_json("300", "保存失败");
		}
	}

	public function edit_activity_type($id) {
		$data = $this->manage_model->get_activity_type($id);
		$this->load->view('manage/add_activity_type.php', $data);
	}

	public function delete_activity_type($id) {
		$ret = $this->manage_model->delete_activity_type($id);
		if($ret == 1) {
			form_submit_json("200", "操作成功", 'list_activity_type', '', '');
		} else {
			form_submit_json("300", "删除失败");
		}
	}

	/**
	 * 用户管理
	 */
	public function list_user() {
		$data = $this->manage_model->list_user();
		$this->load->view('manage/list_user.php', $data);
	}

	public function add_user() {
		$data = array();
		$data['company_list'] = $this->manage_model->get_company_list();
		if(!empty($data['company_list'])) {
			$data['subsidiary_list'] = $this->manage_model->get_subsidiary_list_by_company($data['company_list'][0]->id);
		}
		$data['role_list'] = $this->manage_model->get_role_list();
		$this->load->view('manage/add_user.php', $data);
	}

	public function save_user() {

		if(!$this->input->post('id')){
			$tel = $this->input->post('tel');
			$broker = $this->manage_model->get_user_by_tel($tel);
			if(!empty($broker)) {
				form_submit_json("300", "手机号已经注册过");
				return;
			}
		}

		if($_FILES["userfile"]['name'] and $this->input->post('old_img')){//修改上传的图片，需要先删除原来的图片
			@unlink('./././uploadfiles/profile/'.$this->input->post('old_img'));//del old img
		}else if(!$_FILES["userfile"]['name'] and !$this->input->post('old_img')){//未上传图片
			form_submit_json("300", "请添加图片");exit;
		}

		if(!$_FILES["userfile"]['name'] and $this->input->post('old_img')){//不修改图片信息
			$ret = $this->manage_model->save_user();
		}else{
			$config['upload_path'] = './././uploadfiles/profile';
			$config['allowed_types'] = 'gif|jpg|png|jpeg';
			$config['max_size'] = '1000';
			$config['encrypt_name'] = true;
			$this->load->library('upload', $config);
			if($this->upload->do_upload()){
				$img_info = $this->upload->data();
				$ret = $this->manage_model->save_user($img_info['file_name']);
			}else{
				form_submit_json("300", $this->upload->display_errors('<b>','</b>'));
				exit;
			}
		}

		if($ret == 1){
			form_submit_json("200", "操作成功", 'list_user');
		} else {
			form_submit_json("300", "保存失败");
		}
	}

	public function edit_user($id) {
		$data = $this->manage_model->get_user($id);
		$data['company_list'] = $this->manage_model->get_company_list();
		$data['subsidiary_list'] = $this->manage_model->get_subsidiary_list_by_company($data['company_id']);
		$data['role_list'] = $this->manage_model->get_role_list();
		$this->load->view('manage/add_user.php', $data);
	}

	public function delete_user($id) {
		$ret = $this->manage_model->delete_user($id);
		if($ret == 1) {
			form_submit_json("200", "操作成功", 'list_user', '', '');
		} else {
			form_submit_json("300", "删除失败");
		}
	}

	public function get_subsidiary_list($id) {
		$data = $this->manage_model->get_subsidiary_list_by_company($id);
		$subSidiary = array();
		foreach ($data as $s) {
			$subSidiary[] = array($s['id'], $s['name']);
		}
		echo json_encode($subSidiary);
		die;
	}
}
