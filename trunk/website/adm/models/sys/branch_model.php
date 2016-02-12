<?php
/**
 * 菜单树管理模型
 *
 * @author linzequan <lowkey361@gmail.com>
 *
 */
class branch_model extends MY_Model {

    private $table = 'branch_tree';
    private $fields = 'id, name, pid, sort, create_user_id, create_time, update_user_id, update_time';

    public function __construct() {
        parent::__construct();
    }


    public function search() {
        $query = $this->db->select($this->fields)->order_by('pid asc, sort asc')->get($this->table);
        $list = $query->result_array();
        $this->load->model('sys/user_model', 'user_model');
        $CI = &get_instance();
        foreach($list as $k=>$v) {
            // 创建记录用户名
            $create_user_info = $CI->user_model->get_userinfo_by_id($v['create_user_id']);
            if($create_user_info) {
                $list[$k]['create_user_name'] = $create_user_info['user_name'];
            } else {
                $list[$k]['update_user_name'] = '';
            }
            // 修改记录用户名
            $update_user_info = $CI->user_model->get_userinfo_by_id($v['update_user_id']);
            if($update_user_info) {
                $list[$k]['update_user_name'] = $update_user_info['user_name'];
            } else {
                $list[$k]['update_user_name'] = '';
            }
            // 创建时间
            $list[$k]['create_time'] = $v['create_time']=='' ? '' : date('Y-m-d H:i:s', $v['create_time']);
            // 更新时间
            $list[$k]['update_time'] = $v['update_time']=='' ? '' : date('Y-m-d H:i:s', $v['update_time']);
        }
        $tree = array();
        create_tree_list($list, $tree, 0, 0, array('id_key'=>'id', 'pid_key'=>'pid'));
        return array_values($tree);
    }


    public function insert($info) {
        $query = $this->db->where('name', $info['name'])->select('id')->get($this->table);
        $count = $query->num_rows();
        $query->free_result();
        if($count>0 && $info['name']!='') {
            return $this->create_result(false, 1, '分店名称重复');
        }
        $info['create_user_id'] = $this->session->userdata('user_id');
        $info['create_time'] = time();
        $this->db->insert($this->table, $info);
        $id = $this->db->insert_id();
        return $this->create_result(true, 0, $info);
    }


    public function update($id, $info) {
        $query = $this->db->where(array('name'=>$info['name'], 'id !='=>$id))->select('id')->get($this->table);
        $count = $query->num_rows();
        $query->free_result();
        if($count>0 && $info['name']!='') {
            return $this->create_result(false, 1, '分店名称重复');
        }
        $info['update_user_id'] = $this->session->userdata('user_id');
        $info['update_time'] = time();
        $this->db->update($this->table, $info, array('id'=>$id));
        return $this->create_result(true, 0, array('id'=>$id));
    }


    public function delete($id) {
        $this->db->delete($this->table, array('id'=>$id));
        return $this->create_result(true, 0, '删除成功');
    }


    /**
     * 检查节点是否为叶子节点
     * @param  [type]  $id 节点id
     * @return boolean     是叶子节点返回true，否则返回false
     */
    public function is_leaf($id) {
        $query = $this->db->where('pid', $id)->select('id')->get($this->table);
        $count = $query->num_rows();
        $query->free_result();
        if($count>0) {
            return false;
        } else {
            return true;
        }
    }


    public function get_name_by_id($id) {
        $query = $this->db->where('id', $id)->select('name')->get($this->table);
        $result = $query->result_array();
        if(count($result)>0) {
            return $result[0]['name'];
        } else {
            return false;
        }
    }


    /**
     * 返回分店列表
     * @return [type] [description]
     */
    public function get_list() {
        $query = $this->db->select($this->fields)->order_by('pid asc, sort asc')->get($this->table);
        $list = $query->result_array();
        $tree = array();
        create_tree_list($list, $tree, 0, 0, array('id_key'=>'id', 'pid_key'=>'pid'));
        return array_values($tree);
    }
}
