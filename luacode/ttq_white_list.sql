/*
Navicat MySQL Data Transfer

Source Server         : 个人工作环境（nginx）
Source Server Version : 50622
Source Host           : 192.168.3.128:3306
Source Database       : test

Target Server Type    : MYSQL
Target Server Version : 50622
File Encoding         : 65001

Date: 2016-06-20 13:37:39
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `ttq_white_list`
-- ----------------------------
DROP TABLE IF EXISTS `ttq_white_list`;
CREATE TABLE `ttq_white_list` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `project_name` char(20) NOT NULL COMMENT '项目标示或名称',
  `ip` varchar(15) NOT NULL COMMENT '项目允许访问对应的ip地址',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='项目权限表';
