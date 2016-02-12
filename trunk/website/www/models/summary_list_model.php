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


    public function insert($info) {
        $data = array(
            'name'          => get_value($info, 'name'),
            'age'           => get_value($info, 'age'),
            'sex'           => get_value($info, 'sex'),
            'phone'         => get_value($info, 'phone_ext') . get_value($info, 'phone'),
            'branchId'      => get_value($info, 'branchId'),
            'summary_time'  => strtotime(get_value($info, 'summary_time')),
            'summary_item'  => get_value($info, 'summary_item'),
            'create_time'   => time()
        );
        $this->db->insert($this->table, $data);
        return $this->create_result(true, 0, array('id'=>$this->db->insert_id()));
    }
}
