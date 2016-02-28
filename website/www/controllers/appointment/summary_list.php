<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class summary_list extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('summary_list_model', 'def_model');
    }


    public function index() {
        $this->load->view('appointment/summary_list');
    }


    public function post() {
        $actionxm = $this->get_request('actionxm');
        $result = array();
        switch($actionxm) {
            case 'summary':
                $info = array();
                $info['name'] = $this->input->get_post('name');
                $info['age'] = $this->input->get_post('age');
                $info['sex'] = $this->input->get_post('sex');
                $info['phone_ext'] = $this->input->get_post('phone_ext');
                $info['phone'] = $this->input->get_post('phone');
                $info['branchId'] = $this->input->get_post('branchId');
                $info['summary_time'] = $this->input->get_post('summary_time');
                $info['summary_item'] = $this->input->get_post('summary_item');
                $result = $this->def_model->insert($info);
                break;
        }
        $this->output_result($result);
    }
}
