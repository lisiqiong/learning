SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `b2b_schedule`
-- ----------------------------
DROP TABLE IF EXISTS `b2b_schedule`;
CREATE TABLE `b2b_schedule` (
`id`  int(11) NOT NULL ,
`task_to_run`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '计划任务执行方法' ,
`schedule_type`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '执行频率' ,
`modifier`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '执行频率,类型为MONTHLY时必须；ONCE时无效；其他时为可选，默认为1' ,
`start_datetime`  datetime NOT NULL COMMENT '开始时间' ,
`end_datetime`  datetime NULL DEFAULT NULL COMMENT '结束时间' ,
`last_run_time`  datetime NULL DEFAULT NULL COMMENT '最近执行时间' ,
`info`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '对计划任务的简要描述' ,
`status`  tinyint(1) NULL DEFAULT 0 COMMENT '0关闭，1开启' ,
PRIMARY KEY (`id`)
)
ENGINE=MyISAM
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
COMMENT='农集网计划任务管理表'

;
