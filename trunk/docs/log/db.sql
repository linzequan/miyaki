-- linzequan 20151230
-- 添加接待登记信息表
create table if not exists `recept_regist` (
    `id` int not null auto_increment comment '自增id',
    `name` varchar(32) not null comment '姓名',
    `age` int(3) comment '年龄',
    `sex` tinyint(1) default 0 comment '性别。0未知，1男，2女',
    `phone` varchar(16) default '' comment '电话',
    `address` varchar(128) default '' comment '地址',
    `member_count` int(3) comment '家庭成员数量',
    `remarks` varchar(2048) default '' comment '备注',
    `height` varchar(16) default '' comment '身高',
    `weight` varchar(16) default '' comment '体重',
    `blood_pressure` varchar(16) default '' comment '血压',
    `blood_sugar` varchar(16) default '' comment '血糖',
    `fat_percent` varchar(16) default '' comment '脂肪率',
    `BNI` varchar(16) default '' comment 'BNI',
    `visceral_fat` int(2) comment '内脏脂肪',
    `basal_metabolism` varchar(1024) default '' comment '基础代谢',
    `body_age` int(3) comment '身体年龄',
    `waistline` varchar(16) default '' comment '腰围',
    `hipline` varchar(16) default '' comment '臀围',
    `thigh_circumference` varchar(16) default '' comment '大腿围',
    `manage_aim` varchar(256) default '' comment '管理目标',
    `health_status` varchar(2048) default '' comment '健康现状描述（病症）',
    `drugs_used` varchar(2048) default '' comment '所用药物或其他（药物名称、用量）',
    `member_status` varchar(2048) default '' comment '家庭成员现状',
    primary key (`id`)
) engine = myisam character set utf8 collate utf8_general_ci comment = '接待登记信息表';


-- linzequan 20160102
-- 接待登记信息表添加创建时间及创建用户字段信息
alter table `recept_regist` add `create_user_id` int comment '创建用户id', add `create_time` int(11) comment '创建时间戳', add `update_user_id` int comment '更新用户id', add `update_time` int(11) comment '更新时间戳';


-- linzequan 20160105
-- 添加体检登记信息表
create table if not exists `exam_regist` (
    `id` int not null auto_increment comment '自增id',
    `name` varchar(128) not null comment '体检名称',
    `result` text comment '结果',
    `upper_limit` text comment '参考上限',
    `lower_limit` text comment '参考下限',
    `create_user_id` int comment '创建用户id',
    `create_time` int(11) comment '创建时间戳',
    `update_user_id` int comment '更新用户id',
    `update_time` int(11) comment '更新时间戳',
    primary key (`id`)
) engine = myisam character set utf8 collate utf8_general_ci comment = '体检登记信息表';


-- linzequan 20160106
-- 添加分店树信息表
create table if not exists `branch_tree` (
    `id` int not null auto_increment comment '自增id',
    `name` varchar(128) not null comment '名称',
    `pid` int not null comment '父id，根节点为0',
    `create_user_id` int comment '创建用户id',
    `create_time` int(11) comment '创建时间戳',
    `update_user_id` int comment '更新用户id',
    `update_time` int(11) comment '更新时间戳',
    primary key (`id`)
) engine = myisam character set utf8 collate utf8_general_ci comment = '分店树信息表';


-- linzequan 20160106
-- 分店树信息表添加排序字段
alter table `branch_tree` add `sort` tinyint(4) default 0 comment '菜单排序';


-- linzequan 20160106
-- 系统用户表添加分店id字段
alter table `sys_user` add `bid` int comment '分店id';


-- linzequan 20160109
-- 体检登记信息表添加用户id字段
alter table `exam_regist` add `uid` int not null comment '客户id';


-- linzequan 20160109
-- 接待登记信息表添加业务员id
alter table `recept_regist` add `custId` int not null comment '业务员id';


-- linzequan 20160109
-- 接待登记信息表添加分店id
alter table `recept_regist` add `branchId` int not null comment '分店id';


