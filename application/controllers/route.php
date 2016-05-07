<?php
/**
 * Created by PhpStorm.
 * User: bin.shen
 * Date: 5/7/16
 * Time: 13:53
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
class Route extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
    }

    public function review() {
        $this->load->view('review.html');
    }
}