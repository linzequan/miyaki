<?php
/**
 * 客户跟进——客户台账控制器
 *
 * @author linzequan <lowkey361@gmail.com>
 *
 */
class ledger extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('recept_regist_model', 'def_model');
    }


    public function index() {
        $data['showBid'] = $this->session->userdata('is_admin')=='1' ? true : false;
        $this->load->view('customer/ledger', $data);
    }


    public function get() {
        $actionxm = $this->get_request('actionxm');
        $result = array();
        switch($actionxm) {
            case 'search':
                $params = $this->input->post('rs');
                $order = get_datagrid_order();
                $page = get_datagrid_page();
                $result = $this->def_model->ledger_search($params, $order, $page);
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
