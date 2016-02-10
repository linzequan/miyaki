<?php
/**
 * 售后服务——服务跟进控制器
 *
 * @author linzequan <lowkey361@gmail.com>
 *
 */
class follow extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('follow_model', 'def_model');
    }


    public function index() {
        $this->load->view('aftersale/follow');
    }


    public function info($id=0) {
        $data = array(
            'actionxm' => 'insert',
            'info' => array()
        );
        if(intval($id)>0) {
            $info = $this->def_model->get_follwoInfo($id);
            if(count($info)>0) {
                $data['actionxm'] = 'update';
                $data['info'] = $info;
            }
        }
        $this->load->view('aftersale/follow_info', $data);
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
        }
    }


    public function post() {
        $actionxm = $this->get_request('actionxm');
        $result = array();
        switch($actionxm) {
            case 'update':
                $id = $this->input->post('id');
                $info = $this->get_request();
                $result = $this->def_model->update($id, $info);
                break;
        }
        $this->output_result($result);
    }

}
