<?php
/**
 * Created by PhpStorm.
 * User: bin.shen
 * Date: 5/2/16
 * Time: 09:56
 */

 if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 后台画面控制器
 *
 * @package		app
 * @subpackage	core
 * @category	controller
 * @author		yaobin<645894453@qq.com>
 *
 */
class Index extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
    }

    public function index() {
        $this->load->view('index.html');
    }
}