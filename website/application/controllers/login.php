<?php
/**
 * 系统后台登陆控制器
 *
 * @author linzequan <lowkey361@gmail.com>
 *
 */
class Login extends CI_Controller {

    public function index() {
        $this->load->view('login');
    }


    public function post() {
        $actionxm = $this->get_request('actionxm');
        $result = array();
        switch($actionxm) {
            case 'do_login':
                $this->load->model('')
                $info['site_name'] = $this->input->post('site_name');
                $info['alias_name'] = $this->input->post('alias_name');
                $info['domain'] = $this->input->post('domain');
                $info['description'] = $this->input->post('description');
                $result = $this->site_model->insert($info);
                break;
        }
        $this->output_result($result);
    }
}



        $this->load->model('sys/user_model');
        $user_name = $this->input->post('user_name');
        $pwd = $this->input->post('pwd');
        $result = $this->user_model->check_login($user_name, $pwd);
        if($result['success']) {
            $this->load->model('sys/pms_model');
            $user_pms = $this->pms_model->get_user_menu_pms($result['data']['user_id'], $result['data']['is_admin']);
            $userdata = array(
                'user_id' =>$result['data']['user_id'],
                'user_name' =>$result['data']['user_name'],
                'true_name' =>$result['data']['true_name'],
                'is_admin' =>$result['data']['is_admin']
            );
            $this->session->set_userdata($userdata);
            $this->output_result(true, 0, '登录成功');
        } else {
            $this->output_result($result);
        }
