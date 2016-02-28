<?php
/**
 * 交易管理——套餐订购控制器
 *
 * @author linzequan <lowkey361@gmail.com>
 *
 */
class package_order extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('package_order_model', 'def_model');
    }


    public function index() {
        $this->load->view('transaction/package_order');
    }


    public function info($id=0) {
        $data = array(
            'actionxm' => 'insert',
            'info' => array()
        );
        if(intval($id)>0) {
            $info = $this->def_model->get_info($id);
            if(count($info)>0) {
                $data['actionxm'] = 'update';
                $data['info'] = $info;
            }
        }
        $this->load->view('transaction/package_order_info', $data);
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
                $result = $this->def_model->get_detail($id);
                echo $result;
                break;
            case 'getUserList':
                $this->load->model('recept_regist_model', 'rrModel');
                $result = $this->rrModel->getCustomerJSON();
                echo json_encode($result);
                break;
            case 'getPackageList':
                $this->load->model('package_model', 'pModel');
                $result = $this->pModel->getPackageJSON();
                echo json_encode($result);
                break;
        }
    }


    public function post() {
        $actionxm = $this->get_request('actionxm');
        $result = array();
        switch($actionxm) {
            case 'insert':
                $info = $this->get_request();
                $result = $this->def_model->insert($info);
                break;
        }
        $this->output_result($result);
    }

}
