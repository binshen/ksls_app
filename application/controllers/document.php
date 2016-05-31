<?php
/**
 * Created by PhpStorm.
 * User: bin.shen
 * Date: 5/31/16
 * Time: 16:53
 */

<?php
/**
 * Created by PhpStorm.
 * User: bin.shen
 * Date: 5/31/16
 * Time: 16:23
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Document extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function list_doc() {

        $this->display('online_doc.html');
    }

    public function view_doc() {

        $this->display('doc_view.html');
    }
}