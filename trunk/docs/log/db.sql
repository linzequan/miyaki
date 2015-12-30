-- linzequan 20151230
-- 添加接待登记信息表
create table if not exists `recept_regist` (
    `id` int not null auto_increment comment '自增id',
    `name` varchar(32) not null comment '姓名',
    `age` int(3) comment '年龄',
    `sex` tinyint(1) default 0 comment '性别。0未知，1男，2女',
    `phone` varchar(16) comment '电话',
    `address` varchar(128) comment '地址',
    `member_count` int(3) comment '家庭成员数量',
    `remarks` varchar(2048) comment '备注',
    `height` varchar(16) comment '身高',
    `weight` varchar(16) comment '体重',
    `blood_pressure` varchar(16) comment '血压',
    `blood_sugar` varchar(16) comment '血糖',
    `fat_percent` varchar(16) comment '脂肪率',
    `BNI` varchar(16) comment 'BNI',
    `visceral_fat` int(2) comment '内脏脂肪',
    `basal_metabolism` varchar(1024) comment '基础代谢',
    `body_age` int(3) comment '身体年龄',
    `waistline` varchar(16) comment '腰围',
    `hipline` varchar(16) comment '臀围',
    `thigh_circumference` varchar(16) comment '大腿围',
    `manage_aim` varchar(256) comment '管理目标',
    `health_status` varchar(2048) comment '健康现状描述（病症）',
    `drugs_used` varchar(2048) comment '所用药物或其他（药物名称、用量）',
    `member_status` varchar(2048) comment '家庭成员现状',
    primary key (`id`)
) engine = myisam character set utf8 collate utf8_general_ci comment = '接待登记信息表';