-- linzequan 20160109
-- 添加售后服务跟进信息表
create table if not exists `aftersale_follow` (
    `id` int not null auto_increment comment '自增id',
    `custId` int not null comment '客户id',
    `week` int comment '第几周',
    `day` int comment '第几日',
    `weight` float default 0 comment '体重，单位kg',
    `test_paper` varchar(256) default '' comment '试纸',
    `blood_pressure` varchar(16) default '' comment '血压',
    `blood_sugar` varchar(16) default '' comment '血糖',
    `sleep` varchar(256) default '' comment '睡眠',
    `defecation` varchar(256) default '' comment '大小便',
    `zc_time` int(11) default 0 comment '早餐时间',
    `zc_food` varchar(1024) default '' comment '早餐食物',
    `zcjc_time` int(11) default 0 comment '早餐加餐时间',
    `zcjc_food` varchar(1024) default '' comment '早餐加餐食物',
    `wc_time` int(11) default 0 comment '午餐时间',
    `wc_food` varchar(1024) default '' comment '午餐食物',
    `wcjc_time` int(11)default 0 comment '午餐加餐时间',
    `wcjc_food` varchar(1024) default '' comment '午餐加餐食物',
    `wancan_time` int(11) default 0 comment '晚餐时间',
    `wancan_food` varchar(1024) default '' comment '晚餐食物',
    `wancanjc_time` int(11) default 0 comment '晚餐加餐时间',
    `wancanjc_food` varchar(1024) default '' comment '晚餐加餐食物',
    `wxc_time` int(11) default 0 comment '五行操时间',
    `wxc_duration` int default 0 comment '五行操时长，单位分钟',
    `pj_time` int(11) default 0 comment '泡脚时间',
    `other_sport` varchar(1024) default '' comment '其他运动',
    `drink` float default 0 comment '饮水，单位ml',
    `expirence` varchar(1024) default '' comment '体会',
    `create_user_id` int comment '创建用户id',
    `create_time` int(11) comment '创建时间戳',
    `update_user_id` int comment '更新用户id',
    `update_time` int(11) comment '更新时间戳',
    primary key (`id`)
) engine = myisam character set utf8 collate utf8_general_ci comment = '售后服务跟进信息表';


-- linzequan 20160113
-- 添加套餐数据表
create table if not exists `package` (
    `id` int not null auto_increment comment '自增id',
    `name` varchar(128) not null comment '套餐名称',
    `cyclic` varchar(128) comment '调理周期',
    `content` text comment '调理内容',
    `material` text comment '需要客户物资领用表（试纸、食物称、体重称、五行操、音乐）',
    `prize` float comment '价格',
    `create_user_id` int comment '创建用户id',
    `create_time` int(11) comment '创建时间戳',
    `update_user_id` int comment '更新用户id',
    `update_time` int(11) comment '更新时间戳',
    primary key (`id`)
) engine = myisam character set utf8 collate utf8_general_ci comment = '套餐数据表';


-- linzequan 20160121
-- 套餐数据表添加套餐是否可用选项
alter table `package` add `available` int(2) default 0 comment '套餐是否可用，0可用，1不可用。默认为0可用';


-- linzequan 20160121
-- 添加套餐订单数据表
create table if not exists `package_order` (
    `id` int not null auto_increment comment '自增id',
    `pid` int not null comment '套餐id',
    `uid` int not null comment '用户id',
    `custId` int not null comment '业务员id',
    `discount` int default 100 comment '折扣',
    `care_time` int(11) comment '开始调理时间',
    `food_num` int comment '餐包数量',
    `create_user_id` int comment '创建用户id',
    `create_time` int(11) comment '创建时间戳',
    `update_user_id` int comment '更新用户id',
    `update_time` int(11) comment '更新时间戳',
    primary key (`id`)
) engine = myisam character set utf8 collate utf8_general_ci comment = '套餐订单数据表';
