<?php
/**
 * 服务跟进管理模型
 *
 * @author linzequan <lowkey361@gmail.com>
 *
 */
class follow_model extends MY_Model {

    private $table = 'aftersale_follow';
    private $order_table = 'package_order';
    private $fields = 'id, custId, week, day, weight, test_paper, blood_pressure, blood_sugar, sleep, defecation, zc_time, zc_food, zcjc_time, zcjc_food, wc_time, wc_food, wcjc_time, wcjc_food, wancan_time, wancan_food, wancanjc_time, wancanjc_food, wxc_time, wxc_duration, pj_time, other_sport, drink, expirence, create_user_id, create_time, update_user_id, update_time';
    private $order_fields = 'id, pid, uid, custId, discount, care_time, food_num, create_user_id, create_time, update_user_id, update_time';

    public function __construct() {
        parent::__construct();
    }


    public function search($params, $order, $page) {

        $name = get_value($params, 'name');                     // 客户名称
        $phone = get_value($params, 'phone');                   // 客户电话
        $custId = get_value($params, 'cust_id');                // 业务员id
        $create_time = get_value($params, 'create_time');       // 登记日期

        $where = array();
        if($custId!=-1) {
            $where[] = array('custId', $custId);
        }
        if($create_time!='') {
            $where[] = array('create_time', strtotime($create_time), '>=');
            $where[] = array('create_time', strtotime($create_time)+60*60*24, '<=');
        }

        if(count($order)==0) {
            $order[] = ' create_time desc';
        }
        $datas = $this->db->get_page($this->order_table, $this->order_fields, $where, $order, $page);

        $CI = &get_instance();
        $this->load->model('recept_regist_model');
        $this->load->model('sys/user_model', 'user_model');

        foreach($datas['rows'] as $k=>$v) {
            $uinfo = $CI->recept_regist_model->get_info($v['uid']);
            // 补充用户信息字段
            $datas['rows'][$k]['name'] = $uinfo['name'];
            $datas['rows'][$k]['phone'] = $uinfo['phone'];
            // 业务员信息
            $userinfo = $CI->user_model->get_userinfo_by_id($v['custId']);
            if(count($userinfo)>0) {
                $datas['rows'][$k]['cust_name'] = $userinfo['user_name'];
            } else {
                $datas['rows'][$k]['cust_name'] = '';
            }
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
            // 过滤信息
            if($phone!='' || $name!='') {
                if($phone!='' && $uinfo['phone']!=$phone) {
                    unset($datas['rows'][$k]);
                } else if($name!='' && $uinfo['name']!=$name) {
                    unset($datas['rows'][$k]);
                }
            }
        }
        $datas['total'] = count($datas['rows']);

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


    public function get_follwoInfo($id) {
        if($id<=0) {
            return array();
        }

        $result = array();

        // 查询当前是调理期第几周第几天
        $this->load->model('package_order_model');
        $CI = &get_instance();
        $result['order_week'] = $CI->package_order_model->get_order_week($id);
        $result['order_day'] = $CI->package_order_model->get_order_day($id);
        $order_info = $CI->package_order_model->get_info($id);
        $result['custId'] = $order_info['custId'];

        $query = $this->db->select($this->fields)->where('id', $id)->get($this->table);
        if($query->num_rows()<=0) {
            return $result;
        }
        $result = array_merge($result, $query->row_array());

        return $result;
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
                            '<td class="dv-label" colspan="2" style="text-align:center;"><h3>三养健康管理日志</h3> <h5>[第 ' . $rows[0]['week'] .' 周 - 第 ' . $rows[0]['day'] . ' 天]</h5></td>' .
                        '</tr>' .
                        '<tr>' .
                            '<td class="dv-label">编号: </td>' .
                            '<td>' . $rows[0]['id'] . '</td>' .
                        '</tr>';
            if($rows[0]['weight']!='') {
                $str .= '<tr>' .
                            '<td class="dv-label">体重（KG）: </td>' .
                            '<td>' . $rows[0]['weight']. '</td>' .
                        '</tr>';
            }
            if($rows[0]['test_paper']!='') {
                $str .= '<tr>' .
                            '<td class="dv-label">试纸: </td>' .
                            '<td>' . $rows[0]['test_paper']. '</td>' .
                        '</tr>';
            }
            if($rows[0]['blood_pressure']!='') {
                $str .= '<tr>' .
                            '<td class="dv-label">血压: </td>' .
                            '<td>' . $rows[0]['blood_pressure']. '</td>' .
                        '</tr>';
            }
            if($rows[0]['blood_sugar']!='') {
                $str .= '<tr>' .
                            '<td class="dv-label">血糖: </td>' .
                            '<td>' . $rows[0]['blood_sugar']. '</td>' .
                        '</tr>';
            }
            if($rows[0]['sleep']!='') {
                $str .= '<tr>' .
                            '<td class="dv-label">睡眠: </td>' .
                            '<td>' . $rows[0]['sleep']. '</td>' .
                        '</tr>';
            }
            if($rows[0]['defecation']!='') {
                $str .= '<tr>' .
                            '<td class="dv-label">大小便: </td>' .
                            '<td>' . $rows[0]['defecation']. '</td>' .
                        '</tr>';
            }
            if($rows[0]['zc_time']!='') {
                $str .= '<tr>' .
                            '<td class="dv-label">早餐时间: </td>' .
                            '<td>' . $rows[0]['zc_time']. '</td>' .
                        '</tr>';
            }
            if($rows[0]['zc_food']!='') {
                $str .= '<tr>' .
                            '<td class="dv-label">早餐食物: </td>' .
                            '<td>' . $rows[0]['zc_food']. '</td>' .
                        '</tr>';
            }
            if($rows[0]['zcjc_time']!='') {
                $str .= '<tr>' .
                            '<td class="dv-label">早餐加餐时间: </td>' .
                            '<td>' . $rows[0]['zcjc_time']. '</td>' .
                        '</tr>';
            }
            if($rows[0]['zcjc_food']!='') {
                $str .= '<tr>' .
                            '<td class="dv-label">早餐加餐食物: </td>' .
                            '<td>' . $rows[0]['zcjc_food']. '</td>' .
                        '</tr>';
            }
            if($rows[0]['wc_time']!='') {
                $str .= '<tr>' .
                            '<td class="dv-label">午餐时间: </td>' .
                            '<td>' . $rows[0]['wc_time']. '</td>' .
                        '</tr>';
            }
            if($rows[0]['wc_food']!='') {
                $str .= '<tr>' .
                            '<td class="dv-label">午餐食物: </td>' .
                            '<td>' . $rows[0]['wc_food']. '</td>' .
                        '</tr>';
            }
            if($rows[0]['wcjc_time']!='') {
                $str .= '<tr>' .
                            '<td class="dv-label">午餐加餐时间: </td>' .
                            '<td>' . $rows[0]['wcjc_time']. '</td>' .
                        '</tr>';
            }
            if($rows[0]['wcjc_food']!='') {
                $str .= '<tr>' .
                            '<td class="dv-label">午餐加餐食物: </td>' .
                            '<td>' . $rows[0]['wcjc_food']. '</td>' .
                        '</tr>';
            }
            if($rows[0]['wancan_time']!='') {
                $str .= '<tr>' .
                            '<td class="dv-label">晚餐时间: </td>' .
                            '<td>' . $rows[0]['wancan_time']. '</td>' .
                        '</tr>';
            }
            if($rows[0]['wancan_food']!='') {
                $str .= '<tr>' .
                            '<td class="dv-label">晚餐食物: </td>' .
                            '<td>' . $rows[0]['wancan_food']. '</td>' .
                        '</tr>';
            }
            if($rows[0]['wancanjc_time']!='') {
                $str .= '<tr>' .
                            '<td class="dv-label">晚餐加餐时间: </td>' .
                            '<td>' . $rows[0]['wancanjc_time']. '</td>' .
                        '</tr>';
            }
            if($rows[0]['wancanjc_food']!='') {
                $str .= '<tr>' .
                            '<td class="dv-label">晚餐加餐食物: </td>' .
                            '<td>' . $rows[0]['wancanjc_food']. '</td>' .
                        '</tr>';
            }
            if($rows[0]['wxc_time']!='') {
                $str .= '<tr>' .
                            '<td class="dv-label">五行操时间: </td>' .
                            '<td>' . $rows[0]['wxc_time']. '</td>' .
                        '</tr>';
            }
            if($rows[0]['wxc_duration']!='') {
                $str .= '<tr>' .
                            '<td class="dv-label">五行操时长（分钟）: </td>' .
                            '<td>' . $rows[0]['wxc_duration']. '</td>' .
                        '</tr>';
            }
            if($rows[0]['pj_time']!=0) {
                $str .= '<tr>' .
                            '<td class="dv-label">泡脚时间（分钟）: </td>' .
                            '<td>' . $rows[0]['pj_time']. '</td>' .
                        '</tr>';
            }
            if($rows[0]['other_sport']!='') {
                $str .= '<tr>' .
                            '<td class="dv-label">其他运动: </td>' .
                            '<td>' . $rows[0]['other_sport']. '</td>' .
                        '</tr>';
            }
            if($rows[0]['drink']!='') {
                $str .= '<tr>' .
                            '<td class="dv-label">饮水（ML）: </td>' .
                            '<td>' . $rows[0]['drink']. '</td>' .
                        '</tr>';
            }
            if($rows[0]['expirence']!='') {
                $str .= '<tr>' .
                            '<td class="dv-label">体会: </td>' .
                            '<td>' . $rows[0]['expirence']. '</td>' .
                        '</tr>';
            }
            $str .= '</table>';
            return $str;
        } else {
            $str = '系统出错，刷新重试！';
            return $str;
        }
    }


