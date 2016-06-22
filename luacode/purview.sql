/*
Navicat MySQL Data Transfer

Source Server         : 172.168.6.6
Source Server Version : 50622
Source Host           : 172.168.6.6:3306
Source Database       : ttq_user_center

Target Server Type    : MYSQL
Target Server Version : 50622
File Encoding         : 65001

Date: 2016-06-21 13:43:41
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `ttq_appid_list`
-- ----------------------------
DROP TABLE IF EXISTS `ttq_appid_list`;
CREATE TABLE `ttq_appid_list` (
  `id` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `appid` varchar(20) NOT NULL DEFAULT 'appid相当于项目名称',
  `appkey` varchar(20) NOT NULL COMMENT 'appid密码',
  `create_time` int(11) NOT NULL COMMENT '生产appid时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='项目appid对应关系表';

-- ----------------------------
-- Records of ttq_appid_list
-- ----------------------------

-- ----------------------------
-- Table structure for `ttq_appid_white_list`
-- ----------------------------
DROP TABLE IF EXISTS `ttq_appid_white_list`;
CREATE TABLE `ttq_appid_white_list` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `appid` char(20) NOT NULL COMMENT '项目标示或名称',
  `ip` varchar(15) NOT NULL COMMENT '项目允许访问对应的ip地址',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='项目权限表';

-- ----------------------------
-- Records of ttq_appid_white_list
-- ----------------------------
