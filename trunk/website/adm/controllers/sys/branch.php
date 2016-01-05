<?php
/**
 * 分店树管理控制器
 *
 * @author linzequan <lowkey361@gmail.com>
 *
 */
class branch extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('sys/branch_model', 'def_model');
    }


    public function index() {
        $this->load->view('sys/branch');
    }


    public function get(){
        $actionxm = $this->get_request('actionxm');
        $result = array();
        switch($actionxm) {
            case 'search':
                $result = $this->def_model->search();
                break;
        }
        echo json_encode($result);
    }


    public function post() {
        $actionxm = $this->get_request('actionxm');
        $result = array();
        switch($actionxm) {
            case 'insert':
                $info['name'] = $this->input->post('name');
                $info['pid'] = $this->input->post('pid');
                $info['sort'] = $this->input->post('sort');
                $result = $this->def_model->insert($info);
                break;
            case 'update':
                $id = $this->input->post('id');
                $info['name'] = $this->input->post('name');
                $info['pid'] = $this->input->post('pid');
                $info['sort'] = $this->input->post('sort');
                $result = $this->def_model->update($id, $info);
                break;
            case 'delete':
                $id = $this->input->post('id');
                $result = $this->def_model->delete($id);
                break;
        }
        $this->output_result($result);
    }
}
