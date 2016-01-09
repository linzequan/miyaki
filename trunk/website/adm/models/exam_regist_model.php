<?php
/**
 * 体检登记管理模型
 *
 * @author linzequan <lowkey361@gmail.com>
 *
 */
class exam_regist_model extends MY_Model {

    private $table = 'exam_regist';
    private $fields = 'id, name, uid, result, upper_limit, lower_limit, create_user_id, create_time, update_user_id, update_time';

    public function __construct() {
        parent::__construct();
    }


    public function search($params, $order, $page) {

        $start_time = get_value($params, 'start_time');         // 创建开始时间
        $end_time = get_value($params, 'end_time');             // 创建结束时间
        $name = get_value($params, 'name');                     // 体检名称

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

        if(count($order)==0) {
            $order[] = ' create_time desc';
        }
        $datas = $this->db->get_page($this->table, $this->fields, $where, $order, $page);
        $this->load->model('sys/user_model', 'user_model');
        $this->load->model('recept_regist_model');
        $CI = &get_instance();
        foreach($datas['rows'] as $k=>$v) {
            // 客户名称
            if($v['uid']>0) {
                $userinfo = $CI->recept_regist_model->get_info($v['uid']);
                $datas['rows'][$k]['custName'] = $userinfo['name'] . '(' . $userinfo['phone'] . ')';
            } else {
                $datas['rows'][$k]['custName'] = '';
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


    public function get_info($id) {
        if($id<0) {
            return array();
        }
        $result = array();

        // 获取客户树
        $this->load->model('recept_regist_model');
        $CI = &get_instance();
        $customerTree = $CI->recept_regist_model->getCustomerTree();

        $query = $this->db->select($this->fields)->where('id', $id)->get($this->table);
        if($query->num_rows()<=0) {
            $result['customerTree'] = $customerTree;
            return $result;
        }
        $result = $query->row_array();
        $result['customerTree'] = $customerTree;

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
                            '<td class="dv-label">体检名称: </td>' .
                            '<td>' . $rows[0]['name'] . '</td>' .
                        '</tr>';
            }
            if($rows[0]['uid']!='') {
                // 查询得到uid对应用户信息
                $this->load->model('recept_regist_model');
                $CI = &get_instance();
                $userinfo = $CI->recept_regist_model->get_info($rows[0]['uid']);
                if(count($userinfo)>0) {
                    $str .= '<tr>' .
                            '<td class="dv-label">姓名: </td>' .
                            '<td>' . $userinfo['name'] . '(' . $userinfo['phone'] . ')</td>' .
                        '</tr>';
                }
            }
            if($rows[0]['result']!='') {
                $str .= '<tr>' .
                            '<td class="dv-label">结果: </td>' .
                            '<td>' . $rows[0]['result'] . '</td>' .
                        '</tr>';
            }
            if($rows[0]['upper_limit']!='') {
                $str .= '<tr>' .
                            '<td class="dv-label">参考上限: </td>' .
                            '<td>' . $rows[0]['upper_limit'] . '</td>' .
                        '</tr>';
            }
            if($rows[0]['lower_limit']!='') {
                $str .= '<tr>' .
                            '<td class="dv-label">参考下限: </td>' .
                            '<td>' . $rows[0]['lower_limit'] . '</td>' .
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
            'name'              => get_value($info, 'name'),
            'uid'               => get_value($info, 'uid'),
            'result'            => get_value($info, 'result'),
            'upper_limit'       => get_value($info, 'upper_limit'),
            'lower_limit'       => get_value($info, 'lower_limit'),
            'create_user_id'    => $this->session->userdata('user_id'),
            'create_time'       => time()
        );
        $this->db->insert($this->table, $data);
        return $this->create_result(true, 0, array('id'=>$this->db->insert_id()));
    }


    public function update($id, $info) {
        $data = array(
            'name'              => get_value($info, 'name'),
            'uid'               => get_value($info, 'uid'),
            'result'            => get_value($info, 'result'),
            'upper_limit'       => get_value($info, 'upper_limit'),
            'lower_limit'       => get_value($info, 'lower_limit'),
            'update_user_id'    => $this->session->userdata('user_id'),
            'update_time'       => time()
        );
        $where = array('id'=>$id);
        $this->db->update($this->table, $data, $where);
        return $this->create_result(true, 0, $where);
    }
}
