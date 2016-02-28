<?php
/**
 * 预约列表管理模型
 *
 * @author linzequan <lowkey361@gmail.com>
 *
 */
class summary_list_model extends MY_Model {

    private $table = 'summary_list';
    private $fields = 'id, name, age, sex, phone, branchId, summary_time, summary_item, wx_openid, wx_nickname, wx_sex, wx_city, wx_country, wx_province, wx_language, create_user_id, create_time, update_user_id, update_time';

    public function __construct() {
        parent::__construct();
    }


    public function search($params, $order, $page) {

        $branchId =  get_value($params, 'branch_id');           // 预约分店
        $name = get_value($params, 'name');                     // 姓名
        $phone = get_value($params, 'phone');                   // 手机号码
        $start_time = get_value($params, 'start_time');         // 创建开始时间
        $end_time = get_value($params, 'end_time');             // 创建结束时间

        $where = array();
        if($name!='') {
            $where[] = array('name', $name);
        }
        if($phone!='') {
            $where[] = array('phone', $phone);
        }
        if($start_time!='') {
            $where[] = array('create_time', strtotime($start_time), '>=');
        }
        if($end_time!='') {
            $where[] = array('create_time', strtotime($end_time), '<=');
        }

        if($this->session->userdata('is_admin')=='1') {
            if($branchId!=-1) {
                $where[] = array('branchId', $branchId);
            }
        } else {
            $where[] = array('branchId', $this->session->userdata('bid'));
        }

        if(count($order)==0) {
            $order[] = ' create_time desc';
        }
        $datas = $this->db->get_page($this->table, $this->fields, $where, $order, $page);
        $this->load->model('sys/user_model', 'user_model');
        $this->load->model('sys/branch_model', 'branch_model');
        $CI = &get_instance();
        foreach($datas['rows'] as $k=>$v) {
            // 分店名称
            $datas['rows'][$k]['branch_name'] = $CI->branch_model->get_name_by_id($v['branchId']) ? $CI->branch_model->get_name_by_id($v['branchId']) : '';
            // 创建记录用户名
            $create_user_info = $CI->user_model->get_userinfo_by_id($v['create_user_id']);
            if($create_user_info) {
                $datas['rows'][$k]['create_user_name'] = $create_user_info['user_name'];
            } else {
                $datas['rows'][$k]['update_user_name'] = '';
            }
            // 修改记录用户名
            $update_user_info = $CI->user_model->get_userinfo_by_id($v['update_user_id']);
            if($update_user_info) {
                $datas['rows'][$k]['update_user_name'] = $update_user_info['user_name'];
            } else {
                $datas['rows'][$k]['update_user_name'] = '';
            }
            // 创建时间
            $datas['rows'][$k]['create_time'] = $v['create_time']=='' ? '' : date('Y-m-d H:i:s', $v['create_time']);
            // 更新时间
            $datas['rows'][$k]['update_time'] = $v['update_time']=='' ? '' : date('Y-m-d H:i:s', $v['update_time']);
            // 预约时间
            $datas['rows'][$k]['summary_time'] = $v['summary_time']=='' ? '' : date('Y-m-d H:i:s', $v['summary_time']);
        }
        return $datas;
    }


    public function get_detail($id) {
        if($id<=0) {
            return false;
        }
        $where = array('id'=>$id);
        $query = $this->db->get_where($this->table, $where);
        $rows = $query->result_array();
        if(count($rows)>0) {
            $str = '<table class="dv-table" border="0" style="width:100%;">' .
                        '<tr>' .
                            '<td class="dv-label">编号: </td>' .
                            '<td>' . $rows[0]['id'] . '</td>' .
                        '</tr>';
            if($rows[0]['name']!='') {
                $str .= '<tr>' .
                            '<td class="dv-label">姓名: </td>' .
                            '<td>' . $rows[0]['name'] . '</td>' .
                        '</tr>';
            }
            if($rows[0]['age']!='') {
                $str .= '<tr>' .
                            '<td class="dv-label">年龄: </td>' .
                            '<td>' . $rows[0]['age'] . '</td>' .
                        '</tr>';
            }
            if($rows[0]['sex']!='') {
                switch($rows[0]['sex']) {
                    case 1:
                        $sex_name = '男';
                        break;
                    case 2:
                        $sex_name = '女';
                        break;
                    default:
                        $sex_name = '未知';
                        break;
                }
                $str .= '<tr>' .
                            '<td class="dv-label">性别: </td>' .
                            '<td>' . $sex_name . '</td>' .
                        '</tr>';
            }
            if($rows[0]['phone']!='') {
                $str .= '<tr>' .
                            '<td class="dv-label">电话: </td>' .
                            '<td>' . $rows[0]['phone'] . '</td>' .
                        '</tr>';
            }
            if($rows[0]['branchId']!='') {
                $this->load->model('sys/branch_model', 'branch_model');
                $CI = &get_instance();
                $branch_info = $CI->branch_model->get_name_by_id($rows[0]['branchId']);
                if($branch_info!='') {
                    $str .= '<tr>' .
                            '<td class="dv-label">预约分店: </td>' .
                            '<td>' . $branch_info . '</td>' .
                        '</tr>';
                }
            }
            if($rows[0]['summary_time']!='') {
                $summary_time = $rows[0]['summary_time']=='' ? '' : date('Y-m-d H:i:s', $rows[0]['summary_time']);
                $str .= '<tr>' .
                            '<td class="dv-label">预约时间: </td>' .
                            '<td>' . $summary_time . '</td>' .
                        '</tr>';
            }
            if($rows[0]['summary_item']!='') {
                $str .= '<tr>' .
                            '<td class="dv-label">咨询项目: </td>' .
                            '<td>' . $rows[0]['summary_item'] . '</td>' .
                        '</tr>';
            }
            if($rows[0]['wx_nickname']!='') {
                $str .= '<tr>' .
                            '<td class="dv-label">微信用户昵称: </td>' .
                            '<td>' . $rows[0]['wx_nickname'] . '</td>' .
                        '</tr>';
            }
            $str .= '</table>';
            return $str;
        } else {
            $str = '系统出错，刷新重试！';
            return $str;
        }
    }
}
