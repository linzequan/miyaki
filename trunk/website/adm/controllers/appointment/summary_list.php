<?php
/**
 * 预约汇总——预约列表控制器
 *
 * @author linzequan <lowkey361@gmail.com>
 *
 */
class summary_list extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('summary_list_model', 'def_model');
    }


    public function index() {
        $data['showBid'] = $this->session->userdata('is_admin')=='1' ? true : false;
        $this->load->view('appointment/summary_list', $data);
    }


    public function get() {
        $actionxm = $this->get_request('actionxm');
        $result = array();
        switch($actionxm) {
            case 'search':
                $params = $this->input->post('rs');
                $order = get_datagrid_order();
                $page = get_datagrid_page();
                $result = $this->def_model->search($params, $order, $page);
                echo json_encode($result);
                break;
            case 'details':
                $id = $this->get_request('id');
                $result = $this->def_model->get_detail($id, true);
                echo $result;
                break;
        }
    }


    public function post() {
        $actionxm = $this->get_request('actionxm');
        $result = array();
        switch($actionxm) {

        }
        $this->output_result($result);
    }
}
