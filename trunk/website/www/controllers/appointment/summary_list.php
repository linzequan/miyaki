<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class summary_list extends MY_Controller {

    public function __construct() {
        parent::__construct();
    }


    public function index() {
        $this->load->view('appointment/summary_list');
    }
}
