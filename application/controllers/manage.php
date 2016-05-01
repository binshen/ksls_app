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
}
