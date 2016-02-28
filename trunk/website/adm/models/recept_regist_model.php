<?php
/**
 * 接待登记管理模型
 *
 * @author linzequan <lowkey361@gmail.com>
 *
 */
class recept_regist_model extends MY_Model {

    private $table = 'recept_regist';
    private $fields = 'id, name, age, sex, phone, address, member_count, remarks, height, weight, blood_pressure, blood_sugar, fat_percent, BNI, visceral_fat, basal_metabolism, body_age, waistline, hipline, thigh_circumference, manage_aim, health_status, drugs_used, member_status, create_user_id, create_time, update_user_id, update_time, branchId, custId';

    public function __construct() {
        parent::__construct();
    }


    public function search($params, $order, $page) {

        $start_time = get_value($params, 'start_time');         // 创建开始时间
        $end_time = get_value($params, 'end_time');             // 创建结束时间
        $name = get_value($params, 'name');                     // 姓名
        $phone = get_value($params, 'phone');                   // 电话
        $branchId = get_value($params, 'branch_id');            // 分店id
        $custId = get_value($params, 'custId');                 // 业务员id

        $where = array();
        if($start_time!='') {
            $where[] = array('create_time', strtotime($start_time), '>=');
        }
        if($end_time!='') {
            $where[] = array('create_time', strtotime($end_time), '<=');
        }
        if($name!='') {
            $where[] = array('name', $name, 'like');
        }
        if($phone!='') {
            $where[] = array('phone', $phone, 'like');
        }
        if($this->session->userdata('is_admin')=='1') {
            if($custId!='') {
                $where[] = array('custId', $custId);
            }
            if($branchId!=-1) {
                $where[] = array('branchId', $branchId);
            }
        } else {
            $where[] = array('custId', $this->session->userdata('user_id'));
        }

        if(count($order)==0) {
            $order[] = ' create_time desc';
        }
        $datas = $this->db->get_page($this->table, $this->fields, $where, $order, $page);
        $this->load->model('sys/user_model', 'user_model');
        $this->load->model('sys/branch_model', 'branch_model');
        $CI = &get_instance();
        foreach($datas['rows'] as $k=>$v) {
            // 分店信息
            $branch_info = $CI->branch_model->get_name_by_id($v['branchId']);
            if($branch_info!='') {
                $datas['rows'][$k]['branch_name'] = $branch_info;
            } else {
                $datas['rows'][$k]['branch_name'] = '';
            }

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
        }
        return $datas;
    }


