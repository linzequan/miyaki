<?php
/**
 * 接待登记管理模型
 *
 * @author linzequan <lowkey361@gmail.com>
 *
 */
class recept_regist_model extends MY_Model {

    private $table = 'recept_regist';
    private $fields = 'id, name, age, sex, phone, address, member_count, remarks, height, weight, blood_pressure, blood_sugar, fat_percent, BNI, visceral_fat, basal_metabolism, body_age, waistline, hipline, thigh_circumference, manage_aim, health_status, drugs_used, member_status, create_user_id, create_time, update_user_id, update_time';

    public function __construct() {
        parent::__construct();
    }


    public function search($params, $order, $page) {
        $where = array();
        if(count($order)==0) {
            $order[] = ' create_time desc';
        }
        $datas = $this->db->get_page($this->table, $this->fields, $where, $order, $page);
        $this->load->model('sys/user_model', 'user_model');
        $CI = &get_instance();
        foreach($datas['rows'] as $k=>$v) {
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
            $datas['rows'][$k]['create_time'] = date('Y-m-d H:i:s', $v['create_time']);
            // 更新时间
            $datas['rows'][$k]['update_time'] = date('Y-m-d H:i:s', $v['update_time']);
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
            if($rows[0]['address']!='') {
                $str .= '<tr>' .
                            '<td class="dv-label">地址: </td>' .
                            '<td>' . $rows[0]['address'] . '</td>' .
                        '</tr>';
            }
            if($rows[0]['member_count']!='') {
                $str .= '<tr>' .
                            '<td class="dv-label">家庭成员数量: </td>' .
                            '<td>' . $rows[0]['member_count'] . '</td>' .
                        '</tr>';
            }
            if($rows[0]['remarks']!='') {
                $str .= '<tr>' .
                            '<td class="dv-label">备注: </td>' .
                            '<td>' . $rows[0]['remarks'] . '</td>' .
                        '</tr>';
            }
            if($rows[0]['height']!='') {
                $str .= '<tr>' .
                            '<td class="dv-label">身高: </td>' .
                            '<td>' . $rows[0]['height'] . '</td>' .
                        '</tr>';
            }
            if($rows[0]['weight']!='') {
                $str .= '<tr>' .
                            '<td class="dv-label">体重: </td>' .
                            '<td>' . $rows[0]['weight'] . '</td>' .
                        '</tr>';
            }
            if($rows[0]['blood_pressure']!='') {
                $str .= '<tr>' .
                            '<td class="dv-label">血压: </td>' .
                            '<td>' . $rows[0]['blood_pressure'] . '</td>' .
                        '</tr>';
            }
            if($rows[0]['blood_sugar']!='') {
                $str .= '<tr>' .
                            '<td class="dv-label">血糖: </td>' .
                            '<td>' . $rows[0]['blood_sugar'] . '</td>' .
                        '</tr>';
            }
            if($rows[0]['fat_percent']!='') {
                $str .= '<tr>' .
                            '<td class="dv-label">脂肪率: </td>' .
                            '<td>' . $rows[0]['fat_percent'] . '</td>' .
                        '</tr>';
            }
            if($rows[0]['BNI']!='') {
                $str .= '<tr>' .
                            '<td class="dv-label">BNI: </td>' .
                            '<td>' . $rows[0]['BNI'] . '</td>' .
                        '</tr>';
            }
            if($rows[0]['visceral_fat']!='') {
                $str .= '<tr>' .
                            '<td class="dv-label">内脏脂肪: </td>' .
                            '<td>' . $rows[0]['visceral_fat'] . '</td>' .
                        '</tr>';
            }
            if($rows[0]['basal_metabolism']!='') {
                $str .= '<tr>' .
                            '<td class="dv-label">基础代谢: </td>' .
                            '<td>' . $rows[0]['basal_metabolism'] . '</td>' .
                        '</tr>';
            }
            if($rows[0]['body_age']!='') {
                $str .= '<tr>' .
                            '<td class="dv-label">身体年龄: </td>' .
                            '<td>' . $rows[0]['body_age'] . '</td>' .
                        '</tr>';
            }
            if($rows[0]['waistline']!='') {
                $str .= '<tr>' .
                            '<td class="dv-label">腰围: </td>' .
                            '<td>' . $rows[0]['waistline'] . '</td>' .
                        '</tr>';
            }
            if($rows[0]['hipline']!='') {
                $str .= '<tr>' .
                            '<td class="dv-label">臀围: </td>' .
                            '<td>' . $rows[0]['hipline'] . '</td>' .
                        '</tr>';
            }
            if($rows[0]['thigh_circumference']!='') {
                $str .= '<tr>' .
                            '<td class="dv-label">大腿围: </td>' .
                            '<td>' . $rows[0]['thigh_circumference'] . '</td>' .
                        '</tr>';
            }
            if($rows[0]['manage_aim']!='') {
                $str .= '<tr>' .
                            '<td class="dv-label">管理目标: </td>' .
                            '<td>' . $rows[0]['manage_aim'] . '</td>' .
                        '</tr>';
            }
            if($rows[0]['health_status']!='') {
                $str .= '<tr>' .
                            '<td class="dv-label">健康现状描述（病症）: </td>' .
                            '<td>' . $rows[0]['health_status'] . '</td>' .
                        '</tr>';
            }
            if($rows[0]['drugs_used']!='') {
                $str .= '<tr>' .
                            '<td class="dv-label">所用药物或其他（药物名称、用量）: </td>' .
                            '<td>' . $rows[0]['drugs_used'] . '</td>' .
                        '</tr>';
            }
            if($rows[0]['member_status']!='') {
                $str .= '<tr>' .
                            '<td class="dv-label">家庭成员现状: </td>' .
                            '<td>' . $rows[0]['member_status'] . '</td>' .
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
        $this->db->insert($this->table, $info);
        $record_id = $this->db->insert_id();
        return $this->create_result(true, 0, array('id'=>$record_id));
    }
}
