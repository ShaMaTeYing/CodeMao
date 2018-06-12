/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50553
Source Host           : localhost:3306
Source Database       : onecode

Target Server Type    : MYSQL
Target Server Version : 50553
File Encoding         : 65001

Date: 2017-10-02 00:11:27
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for test_judge_detail
-- ----------------------------
DROP TABLE IF EXISTS `test_judge_detail`;
CREATE TABLE `test_judge_detail` (
  `id` int(9) unsigned NOT NULL AUTO_INCREMENT,
  `user_problem_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `judge_status` int(11) NOT NULL DEFAULT '8',
  `exe_time` int(11) NOT NULL DEFAULT '0',
  `exe_memory` int(11) NOT NULL DEFAULT '0',
  `score` int(11) NOT NULL DEFAULT '0',
  `input_file_path` varchar(255) NOT NULL,
  `output_file_path` varchar(255) NOT NULL,
  `group_score` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=331 DEFAULT CHARSET=utf8;