    public function ledger_search($params, $order, $page) {

        $has_trade = get_value($params, 'has_trade');           // 是否交易

        $datas = $this->search($params, $order, $page);
        $this->load->model('package_order_model');
        $CI = &get_instance();
        foreach($datas['rows'] as $k=>$v) {
            // 查询是否有交易记录
            $datas['rows'][$k]['has_trade'] = $CI->package_order_model->has_trade($v['id']);
            if($has_trade==1 && !$datas['rows'][$k]['has_trade']) {
                // 如果选择已交易，并且是未交易用户
                unset($datas['rows'][$k]);
                $datas['total']--;
            } else if($has_trade==2 && !!$datas['rows'][$k]['has_trade']) {
                // 如果选择未交易，并且是已交易用户
                unset($datas['rows'][$k]);
                $datas['total']--;
            }
        }
        if(count($datas['rows'])==1) {
            $tmp = $datas['rows'];
            $key = key($datas['rows']);
            unset($datas['rows']);
            $datas['rows'][] = $tmp[$key];
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


    public function get_detail($id, $has_trade=false) {
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
            if($has_trade) {
                $this->load->model('package_order_model');
                $CI = &get_instance();
                $has_trade = $CI->package_order_model->has_trade($rows[0]['id']) ? '已交易' : '未交易';
                $str .= '<tr>' .
                            '<td class="dv-label">交易与否: </td>' .
                            '<td>' . $has_trade . '</td>' .
                        '</tr>';
            }
            if($rows[0]['branchId']>0) {
                $this->load->model('sys/branch_model', 'branch_model');
                $CI = &get_instance();
                $branch_info = $CI->branch_model->get_name_by_id($rows[0]['branchId']);
                if($branch_info!='') {
                    $str .= '<tr>' .
                            '<td class="dv-label">所属分店: </td>' .
                            '<td>' . $branch_info . '</td>' .
                        '</tr>';
                }
            }
            if($rows[0]['custId']>0) {
                $this->load->model('sys/user_model', 'user_model');
                $CI = &get_instance();
                $userinfo = $CI->user_model->get_userinfo_by_id($rows[0]['custId']);
                if(count($userinfo)>0) {
                    $str .= '<tr>' .
                            '<td class="dv-label">所属业务员: </td>' .
                            '<td>' . $userinfo['user_name'] . '</td>' .
                        '</tr>';
                }
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
            if($rows[0]['visceral_fat']!=0) {
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
            if($rows[0]['body_age']!=0) {
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
         $data = array(
            'name'                  => get_value($info, 'name'),
            'age'                   => get_value($info, 'age'),
            'sex'                   => get_value($info, 'sex'),
            'phone'                 => get_value($info, 'phone'),
            'address'               => get_value($info, 'address'),
            'member_count'          => get_value($info, 'member_count'),
            'remarks'               => get_value($info, 'remarks'),
            'height'                => get_value($info, 'height'),
            'weight'                => get_value($info, 'weight'),
            'blood_pressure'        => get_value($info, 'blood_pressure'),
            'blood_sugar'           => get_value($info, 'blood_sugar'),
            'fat_percent'           => get_value($info, 'fat_percent'),
            'BNI'                   => get_value($info, 'BNI'),
            'visceral_fat'          => get_value($info, 'visceral_fat') || 0,
            'basal_metabolism'      => get_value($info, 'basal_metabolism'),
            'body_age'              => get_value($info, 'body_age') || 0,
            'waistline'             => get_value($info, 'waistline'),
            'hipline'               => get_value($info, 'hipline'),
            'thigh_circumference'   => get_value($info, 'thigh_circumference'),
            'drugs_used'            => get_value($info, 'drugs_used'),
            'create_user_id'        => $this->session->userdata('user_id'),
            'create_time'           => time(),
            'custId'                => $this->session->userdata('user_id'),
            'branchId'              => $this->session->userdata('bid')
        );
        $this->db->insert($this->table, $data);
        return $this->create_result(true, 0, array('id'=>$this->db->insert_id()));
    }


    public function update($id, $info) {
        $data = array(
            'name'                  => get_value($info, 'name'),
            'age'                   => get_value($info, 'age'),
            'sex'                   => get_value($info, 'sex'),
            'phone'                 => get_value($info, 'phone'),
            'address'               => get_value($info, 'address'),
            'member_count'          => get_value($info, 'member_count'),
            'remarks'               => get_value($info, 'remarks'),
            'height'                => get_value($info, 'height'),
            'weight'                => get_value($info, 'weight'),
            'blood_pressure'        => get_value($info, 'blood_pressure'),
            'blood_sugar'           => get_value($info, 'blood_sugar'),
            'fat_percent'           => get_value($info, 'fat_percent'),
            'BNI'                   => get_value($info, 'BNI'),
            'visceral_fat'          => get_value($info, 'visceral_fat') || 0,
            'basal_metabolism'      => get_value($info, 'basal_metabolism'),
            'body_age'              => get_value($info, 'body_age') || 0,
            'waistline'             => get_value($info, 'waistline'),
            'hipline'               => get_value($info, 'hipline'),
            'thigh_circumference'   => get_value($info, 'thigh_circumference'),
            'drugs_used'            => get_value($info, 'drugs_used'),
            'update_user_id'        => $this->session->userdata('user_id'),
            'update_time'           => time()
        );
        $where = array('id'=>$id);
        $this->db->update($this->table, $data, $where);
        return $this->create_result(true, 0, $where);
    }


    public function updateSale($id, $info) {
        $data = array(
            'custId'            => get_value($info, 'custId'),
            'update_user_id'    => $this->session->userdata('user_id'),
            'update_time'       => time()
        );
        $where = array('id'=>$id);
        $this->db->update($this->table, $data, $where);
        return $this->create_result(true, 0, $where);
    }


    public function getAllUser() {
        $query = $this->db->select($this->fields)->get($this->table);
        if($query->num_rows()<=0) {
            return array();
        }
        $result = $query->result_array();
        return $result;
    }


    public function getCustomerTree() {
        $customers = $this->getAllUser();
        $customerTree = '<option value="0">---请选择---</option>';
        foreach($customers as $k=>$v) {
            $customerTree .= '<option value="' . $v['id'] . '">' . $v['phone'] . '(' . $v['name'] . ')</option>';
        }
        return $customerTree;
    }


    public function getCustomerJSON() {
        $customers = $this->getAllUser();
        $customerArr = array();
        foreach($customers as $k=>$v) {
            $customerArr[] = ['name'=>$v['phone'].'('.$v['name'].')', 'id'=>$v['id']];
        }
        return $customerArr;
    }
}
