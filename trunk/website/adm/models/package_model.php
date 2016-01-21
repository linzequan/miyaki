<?php
/**
 * 套餐管理模型
 *
 * @author linzequan <lowkey361@gmail.com>
 *
 */
class package_model extends MY_Model {

    private $table = 'package';
    private $fields = 'id, name, cyclic, content, material, prize, create_user_id, create_time, update_user_id, update_time';

    public function __construct() {
        parent::__construct();
    }


    public function search($params, $order, $page) {

        $start_time = get_value($params, 'start_time');         // 创建开始时间
        $end_time = get_value($params, 'end_time');             // 创建结束时间
        $name = get_value($params, 'name');                     // 套餐名称
        $prize = get_value($params, 'prize');                   // 价格
        $available = get_value($params, 'available');           // 状态

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
        if($prize!='') {
            $where[] = array('prize', $prize);
        }
        if($available==0 || $available==1) {
            $where[] = array('available', $available);
        }

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
        if(count($rows)>0) {
            $str = '<table class="dv-table" border="0" style="width:100%;">' .
                        '<tr>' .
                            '<td class="dv-label">编号: </td>' .
                            '<td>' . $rows[0]['id'] . '</td>' .
                        '</tr>';
            if($rows[0]['name']!='') {
                $str .= '<tr>' .
                            '<td class="dv-label">套餐名称: </td>' .
                            '<td>' . $rows[0]['name'] . '</td>' .
                        '</tr>';
            }
            if($rows[0]['cyclic']!='') {
                $str .= '<tr>' .
                            '<td class="dv-label">调理周期: </td>' .
                            '<td>' . $rows[0]['cyclic'] . '</td>' .
                        '</tr>';
            }
            if($rows[0]['content']!='') {
                $str .= '<tr>' .
                            '<td class="dv-label">调理内容: </td>' .
                            '<td>' . $rows[0]['content'] . '</td>' .
                        '</tr>';
            }
            if($rows[0]['material']!='') {
                $str .= '<tr>' .
                            '<td class="dv-label">需要客户物资领用表: </td>' .
                            '<td>' . $rows[0]['material'] . '</td>' .
                        '</tr>';
            }
            if($rows[0]['prize']!='') {
                $str .= '<tr>' .
                            '<td class="dv-label">价格: </td>' .
                            '<td>' . $rows[0]['prize'] . '</td>' .
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
            'cyclic'                => get_value($info, 'cyclic'),
            'content'               => get_value($info, 'content'),
            'material'              => get_value($info, 'material'),
            'prize'                 => get_value($info, 'prize'),
            'available'             => get_value($info, 'available'),
            'create_user_id'        => $this->session->userdata('user_id'),
            'create_time'           => time()
        );
        $this->db->insert($this->table, $data);
        return $this->create_result(true, 0, array('id'=>$this->db->insert_id()));
    }


    public function update($id, $info) {
        $data = array(
            'name'                  => get_value($info, 'name'),
            'cyclic'                => get_value($info, 'cyclic'),
            'content'               => get_value($info, 'content'),
            'material'              => get_value($info, 'material'),
            'prize'                 => get_value($info, 'prize'),
            'available'             => get_value($info, 'available'),
            'update_user_id'        => $this->session->userdata('user_id'),
            'update_time'           => time()
        );
        $where = array('id'=>$id);
        $this->db->update($this->table, $data, $where);
        return $this->create_result(true, 0, $where);
    }

}