    public function has_record($custId, $week=1, $day=1) {
        if($custId<=0) {
            return array();
        }
        $result = array();
        $where = array('custId'=>$custId, 'week'=>$week, 'day'=>$day);

        $query = $this->db->select($this->fields)->$where->get($this->table);
        return ($query->num_rows()<=0 ? false : true);
    }


    public function insert($info) {
        $data = array(
            'custId'        => get_value($info, 'custId'),
            'week'          => get_value($info, 'week'),
            'day'           => get_value($info, 'day'),
            'weight'        => get_value($info, 'weight'),
            'test_paper'    => get_value($info, 'test_paper'),
            'blood_pressure'=> get_value($info, 'blood_pressure'),
            'blood_sugar'   => get_value($info, 'blood_sugar'),
            'sleep'         => get_value($info, 'sleep'),
            'defecation'    => get_value($info, 'defecation'),
            'zc_time'       => get_value($info, 'zc_time'),
            'zc_food'       => get_value($info, 'zc_food'),
            'zcjc_time'     => get_value($info, 'zcjc_time'),
            'zcjc_food'     => get_value($info, 'zcjc_food'),
            'wc_time'       => get_value($info, 'wc_time'),
            'wc_food'       => get_value($info, 'wc_food'),
            'wcjc_time'     => get_value($info, 'wcjc_time'),
            'wcjc_food'     => get_value($info, 'wcjc_food'),
            'wancan_time'   => get_value($info, 'wancan_time'),
            'wancan_food'   => get_value($info, 'wancan_food'),
            'wancanjc_time' => get_value($info, 'wancanjc_time'),
            'wancanjc_food' => get_value($info, 'wancanjc_food'),
            'wxc_time'      => get_value($info, 'wxc_time'),
            'wxc_duration'  => get_value($info, 'wxc_duration'),
            'pj_time'       => get_value($info, 'pj_time'),
            'other_sport'   => get_value($info, 'other_sport'),
            'drink'         => get_value($info, 'drink'),
            'expirence'     => get_value($info, 'expirence'),
            'create_user_id'=> $this->session->userdata('user_id'),
            'create_time'   => time()
        );
        foreach($data as $k=>$v) {
            if($v=='') {
                unset($data[$k]);
            }
        }
        $this->db->insert($this->table, $data);

        return $this->create_result(true, 0, array('id'=>$this->db->insert_id()));
    }


