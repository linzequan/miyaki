<?php
/**
 * 套餐订单管理模型
 *
 * @author linzequan <lowkey361@gmail.com>
 *
 */
class package_order_model extends MY_Model {

    private $table = 'package_order';
    private $fields = 'id, pid, uid, custId, discount, care_time, food_num, create_user_id, create_time, update_user_id, update_time';

    public function __construct() {
        parent::__construct();
    }


    public function search($params, $order, $page) {

        $start_time = get_value($params, 'start_time');             // 创建开始时间
        $end_time = get_value($params, 'end_time');                 // 创建结束时间
        $start_care_time = get_value($params, 'start_care_time');   // 调理开始时间
        $end_care_time = get_value($params, 'end_care_time');       // 调理结束时间
        $uid = get_value($params, 'uid');                           // 顾客id
        $custId = get_value($params, 'custId');                     // 业务员ID

        $where = array();
        if($start_time!='') {
            $where[] = array('create_time', strtotime($start_time), '>=');
        }
        if($end_time!='') {
            $where[] = array('create_time', strtotime($end_time), '<=');
        }
        if($start_care_time!='') {
            $where[] = array('care_time', strtotime($start_care_time), '>=');
        }
        if($end_care_time!='') {
            $where[] = array('care_time', strtotime($end_care_time), '<=');
        }
        if($uid!='') {
            $where[] = array('uid', $uid);
        }
        if($custId!='') {
            $wehre[] = array('custId', $custId);
        }

        if(count($order)==0) {
            $order[] = ' create_time desc';
        }

        $datas = $this->db->get_page($this->table, $this->fields, $where, $order, $page);
        $this->load->model('sys/user_model', 'user_model');
        $this->load->model('package_model');
        $this->load->model('recept_regist_model');
        $CI = &get_instance();
        foreach($datas['rows'] as $k=>$v) {
            // 套餐名称
            $package_info = $CI->package_model->get_info($v['pid']);
            $datas['rows'][$k]['package_info'] = $package_info['name'] . ' [' . $package_info['id'] . ']';
            // 客户名称
            if($v['uid']>0) {
                $userinfo = $CI->recept_regist_model->get_info($v['uid']);
                $datas['rows'][$k]['userinfo'] = $userinfo['name'] . '(' . $userinfo['phone'] . ')';
            } else {
                $datas['rows'][$k]['userinfo'] = '';
            }
            // 业务员用户名
            $cust_info = $CI->user_model->get_userinfo_by_id($v['custId']);
            if($cust_info) {
                $datas['rows'][$k]['cust_info'] = $cust_info['user_name'];
            } else {
                $datas['rows'][$k]['cust_info'] = '';
            }
            // 创建记录用户名
            $create_user_info = $CI->user_model->get_userinfo_by_id($v['create_user_id']);
            if($create_user_info) {
                $datas['rows'][$k]['create_user_name'] = $create_user_info['user_name'];
            } else {
                $datas['rows'][$k]['create_user_name'] = '';
            }
            // 修改记录用户名
            $update_user_info = $CI->user_model->get_userinfo_by_id($v['update_user_id']);
            if($update_user_info) {
                $datas['rows'][$k]['update_user_name'] = $update_user_info['user_name'];
            } else {
                $datas['rows'][$k]['update_user_name'] = '';
            }
            // 开始调理时间
            $datas['rows'][$k]['care_time'] = $v['care_time']=='' ? '' : date('Y-m-d', $v['care_time']);
            // 创建时间
            $datas['rows'][$k]['create_time'] = $v['create_time']=='' ? '' : date('Y-m-d H:i:s', $v['create_time']);
            // 更新时间
            $datas['rows'][$k]['update_time'] = $v['update_time']=='' ? '' : date('Y-m-d H:i:s', $v['update_time']);
        }

        return $datas;
    }


    public function get_info($id) {
        if($id<=0) {
            return array();
        }
        $result = array();

        $query = $this->db->select($this->fields)->where('id', $id)->get($this->table);
        if($query->num_rows()<=0) {
            return array();
        }
        $result = $query->row_array();
        return $result;
    }


    public function get_detail($id) {
        if($id<=0) {
            return false;
        }
        $where = array('id'=>$id);
        $query = $this->db->get_where($this->table, $where);
        $rows = $query->result_array();
        $this->load->model('sys/user_model', 'user_model');
        $this->load->model('package_model');
        $this->load->model('recept_regist_model');
        $CI = &get_instance();
        if(count($rows)>0) {
            $str = '<table class="dv-table" border="0" style="width:100%;">' .
                        '<tr>' .
                            '<td class="dv-label">编号: </td>' .
                            '<td>' . $rows[0]['id'] . '</td>' .
                        '</tr>';
            if($rows[0]['pid']!='') {
                $package_info = $CI->package_model->get_info($rows[0]['pid']);
                $package = $package_info['name'] . ' [' . $package_info['id'] . ']';
                $str .= '<tr>' .
                            '<td class="dv-label">套餐: </td>' .
                            '<td>' . $package . '</td>' .
                        '</tr>';
            }
            if($rows[0]['uid']!='') {
                if($rows[0]['uid']>0) {
                    $userinfo = $CI->recept_regist_model->get_info($rows[0]['uid']);
                    $custName = $userinfo['name'] . '(' . $userinfo['phone'] . ')';
                } else {
                    $custName = '';
                }
                $str .= '<tr>' .
                            '<td class="dv-label">客户名称: </td>' .
                            '<td>' . $custName . '</td>' .
                        '</tr>';
            }
            if($rows[0]['custId']!='') {
                $cust_info = $CI->user_model->get_userinfo_by_id($rows[0]['custId']);
                if($cust_info) {
                    $cust = $cust_info['user_name'];
                } else {
                    $cust = '';
                }
                $str .= '<tr>' .
                            '<td class="dv-label">业务员: </td>' .
                            '<td>' . $cust . '</td>' .
                        '</tr>';
            }
            $str .= '</table>';
            return $str;
        } else {
            $str = '系统出错，刷新重试！';
            return $str;
        }
    }


    public function insert($info) {
        $data = array(
            'pid'           => get_value($info, 'pid'),
            'uid'           => get_value($info, 'uid'),
            'custId'        => $this->session->userdata('user_id'),
            'discount'      => get_value($info, 'discount'),
            'care_time'     => get_value($info, 'care_time'),
            'food_num'      => get_value($info, 'food_num'),
            'create_user_id'=> $this->session->userdata('user_id'),
            'create_time'   => time()
        );
        if($data['discount']=='') {
            $data['discount'] = 100;
        }
        if($data['care_time']=='') {
            $data['care_time'] = time();
        } else {
            $data['care_time'] = strtotime($data['care_time']);
        }
        $this->db->insert($this->table, $data);
        return $this->create_result(true, 0, array('id'=>$this->db->insert_id()));
    }

}
