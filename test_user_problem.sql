/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50553
Source Host           : localhost:3306
Source Database       : onecode

Target Server Type    : MYSQL
Target Server Version : 50553
File Encoding         : 65001

Date: 2017-10-02 00:11:41
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for test_user_problem
-- ----------------------------
DROP TABLE IF EXISTS `test_user_problem`;
CREATE TABLE `test_user_problem` (
  `id` int(6) NOT NULL AUTO_INCREMENT,
  `user_id` int(6) NOT NULL,
  `problem_id` int(6) NOT NULL,
  `submit_time` int(11) NOT NULL,
  `judge_status` int(1) NOT NULL,
  `exe_time` int(6) NOT NULL DEFAULT '0',
  `exe_memory` int(6) NOT NULL,
  `code_len` int(6) NOT NULL,
  `language` varchar(30) NOT NULL,
  `nickname` varchar(30) NOT NULL,
  `filepath` text NOT NULL,
  `judge_results` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8;