    public function update($id, $info) {
        if($id==0) {
            $this->insert($info);
        } else {
            $data = array(
                'weight'        => get_value($info, 'weight'),
                'test_paper'    => get_value($info, 'test_paper'),
                'blood_pressure'=> get_value($info, 'blood_pressure'),
                'blood_sugar'   => get_value($info, 'blood_sugar'),
                'sleep'         => get_value($info, 'sleep'),
                'defecation'    => get_value($info, 'defecation'),
                'zc_time'       => get_value($info, 'zc_time'),
                'zc_food'       => get_value($info, 'zc_food'),
                'zcjc_time'     => get_value($info, 'zcjc_time'),
                'zcjc_food'     => get_value($info, 'zcjc_food'),
                'wc_time'       => get_value($info, 'wc_time'),
                'wc_food'       => get_value($info, 'wc_food'),
                'wcjc_time'     => get_value($info, 'wcjc_time'),
                'wcjc_food'     => get_value($info, 'wcjc_food'),
                'wancan_time'   => get_value($info, 'wancan_time'),
                'wancan_food'   => get_value($info, 'wancan_food'),
                'wancanjc_time' => get_value($info, 'wancanjc_time'),
                'wancanjc_food' => get_value($info, 'wancanjc_food'),
                'wxc_time'      => get_value($info, 'wxc_time'),
                'wxc_duration'  => get_value($info, 'wxc_duration'),
                'pj_time'       => get_value($info, 'pj_time'),
                'other_sport'   => get_value($info, 'other_sport'),
                'drink'         => get_value($info, 'drink'),
                'expirence'     => get_value($info, 'expirence'),
                'update_user_id'=> $this->session->userdata('user_id'),
                'update_time'   => time()
            );
            $where = array('id'=>$id);
            $this->db->update($this->table, $data, $where);
            return $this->create_result(true, 0, $where);
        }
    }

}
