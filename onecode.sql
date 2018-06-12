/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50553
Source Host           : localhost:3306
Source Database       : onecode

Target Server Type    : MYSQL
Target Server Version : 50553
File Encoding         : 65001

Date: 2017-08-25 10:31:53
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for black_user
-- ----------------------------
DROP TABLE IF EXISTS `black_user`;
CREATE TABLE `black_user` (
  `id` int(5) unsigned NOT NULL AUTO_INCREMENT,
  `ip` varchar(30) CHARACTER SET utf8 NOT NULL,
  `status` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of black_user
-- ----------------------------

-- ----------------------------
-- Table structure for contest_judge
-- ----------------------------
DROP TABLE IF EXISTS `contest_judge`;
CREATE TABLE `contest_judge` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `contest_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `problem_id` int(11) DEFAULT NULL,
  `judge_status` int(11) DEFAULT NULL,
  `score` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of contest_judge
-- ----------------------------

-- ----------------------------
-- Table structure for contest_judge_detail
-- ----------------------------
DROP TABLE IF EXISTS `contest_judge_detail`;
CREATE TABLE `contest_judge_detail` (
  `id` int(9) unsigned NOT NULL AUTO_INCREMENT,
  `user_problem_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `judge_status` int(11) NOT NULL DEFAULT '8',
  `exe_time` int(11) NOT NULL DEFAULT '0',
  `exe_memory` int(11) NOT NULL DEFAULT '0',
  `score` int(11) NOT NULL DEFAULT '0',
  `input_file_path` text NOT NULL,
  `output_file_path` text NOT NULL,
  `group_score` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of contest_judge_detail
-- ----------------------------

-- ----------------------------
-- Table structure for contest_list
-- ----------------------------
DROP TABLE IF EXISTS `contest_list`;
CREATE TABLE `contest_list` (
  `id` int(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8 NOT NULL,
  `start_time` int(11) NOT NULL,
  `end_time` int(11) NOT NULL,
  `type` varchar(100) CHARACTER SET utf8 NOT NULL,
  `user_id` int(11) NOT NULL,
  `password` varchar(30) CHARACTER SET utf8 DEFAULT NULL,
  `is_visible` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of contest_list
-- ----------------------------
INSERT INTO `contest_list` VALUES ('7', 'Test', '1503384780', '1503442200', '0', '1', null, '1');

-- ----------------------------
-- Table structure for contest_problem
-- ----------------------------
DROP TABLE IF EXISTS `contest_problem`;
CREATE TABLE `contest_problem` (
  `id` int(5) unsigned NOT NULL AUTO_INCREMENT,
  `contest_id` int(5) NOT NULL,
  `title` text NOT NULL,
  `time_limit` int(7) NOT NULL DEFAULT '1000',
  `memory_limit` int(7) NOT NULL DEFAULT '32768',
  `submissions` int(7) NOT NULL DEFAULT '0',
  `accepted` int(7) NOT NULL DEFAULT '0',
  `description` text NOT NULL,
  `input` text NOT NULL,
  `output` text NOT NULL,
  `sample_input` text NOT NULL,
  `sample_output` text NOT NULL,
  `author` text NOT NULL,
  `source` text NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `problem_mark` varchar(255) NOT NULL,
  `output_limit` int(11) NOT NULL,
  `case_number` int(11) NOT NULL DEFAULT '10',
  `score` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of contest_problem
-- ----------------------------
INSERT INTO `contest_problem` VALUES ('8', '7', 'a+b', '1000', '131072', '0', '0', '计算两个整数a与b的值.', '两个整数a, b(-1e9<=a, b<=1e9).', 'a+b的值.', '1 1', '2', '吴迎', 'OneCode', '1', 'A', '256', '10', '30');
INSERT INTO `contest_problem` VALUES ('9', '7', '反转数组', '1000', '131072', '0', '0', '将数组中的元素逆序输出.', '第一行, 一个整数n(0<=n<=100).\r\n第二行, 一个数组a, 包含n个整数(-1e9<=a[I]<=1e9).', '在一行内反向输出数组的元素.', '3\r\n1 2 3', '3 2 1', '吴迎', 'OneCode', '1', 'B', '256', '10', '30');
INSERT INTO `contest_problem` VALUES ('10', '7', '打印沙漏', '1000', '131072', '0', '0', '把给定的符号打印成沙漏的形状。例如给定17个“*”，要求按下列格式打印\r\n*****\r\n ***\r\n  *\r\n ***\r\n*****\r\n所谓\"沙漏形状\", 是指每行输出奇数个符号; 各行符号中心对齐; 相邻两行符号数差2; 符号数先从大到小顺序递减到1, 再从小到大顺序递增; 首尾符号数相等.给定任意N个符号, 不一定能正好组成一个沙漏. 要求打印出的沙漏能用掉尽可能多的符号', '输入在一行给出1个正整数n(1<=n<=1000)和一个符号, 中间以空格分隔.', '首先打印由给定符号组成的最大沙漏, 然后打印剩余的字符数. 注意在每行的末尾不要打印多余的空格!', '19 *\r\n', '*****\r\n ***\r\n  *\r\n ***\r\n*****\r\n2\r\n', '吴迎', 'OneCode', '1', 'C', '256', '10', '30');

-- ----------------------------
-- Table structure for contest_user_problem
-- ----------------------------
DROP TABLE IF EXISTS `contest_user_problem`;
CREATE TABLE `contest_user_problem` (
  `id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(6) NOT NULL,
  `contest_id` int(11) NOT NULL,
  `problem_id` int(6) NOT NULL,
  `submit_time` int(11) NOT NULL,
  `judge_status` int(1) NOT NULL,
  `exe_time` varchar(6) NOT NULL DEFAULT '0',
  `exe_memory` varchar(6) NOT NULL,
  `code_len` int(6) NOT NULL,
  `language` varchar(30) NOT NULL,
  `nickname` varchar(30) NOT NULL,
  `filepath` text NOT NULL,
  `score` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of contest_user_problem
-- ----------------------------

-- ----------------------------
-- Table structure for judge_detail
-- ----------------------------
DROP TABLE IF EXISTS `judge_detail`;
CREATE TABLE `judge_detail` (
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
) ENGINE=InnoDB AUTO_INCREMENT=161 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of judge_detail
-- ----------------------------
INSERT INTO `judge_detail` VALUES ('1', '1', '1', '0', '24', '7268', '10', 'Data/Library/problems/10105/1.in', 'Data/Library/problems/10105/1.out', '10');
INSERT INTO `judge_detail` VALUES ('2', '1', '2', '0', '6', '4', '10', 'Data/Library/problems/10105/2.in', 'Data/Library/problems/10105/2.out', '10');
INSERT INTO `judge_detail` VALUES ('3', '1', '3', '0', '5', '4', '10', 'Data/Library/problems/10105/3.in', 'Data/Library/problems/10105/3.out', '10');
INSERT INTO `judge_detail` VALUES ('4', '1', '4', '0', '4', '4', '10', 'Data/Library/problems/10105/4.in', 'Data/Library/problems/10105/4.out', '10');
INSERT INTO `judge_detail` VALUES ('5', '1', '5', '0', '5', '4', '10', 'Data/Library/problems/10105/5.in', 'Data/Library/problems/10105/5.out', '10');
INSERT INTO `judge_detail` VALUES ('6', '1', '6', '0', '7', '4', '10', 'Data/Library/problems/10105/6.in', 'Data/Library/problems/10105/6.out', '10');
INSERT INTO `judge_detail` VALUES ('7', '1', '7', '0', '7', '4', '10', 'Data/Library/problems/10105/7.in', 'Data/Library/problems/10105/7.out', '10');
INSERT INTO `judge_detail` VALUES ('8', '1', '8', '0', '6', '4', '10', 'Data/Library/problems/10105/8.in', 'Data/Library/problems/10105/8.out', '10');
INSERT INTO `judge_detail` VALUES ('9', '1', '9', '0', '5', '4', '10', 'Data/Library/problems/10105/9.in', 'Data/Library/problems/10105/9.out', '10');
INSERT INTO `judge_detail` VALUES ('10', '1', '10', '0', '4', '4', '10', 'Data/Library/problems/10105/10.in', 'Data/Library/problems/10105/10.out', '10');
INSERT INTO `judge_detail` VALUES ('11', '2', '1', '1', '4', '4', '0', 'Data/Library/problems/10106/1.in', 'Data/Library/problems/10106/1.out', '10');
INSERT INTO `judge_detail` VALUES ('12', '2', '2', '4', '361', '360', '0', 'Data/Library/problems/10106/2.in', 'Data/Library/problems/10106/2.out', '10');
INSERT INTO `judge_detail` VALUES ('13', '2', '3', '4', '251', '360', '0', 'Data/Library/problems/10106/3.in', 'Data/Library/problems/10106/3.out', '10');
INSERT INTO `judge_detail` VALUES ('14', '2', '4', '0', '7', '4', '0', 'Data/Library/problems/10106/4.in', 'Data/Library/problems/10106/4.out', '10');
INSERT INTO `judge_detail` VALUES ('15', '2', '5', '4', '245', '364', '0', 'Data/Library/problems/10106/5.in', 'Data/Library/problems/10106/5.out', '10');
INSERT INTO `judge_detail` VALUES ('16', '2', '6', '4', '450', '364', '0', 'Data/Library/problems/10106/6.in', 'Data/Library/problems/10106/6.out', '10');
INSERT INTO `judge_detail` VALUES ('17', '2', '7', '4', '354', '360', '0', 'Data/Library/problems/10106/7.in', 'Data/Library/problems/10106/7.out', '10');
INSERT INTO `judge_detail` VALUES ('18', '2', '8', '4', '437', '360', '0', 'Data/Library/problems/10106/8.in', 'Data/Library/problems/10106/8.out', '10');
INSERT INTO `judge_detail` VALUES ('19', '2', '9', '4', '372', '364', '0', 'Data/Library/problems/10106/9.in', 'Data/Library/problems/10106/9.out', '10');
INSERT INTO `judge_detail` VALUES ('20', '2', '10', '1', '6', '4', '0', 'Data/Library/problems/10106/10.in', 'Data/Library/problems/10106/10.out', '10');
INSERT INTO `judge_detail` VALUES ('21', '3', '1', '0', '5', '4', '0', 'Data/Library/problems/10109/1.in', 'Data/Library/problems/10109/1.out', '10');
INSERT INTO `judge_detail` VALUES ('22', '3', '2', '0', '5', '4', '0', 'Data/Library/problems/10109/2.in', 'Data/Library/problems/10109/2.out', '10');
INSERT INTO `judge_detail` VALUES ('23', '3', '3', '0', '3', '4', '0', 'Data/Library/problems/10109/3.in', 'Data/Library/problems/10109/3.out', '10');
INSERT INTO `judge_detail` VALUES ('24', '3', '4', '0', '3', '4', '0', 'Data/Library/problems/10109/4.in', 'Data/Library/problems/10109/4.out', '10');
INSERT INTO `judge_detail` VALUES ('25', '3', '5', '0', '6', '4', '0', 'Data/Library/problems/10109/5.in', 'Data/Library/problems/10109/5.out', '10');
INSERT INTO `judge_detail` VALUES ('26', '3', '6', '1', '3', '4', '0', 'Data/Library/problems/10109/6.in', 'Data/Library/problems/10109/6.out', '10');
INSERT INTO `judge_detail` VALUES ('27', '3', '7', '0', '3', '4', '0', 'Data/Library/problems/10109/7.in', 'Data/Library/problems/10109/7.out', '10');
INSERT INTO `judge_detail` VALUES ('28', '3', '8', '0', '3', '4', '0', 'Data/Library/problems/10109/8.in', 'Data/Library/problems/10109/8.out', '10');
INSERT INTO `judge_detail` VALUES ('29', '3', '9', '1', '3', '4', '0', 'Data/Library/problems/10109/9.in', 'Data/Library/problems/10109/9.out', '10');
INSERT INTO `judge_detail` VALUES ('30', '3', '10', '1', '3', '4', '0', 'Data/Library/problems/10109/10.in', 'Data/Library/problems/10109/10.out', '10');
INSERT INTO `judge_detail` VALUES ('31', '4', '1', '1', '8', '1068', '0', 'Data/Library/problems/10106/1.in', 'Data/Library/problems/10106/1.out', '10');
INSERT INTO `judge_detail` VALUES ('32', '4', '2', '0', '6', '4', '10', 'Data/Library/problems/10106/2.in', 'Data/Library/problems/10106/2.out', '10');
INSERT INTO `judge_detail` VALUES ('33', '4', '3', '1', '132', '1336', '0', 'Data/Library/problems/10106/3.in', 'Data/Library/problems/10106/3.out', '10');
INSERT INTO `judge_detail` VALUES ('34', '4', '4', '0', '6', '4', '10', 'Data/Library/problems/10106/4.in', 'Data/Library/problems/10106/4.out', '10');
INSERT INTO `judge_detail` VALUES ('35', '4', '5', '1', '36', '1068', '0', 'Data/Library/problems/10106/5.in', 'Data/Library/problems/10106/5.out', '10');
INSERT INTO `judge_detail` VALUES ('36', '4', '6', '1', '15', '1068', '0', 'Data/Library/problems/10106/6.in', 'Data/Library/problems/10106/6.out', '10');
INSERT INTO `judge_detail` VALUES ('37', '4', '7', '1', '27', '1072', '0', 'Data/Library/problems/10106/7.in', 'Data/Library/problems/10106/7.out', '10');
INSERT INTO `judge_detail` VALUES ('38', '4', '8', '1', '16', '1072', '0', 'Data/Library/problems/10106/8.in', 'Data/Library/problems/10106/8.out', '10');
INSERT INTO `judge_detail` VALUES ('39', '4', '9', '1', '20', '1072', '0', 'Data/Library/problems/10106/9.in', 'Data/Library/problems/10106/9.out', '10');
INSERT INTO `judge_detail` VALUES ('40', '4', '10', '1', '6', '4', '0', 'Data/Library/problems/10106/10.in', 'Data/Library/problems/10106/10.out', '10');
INSERT INTO `judge_detail` VALUES ('41', '5', '1', '0', '6', '4', '0', 'Data/Library/problems/10109/1.in', 'Data/Library/problems/10109/1.out', '10');
INSERT INTO `judge_detail` VALUES ('42', '5', '2', '0', '7', '4', '0', 'Data/Library/problems/10109/2.in', 'Data/Library/problems/10109/2.out', '10');
INSERT INTO `judge_detail` VALUES ('43', '5', '3', '0', '3', '4', '0', 'Data/Library/problems/10109/3.in', 'Data/Library/problems/10109/3.out', '10');
INSERT INTO `judge_detail` VALUES ('44', '5', '4', '0', '7', '4', '0', 'Data/Library/problems/10109/4.in', 'Data/Library/problems/10109/4.out', '10');
INSERT INTO `judge_detail` VALUES ('45', '5', '5', '0', '3', '4', '0', 'Data/Library/problems/10109/5.in', 'Data/Library/problems/10109/5.out', '10');
INSERT INTO `judge_detail` VALUES ('46', '5', '6', '0', '3', '4', '0', 'Data/Library/problems/10109/6.in', 'Data/Library/problems/10109/6.out', '10');
INSERT INTO `judge_detail` VALUES ('47', '5', '7', '0', '3', '4', '0', 'Data/Library/problems/10109/7.in', 'Data/Library/problems/10109/7.out', '10');
INSERT INTO `judge_detail` VALUES ('48', '5', '8', '0', '3', '4', '0', 'Data/Library/problems/10109/8.in', 'Data/Library/problems/10109/8.out', '10');
INSERT INTO `judge_detail` VALUES ('49', '5', '9', '0', '3', '4', '0', 'Data/Library/problems/10109/9.in', 'Data/Library/problems/10109/9.out', '10');
INSERT INTO `judge_detail` VALUES ('50', '5', '10', '0', '3', '4', '0', 'Data/Library/problems/10109/10.in', 'Data/Library/problems/10109/10.out', '10');
INSERT INTO `judge_detail` VALUES ('51', '6', '1', '0', '6', '4', '10', 'Data/Library/problems/10109/1.in', 'Data/Library/problems/10109/1.out', '10');
INSERT INTO `judge_detail` VALUES ('52', '6', '2', '0', '3', '4', '10', 'Data/Library/problems/10109/2.in', 'Data/Library/problems/10109/2.out', '10');
INSERT INTO `judge_detail` VALUES ('53', '6', '3', '0', '3', '4', '10', 'Data/Library/problems/10109/3.in', 'Data/Library/problems/10109/3.out', '10');
INSERT INTO `judge_detail` VALUES ('54', '6', '4', '0', '3', '4', '10', 'Data/Library/problems/10109/4.in', 'Data/Library/problems/10109/4.out', '10');
INSERT INTO `judge_detail` VALUES ('55', '6', '5', '0', '3', '4', '10', 'Data/Library/problems/10109/5.in', 'Data/Library/problems/10109/5.out', '10');
INSERT INTO `judge_detail` VALUES ('56', '6', '6', '0', '3', '4', '10', 'Data/Library/problems/10109/6.in', 'Data/Library/problems/10109/6.out', '10');
INSERT INTO `judge_detail` VALUES ('57', '6', '7', '0', '3', '4', '10', 'Data/Library/problems/10109/7.in', 'Data/Library/problems/10109/7.out', '10');
INSERT INTO `judge_detail` VALUES ('58', '6', '8', '0', '7', '364', '10', 'Data/Library/problems/10109/8.in', 'Data/Library/problems/10109/8.out', '10');
INSERT INTO `judge_detail` VALUES ('59', '6', '9', '0', '3', '4', '10', 'Data/Library/problems/10109/9.in', 'Data/Library/problems/10109/9.out', '10');
INSERT INTO `judge_detail` VALUES ('60', '6', '10', '0', '3', '4', '10', 'Data/Library/problems/10109/10.in', 'Data/Library/problems/10109/10.out', '10');
INSERT INTO `judge_detail` VALUES ('61', '7', '1', '5', '0', '0', '0', 'Data/Library/problems/10101/1.in', 'Data/Library/problems/10101/1.out', '10');
INSERT INTO `judge_detail` VALUES ('62', '7', '2', '5', '0', '0', '0', 'Data/Library/problems/10101/2.in', 'Data/Library/problems/10101/2.out', '10');
INSERT INTO `judge_detail` VALUES ('63', '7', '3', '5', '0', '0', '0', 'Data/Library/problems/10101/3.in', 'Data/Library/problems/10101/3.out', '10');
INSERT INTO `judge_detail` VALUES ('64', '7', '4', '5', '0', '0', '0', 'Data/Library/problems/10101/4.in', 'Data/Library/problems/10101/4.out', '10');
INSERT INTO `judge_detail` VALUES ('65', '7', '5', '5', '0', '0', '0', 'Data/Library/problems/10101/5.in', 'Data/Library/problems/10101/5.out', '10');
INSERT INTO `judge_detail` VALUES ('66', '7', '6', '5', '0', '0', '0', 'Data/Library/problems/10101/6.in', 'Data/Library/problems/10101/6.out', '10');
INSERT INTO `judge_detail` VALUES ('67', '7', '7', '5', '0', '0', '0', 'Data/Library/problems/10101/7.in', 'Data/Library/problems/10101/7.out', '10');
INSERT INTO `judge_detail` VALUES ('68', '7', '8', '5', '0', '0', '0', 'Data/Library/problems/10101/8.in', 'Data/Library/problems/10101/8.out', '10');
INSERT INTO `judge_detail` VALUES ('69', '7', '9', '5', '0', '0', '0', 'Data/Library/problems/10101/9.in', 'Data/Library/problems/10101/9.out', '10');
INSERT INTO `judge_detail` VALUES ('70', '7', '10', '5', '0', '0', '0', 'Data/Library/problems/10101/10.in', 'Data/Library/problems/10101/10.out', '10');
INSERT INTO `judge_detail` VALUES ('71', '8', '1', '0', '7', '360', '10', 'Data/Library/problems/10104/1.in', 'Data/Library/problems/10104/1.out', '10');
INSERT INTO `judge_detail` VALUES ('72', '8', '2', '0', '7', '360', '10', 'Data/Library/problems/10104/2.in', 'Data/Library/problems/10104/2.out', '10');
INSERT INTO `judge_detail` VALUES ('73', '8', '3', '1', '6', '360', '0', 'Data/Library/problems/10104/3.in', 'Data/Library/problems/10104/3.out', '10');
INSERT INTO `judge_detail` VALUES ('74', '8', '4', '1', '6', '360', '0', 'Data/Library/problems/10104/4.in', 'Data/Library/problems/10104/4.out', '10');
INSERT INTO `judge_detail` VALUES ('75', '8', '5', '1', '7', '364', '0', 'Data/Library/problems/10104/5.in', 'Data/Library/problems/10104/5.out', '10');
INSERT INTO `judge_detail` VALUES ('76', '8', '6', '4', '338', '364', '0', 'Data/Library/problems/10104/6.in', 'Data/Library/problems/10104/6.out', '10');
INSERT INTO `judge_detail` VALUES ('77', '8', '7', '4', '254', '364', '0', 'Data/Library/problems/10104/7.in', 'Data/Library/problems/10104/7.out', '10');
INSERT INTO `judge_detail` VALUES ('78', '8', '8', '4', '247', '360', '0', 'Data/Library/problems/10104/8.in', 'Data/Library/problems/10104/8.out', '10');
INSERT INTO `judge_detail` VALUES ('79', '8', '9', '4', '415', '360', '0', 'Data/Library/problems/10104/9.in', 'Data/Library/problems/10104/9.out', '10');
INSERT INTO `judge_detail` VALUES ('80', '8', '10', '4', '369', '364', '0', 'Data/Library/problems/10104/10.in', 'Data/Library/problems/10104/10.out', '10');
INSERT INTO `judge_detail` VALUES ('81', '9', '1', '5', '0', '0', '0', 'Data/Library/problems/10111/1.in', 'Data/Library/problems/10111/1.out', '10');
INSERT INTO `judge_detail` VALUES ('82', '9', '2', '5', '0', '0', '0', 'Data/Library/problems/10111/2.in', 'Data/Library/problems/10111/2.out', '10');
INSERT INTO `judge_detail` VALUES ('83', '9', '3', '5', '0', '0', '0', 'Data/Library/problems/10111/3.in', 'Data/Library/problems/10111/3.out', '10');
INSERT INTO `judge_detail` VALUES ('84', '9', '4', '5', '0', '0', '0', 'Data/Library/problems/10111/4.in', 'Data/Library/problems/10111/4.out', '10');
INSERT INTO `judge_detail` VALUES ('85', '9', '5', '5', '0', '0', '0', 'Data/Library/problems/10111/5.in', 'Data/Library/problems/10111/5.out', '10');
INSERT INTO `judge_detail` VALUES ('86', '9', '6', '5', '0', '0', '0', 'Data/Library/problems/10111/6.in', 'Data/Library/problems/10111/6.out', '10');
INSERT INTO `judge_detail` VALUES ('87', '9', '7', '5', '0', '0', '0', 'Data/Library/problems/10111/7.in', 'Data/Library/problems/10111/7.out', '10');
INSERT INTO `judge_detail` VALUES ('88', '9', '8', '5', '0', '0', '0', 'Data/Library/problems/10111/8.in', 'Data/Library/problems/10111/8.out', '10');
INSERT INTO `judge_detail` VALUES ('89', '9', '9', '5', '0', '0', '0', 'Data/Library/problems/10111/9.in', 'Data/Library/problems/10111/9.out', '10');
INSERT INTO `judge_detail` VALUES ('90', '9', '10', '5', '0', '0', '0', 'Data/Library/problems/10111/10.in', 'Data/Library/problems/10111/10.out', '10');
INSERT INTO `judge_detail` VALUES ('91', '10', '1', '1', '6', '360', '0', 'Data/Library/problems/10111/1.in', 'Data/Library/problems/10111/1.out', '10');
INSERT INTO `judge_detail` VALUES ('92', '10', '2', '4', '406', '364', '0', 'Data/Library/problems/10111/2.in', 'Data/Library/problems/10111/2.out', '10');
INSERT INTO `judge_detail` VALUES ('93', '10', '3', '4', '416', '360', '0', 'Data/Library/problems/10111/3.in', 'Data/Library/problems/10111/3.out', '10');
INSERT INTO `judge_detail` VALUES ('94', '10', '4', '4', '371', '364', '0', 'Data/Library/problems/10111/4.in', 'Data/Library/problems/10111/4.out', '10');
INSERT INTO `judge_detail` VALUES ('95', '10', '5', '0', '4', '360', '10', 'Data/Library/problems/10111/5.in', 'Data/Library/problems/10111/5.out', '10');
INSERT INTO `judge_detail` VALUES ('96', '10', '6', '1', '7', '360', '0', 'Data/Library/problems/10111/6.in', 'Data/Library/problems/10111/6.out', '10');
INSERT INTO `judge_detail` VALUES ('97', '10', '7', '0', '6', '360', '10', 'Data/Library/problems/10111/7.in', 'Data/Library/problems/10111/7.out', '10');
INSERT INTO `judge_detail` VALUES ('98', '10', '8', '4', '426', '364', '0', 'Data/Library/problems/10111/8.in', 'Data/Library/problems/10111/8.out', '10');
INSERT INTO `judge_detail` VALUES ('99', '10', '9', '0', '4', '364', '10', 'Data/Library/problems/10111/9.in', 'Data/Library/problems/10111/9.out', '10');
INSERT INTO `judge_detail` VALUES ('100', '10', '10', '1', '6', '364', '0', 'Data/Library/problems/10111/10.in', 'Data/Library/problems/10111/10.out', '10');
INSERT INTO `judge_detail` VALUES ('101', '11', '1', '1', '6', '4', '0', 'Data/Library/problems/10111/1.in', 'Data/Library/problems/10111/1.out', '10');
INSERT INTO `judge_detail` VALUES ('102', '11', '2', '4', '426', '364', '0', 'Data/Library/problems/10111/2.in', 'Data/Library/problems/10111/2.out', '10');
INSERT INTO `judge_detail` VALUES ('103', '11', '3', '4', '459', '364', '0', 'Data/Library/problems/10111/3.in', 'Data/Library/problems/10111/3.out', '10');
INSERT INTO `judge_detail` VALUES ('104', '11', '4', '4', '444', '364', '0', 'Data/Library/problems/10111/4.in', 'Data/Library/problems/10111/4.out', '10');
INSERT INTO `judge_detail` VALUES ('105', '11', '5', '0', '4', '4', '10', 'Data/Library/problems/10111/5.in', 'Data/Library/problems/10111/5.out', '10');
INSERT INTO `judge_detail` VALUES ('106', '11', '6', '1', '7', '4', '0', 'Data/Library/problems/10111/6.in', 'Data/Library/problems/10111/6.out', '10');
INSERT INTO `judge_detail` VALUES ('107', '11', '7', '0', '5', '4', '10', 'Data/Library/problems/10111/7.in', 'Data/Library/problems/10111/7.out', '10');
INSERT INTO `judge_detail` VALUES ('108', '11', '8', '4', '414', '364', '0', 'Data/Library/problems/10111/8.in', 'Data/Library/problems/10111/8.out', '10');
INSERT INTO `judge_detail` VALUES ('109', '11', '9', '0', '6', '4', '10', 'Data/Library/problems/10111/9.in', 'Data/Library/problems/10111/9.out', '10');
INSERT INTO `judge_detail` VALUES ('110', '11', '10', '1', '7', '364', '0', 'Data/Library/problems/10111/10.in', 'Data/Library/problems/10111/10.out', '10');
INSERT INTO `judge_detail` VALUES ('111', '12', '1', '0', '9', '1068', '10', 'Data/Library/problems/10109/1.in', 'Data/Library/problems/10109/1.out', '10');
INSERT INTO `judge_detail` VALUES ('112', '12', '2', '0', '7', '1068', '10', 'Data/Library/problems/10109/2.in', 'Data/Library/problems/10109/2.out', '10');
INSERT INTO `judge_detail` VALUES ('113', '12', '3', '0', '7', '1072', '10', 'Data/Library/problems/10109/3.in', 'Data/Library/problems/10109/3.out', '10');
INSERT INTO `judge_detail` VALUES ('114', '12', '4', '0', '8', '1068', '10', 'Data/Library/problems/10109/4.in', 'Data/Library/problems/10109/4.out', '10');
INSERT INTO `judge_detail` VALUES ('115', '12', '5', '0', '6', '1072', '10', 'Data/Library/problems/10109/5.in', 'Data/Library/problems/10109/5.out', '10');
INSERT INTO `judge_detail` VALUES ('116', '12', '6', '1', '7', '1072', '0', 'Data/Library/problems/10109/6.in', 'Data/Library/problems/10109/6.out', '10');
INSERT INTO `judge_detail` VALUES ('117', '12', '7', '0', '8', '1072', '10', 'Data/Library/problems/10109/7.in', 'Data/Library/problems/10109/7.out', '10');
INSERT INTO `judge_detail` VALUES ('118', '12', '8', '0', '7', '1068', '10', 'Data/Library/problems/10109/8.in', 'Data/Library/problems/10109/8.out', '10');
INSERT INTO `judge_detail` VALUES ('119', '12', '9', '1', '7', '1072', '0', 'Data/Library/problems/10109/9.in', 'Data/Library/problems/10109/9.out', '10');
INSERT INTO `judge_detail` VALUES ('120', '12', '10', '1', '7', '1072', '0', 'Data/Library/problems/10109/10.in', 'Data/Library/problems/10109/10.out', '10');
INSERT INTO `judge_detail` VALUES ('121', '13', '1', '0', '5', '4', '0', 'Data/Library/problems/10109/1.in', 'Data/Library/problems/10109/1.out', '10');
INSERT INTO `judge_detail` VALUES ('122', '13', '2', '0', '5', '4', '0', 'Data/Library/problems/10109/2.in', 'Data/Library/problems/10109/2.out', '10');
INSERT INTO `judge_detail` VALUES ('123', '13', '3', '0', '6', '4', '0', 'Data/Library/problems/10109/3.in', 'Data/Library/problems/10109/3.out', '10');
INSERT INTO `judge_detail` VALUES ('124', '13', '4', '0', '7', '4', '0', 'Data/Library/problems/10109/4.in', 'Data/Library/problems/10109/4.out', '10');
INSERT INTO `judge_detail` VALUES ('125', '13', '5', '0', '6', '4', '0', 'Data/Library/problems/10109/5.in', 'Data/Library/problems/10109/5.out', '10');
INSERT INTO `judge_detail` VALUES ('126', '13', '6', '0', '4', '4', '0', 'Data/Library/problems/10109/6.in', 'Data/Library/problems/10109/6.out', '10');
INSERT INTO `judge_detail` VALUES ('127', '13', '7', '0', '7', '4', '0', 'Data/Library/problems/10109/7.in', 'Data/Library/problems/10109/7.out', '10');
INSERT INTO `judge_detail` VALUES ('128', '13', '8', '0', '6', '4', '0', 'Data/Library/problems/10109/8.in', 'Data/Library/problems/10109/8.out', '10');
INSERT INTO `judge_detail` VALUES ('129', '13', '9', '0', '7', '4', '0', 'Data/Library/problems/10109/9.in', 'Data/Library/problems/10109/9.out', '10');
INSERT INTO `judge_detail` VALUES ('130', '13', '10', '0', '7', '4', '0', 'Data/Library/problems/10109/10.in', 'Data/Library/problems/10109/10.out', '10');
INSERT INTO `judge_detail` VALUES ('131', '14', '1', '5', '0', '0', '0', 'Data/Library/problems/10111/1.in', 'Data/Library/problems/10111/1.out', '10');
INSERT INTO `judge_detail` VALUES ('132', '14', '2', '5', '0', '0', '0', 'Data/Library/problems/10111/2.in', 'Data/Library/problems/10111/2.out', '10');
INSERT INTO `judge_detail` VALUES ('133', '14', '3', '5', '0', '0', '0', 'Data/Library/problems/10111/3.in', 'Data/Library/problems/10111/3.out', '10');
INSERT INTO `judge_detail` VALUES ('134', '14', '4', '5', '0', '0', '0', 'Data/Library/problems/10111/4.in', 'Data/Library/problems/10111/4.out', '10');
INSERT INTO `judge_detail` VALUES ('135', '14', '5', '5', '0', '0', '0', 'Data/Library/problems/10111/5.in', 'Data/Library/problems/10111/5.out', '10');
INSERT INTO `judge_detail` VALUES ('136', '14', '6', '5', '0', '0', '0', 'Data/Library/problems/10111/6.in', 'Data/Library/problems/10111/6.out', '10');
INSERT INTO `judge_detail` VALUES ('137', '14', '7', '5', '0', '0', '0', 'Data/Library/problems/10111/7.in', 'Data/Library/problems/10111/7.out', '10');
INSERT INTO `judge_detail` VALUES ('138', '14', '8', '5', '0', '0', '0', 'Data/Library/problems/10111/8.in', 'Data/Library/problems/10111/8.out', '10');
INSERT INTO `judge_detail` VALUES ('139', '14', '9', '5', '0', '0', '0', 'Data/Library/problems/10111/9.in', 'Data/Library/problems/10111/9.out', '10');
INSERT INTO `judge_detail` VALUES ('140', '14', '10', '5', '0', '0', '0', 'Data/Library/problems/10111/10.in', 'Data/Library/problems/10111/10.out', '10');
INSERT INTO `judge_detail` VALUES ('141', '15', '1', '1', '5', '4', '0', 'Data/Library/problems/10111/1.in', 'Data/Library/problems/10111/1.out', '10');
INSERT INTO `judge_detail` VALUES ('142', '15', '2', '1', '14', '360', '0', 'Data/Library/problems/10111/2.in', 'Data/Library/problems/10111/2.out', '10');
INSERT INTO `judge_detail` VALUES ('143', '15', '3', '1', '6', '4', '0', 'Data/Library/problems/10111/3.in', 'Data/Library/problems/10111/3.out', '10');
INSERT INTO `judge_detail` VALUES ('144', '15', '4', '1', '3', '4', '0', 'Data/Library/problems/10111/4.in', 'Data/Library/problems/10111/4.out', '10');
INSERT INTO `judge_detail` VALUES ('145', '15', '5', '0', '5', '4', '10', 'Data/Library/problems/10111/5.in', 'Data/Library/problems/10111/5.out', '10');
INSERT INTO `judge_detail` VALUES ('146', '15', '6', '1', '3', '4', '0', 'Data/Library/problems/10111/6.in', 'Data/Library/problems/10111/6.out', '10');
INSERT INTO `judge_detail` VALUES ('147', '15', '7', '0', '7', '4', '10', 'Data/Library/problems/10111/7.in', 'Data/Library/problems/10111/7.out', '10');
INSERT INTO `judge_detail` VALUES ('148', '15', '8', '4', '379', '360', '0', 'Data/Library/problems/10111/8.in', 'Data/Library/problems/10111/8.out', '10');
INSERT INTO `judge_detail` VALUES ('149', '15', '9', '0', '3', '4', '10', 'Data/Library/problems/10111/9.in', 'Data/Library/problems/10111/9.out', '10');
INSERT INTO `judge_detail` VALUES ('150', '15', '10', '1', '7', '4', '0', 'Data/Library/problems/10111/10.in', 'Data/Library/problems/10111/10.out', '10');
INSERT INTO `judge_detail` VALUES ('151', '16', '1', '8', '0', '0', '0', 'Data/Library/problems/10101/1.in', 'Data/Library/problems/10101/1.out', '10');
INSERT INTO `judge_detail` VALUES ('152', '16', '2', '8', '0', '0', '0', 'Data/Library/problems/10101/2.in', 'Data/Library/problems/10101/2.out', '10');
INSERT INTO `judge_detail` VALUES ('153', '16', '3', '8', '0', '0', '0', 'Data/Library/problems/10101/3.in', 'Data/Library/problems/10101/3.out', '10');
INSERT INTO `judge_detail` VALUES ('154', '16', '4', '8', '0', '0', '0', 'Data/Library/problems/10101/4.in', 'Data/Library/problems/10101/4.out', '10');
INSERT INTO `judge_detail` VALUES ('155', '16', '5', '8', '0', '0', '0', 'Data/Library/problems/10101/5.in', 'Data/Library/problems/10101/5.out', '10');
INSERT INTO `judge_detail` VALUES ('156', '16', '6', '8', '0', '0', '0', 'Data/Library/problems/10101/6.in', 'Data/Library/problems/10101/6.out', '10');
INSERT INTO `judge_detail` VALUES ('157', '16', '7', '8', '0', '0', '0', 'Data/Library/problems/10101/7.in', 'Data/Library/problems/10101/7.out', '10');
INSERT INTO `judge_detail` VALUES ('158', '16', '8', '8', '0', '0', '0', 'Data/Library/problems/10101/8.in', 'Data/Library/problems/10101/8.out', '10');
INSERT INTO `judge_detail` VALUES ('159', '16', '9', '8', '0', '0', '0', 'Data/Library/problems/10101/9.in', 'Data/Library/problems/10101/9.out', '10');
INSERT INTO `judge_detail` VALUES ('160', '16', '10', '8', '0', '0', '0', 'Data/Library/problems/10101/10.in', 'Data/Library/problems/10101/10.out', '10');

-- ----------------------------
-- Table structure for label_info
-- ----------------------------
DROP TABLE IF EXISTS `label_info`;
CREATE TABLE `label_info` (
  `id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `label_name` text NOT NULL,
  `status` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of label_info
-- ----------------------------
INSERT INTO `label_info` VALUES ('28', '动态规划', '0');
INSERT INTO `label_info` VALUES ('29', '贪心', '0');
INSERT INTO `label_info` VALUES ('30', '数学', '0');
INSERT INTO `label_info` VALUES ('31', '队列', '0');
INSERT INTO `label_info` VALUES ('32', '深搜', '0');
INSERT INTO `label_info` VALUES ('33', '高精度', '0');
INSERT INTO `label_info` VALUES ('34', '', '0');
INSERT INTO `label_info` VALUES ('35', '枚举', '0');
INSERT INTO `label_info` VALUES ('36', '二分', '0');
INSERT INTO `label_info` VALUES ('37', '递归', '0');
INSERT INTO `label_info` VALUES ('38', '博弈论', '0');
INSERT INTO `label_info` VALUES ('39', '栈', '0');

-- ----------------------------
-- Table structure for ladder_contest
-- ----------------------------
DROP TABLE IF EXISTS `ladder_contest`;
CREATE TABLE `ladder_contest` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `join_number` int(11) NOT NULL,
  `creat_time` int(11) NOT NULL,
  `is_visible` int(11) NOT NULL,
  `creat_user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of ladder_contest
-- ----------------------------
INSERT INTO `ladder_contest` VALUES ('1', '哈哈哈哈哈哈', '0', '1503464221', '0', '1');
INSERT INTO `ladder_contest` VALUES ('2', '第二次天梯赛', '0', '1503572082', '0', '1');

-- ----------------------------
-- Table structure for ladder_contest_problem
-- ----------------------------
DROP TABLE IF EXISTS `ladder_contest_problem`;
CREATE TABLE `ladder_contest_problem` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `contest_id` int(11) NOT NULL,
  `problem_id` int(11) NOT NULL,
  `problem_mark` varchar(255) NOT NULL,
  `is_visible` int(11) NOT NULL,
  `score` int(11) NOT NULL,
  `submissions` int(11) NOT NULL DEFAULT '0',
  `accepted` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of ladder_contest_problem
-- ----------------------------
INSERT INTO `ladder_contest_problem` VALUES ('1', '1', '10101', 'A', '0', '20', '0', '0');
INSERT INTO `ladder_contest_problem` VALUES ('2', '1', '10102', 'B', '0', '20', '0', '0');
INSERT INTO `ladder_contest_problem` VALUES ('3', '1', '10103', 'C', '0', '20', '0', '0');
INSERT INTO `ladder_contest_problem` VALUES ('4', '1', '10105', 'D', '0', '20', '0', '0');
INSERT INTO `ladder_contest_problem` VALUES ('5', '2', '10111', 'A', '0', '20', '0', '0');
INSERT INTO `ladder_contest_problem` VALUES ('6', '2', '10109', 'B', '0', '40', '0', '0');
INSERT INTO `ladder_contest_problem` VALUES ('7', '2', '10108', 'C', '0', '40', '0', '0');
INSERT INTO `ladder_contest_problem` VALUES ('8', '2', '10107', 'D', '0', '40', '0', '0');

-- ----------------------------
-- Table structure for ladder_judge_detail
-- ----------------------------
DROP TABLE IF EXISTS `ladder_judge_detail`;
CREATE TABLE `ladder_judge_detail` (
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
) ENGINE=InnoDB AUTO_INCREMENT=241 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of ladder_judge_detail
-- ----------------------------
INSERT INTO `ladder_judge_detail` VALUES ('1', '1', '1', '0', '24', '7268', '10', 'Data/Library/problems/10105/1.in', 'Data/Library/problems/10105/1.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('2', '1', '2', '0', '6', '4', '10', 'Data/Library/problems/10105/2.in', 'Data/Library/problems/10105/2.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('3', '1', '3', '0', '5', '4', '10', 'Data/Library/problems/10105/3.in', 'Data/Library/problems/10105/3.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('4', '1', '4', '0', '4', '4', '10', 'Data/Library/problems/10105/4.in', 'Data/Library/problems/10105/4.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('5', '1', '5', '0', '5', '4', '10', 'Data/Library/problems/10105/5.in', 'Data/Library/problems/10105/5.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('6', '1', '6', '0', '7', '4', '10', 'Data/Library/problems/10105/6.in', 'Data/Library/problems/10105/6.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('7', '1', '7', '0', '7', '4', '10', 'Data/Library/problems/10105/7.in', 'Data/Library/problems/10105/7.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('8', '1', '8', '0', '6', '4', '10', 'Data/Library/problems/10105/8.in', 'Data/Library/problems/10105/8.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('9', '1', '9', '0', '5', '4', '10', 'Data/Library/problems/10105/9.in', 'Data/Library/problems/10105/9.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('10', '1', '10', '0', '4', '4', '10', 'Data/Library/problems/10105/10.in', 'Data/Library/problems/10105/10.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('11', '2', '1', '1', '4', '4', '0', 'Data/Library/problems/10106/1.in', 'Data/Library/problems/10106/1.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('12', '2', '2', '4', '361', '360', '0', 'Data/Library/problems/10106/2.in', 'Data/Library/problems/10106/2.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('13', '2', '3', '4', '251', '360', '0', 'Data/Library/problems/10106/3.in', 'Data/Library/problems/10106/3.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('14', '2', '4', '0', '7', '4', '0', 'Data/Library/problems/10106/4.in', 'Data/Library/problems/10106/4.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('15', '2', '5', '4', '245', '364', '0', 'Data/Library/problems/10106/5.in', 'Data/Library/problems/10106/5.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('16', '2', '6', '4', '450', '364', '0', 'Data/Library/problems/10106/6.in', 'Data/Library/problems/10106/6.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('17', '2', '7', '4', '354', '360', '0', 'Data/Library/problems/10106/7.in', 'Data/Library/problems/10106/7.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('18', '2', '8', '4', '437', '360', '0', 'Data/Library/problems/10106/8.in', 'Data/Library/problems/10106/8.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('19', '2', '9', '4', '372', '364', '0', 'Data/Library/problems/10106/9.in', 'Data/Library/problems/10106/9.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('20', '2', '10', '1', '6', '4', '0', 'Data/Library/problems/10106/10.in', 'Data/Library/problems/10106/10.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('21', '3', '1', '0', '5', '4', '0', 'Data/Library/problems/10109/1.in', 'Data/Library/problems/10109/1.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('22', '3', '2', '0', '5', '4', '0', 'Data/Library/problems/10109/2.in', 'Data/Library/problems/10109/2.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('23', '3', '3', '0', '3', '4', '0', 'Data/Library/problems/10109/3.in', 'Data/Library/problems/10109/3.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('24', '3', '4', '0', '3', '4', '0', 'Data/Library/problems/10109/4.in', 'Data/Library/problems/10109/4.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('25', '3', '5', '0', '6', '4', '0', 'Data/Library/problems/10109/5.in', 'Data/Library/problems/10109/5.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('26', '3', '6', '1', '3', '4', '0', 'Data/Library/problems/10109/6.in', 'Data/Library/problems/10109/6.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('27', '3', '7', '0', '3', '4', '0', 'Data/Library/problems/10109/7.in', 'Data/Library/problems/10109/7.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('28', '3', '8', '0', '3', '4', '0', 'Data/Library/problems/10109/8.in', 'Data/Library/problems/10109/8.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('29', '3', '9', '1', '3', '4', '0', 'Data/Library/problems/10109/9.in', 'Data/Library/problems/10109/9.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('30', '3', '10', '1', '3', '4', '0', 'Data/Library/problems/10109/10.in', 'Data/Library/problems/10109/10.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('31', '4', '1', '1', '8', '1068', '0', 'Data/Library/problems/10106/1.in', 'Data/Library/problems/10106/1.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('32', '4', '2', '0', '6', '4', '10', 'Data/Library/problems/10106/2.in', 'Data/Library/problems/10106/2.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('33', '4', '3', '1', '132', '1336', '0', 'Data/Library/problems/10106/3.in', 'Data/Library/problems/10106/3.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('34', '4', '4', '0', '6', '4', '10', 'Data/Library/problems/10106/4.in', 'Data/Library/problems/10106/4.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('35', '4', '5', '1', '36', '1068', '0', 'Data/Library/problems/10106/5.in', 'Data/Library/problems/10106/5.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('36', '4', '6', '1', '15', '1068', '0', 'Data/Library/problems/10106/6.in', 'Data/Library/problems/10106/6.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('37', '4', '7', '1', '27', '1072', '0', 'Data/Library/problems/10106/7.in', 'Data/Library/problems/10106/7.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('38', '4', '8', '1', '16', '1072', '0', 'Data/Library/problems/10106/8.in', 'Data/Library/problems/10106/8.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('39', '4', '9', '1', '20', '1072', '0', 'Data/Library/problems/10106/9.in', 'Data/Library/problems/10106/9.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('40', '4', '10', '1', '6', '4', '0', 'Data/Library/problems/10106/10.in', 'Data/Library/problems/10106/10.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('41', '5', '1', '0', '6', '4', '0', 'Data/Library/problems/10109/1.in', 'Data/Library/problems/10109/1.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('42', '5', '2', '0', '7', '4', '0', 'Data/Library/problems/10109/2.in', 'Data/Library/problems/10109/2.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('43', '5', '3', '0', '3', '4', '0', 'Data/Library/problems/10109/3.in', 'Data/Library/problems/10109/3.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('44', '5', '4', '0', '7', '4', '0', 'Data/Library/problems/10109/4.in', 'Data/Library/problems/10109/4.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('45', '5', '5', '0', '3', '4', '0', 'Data/Library/problems/10109/5.in', 'Data/Library/problems/10109/5.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('46', '5', '6', '0', '3', '4', '0', 'Data/Library/problems/10109/6.in', 'Data/Library/problems/10109/6.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('47', '5', '7', '0', '3', '4', '0', 'Data/Library/problems/10109/7.in', 'Data/Library/problems/10109/7.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('48', '5', '8', '0', '3', '4', '0', 'Data/Library/problems/10109/8.in', 'Data/Library/problems/10109/8.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('49', '5', '9', '0', '3', '4', '0', 'Data/Library/problems/10109/9.in', 'Data/Library/problems/10109/9.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('50', '5', '10', '0', '3', '4', '0', 'Data/Library/problems/10109/10.in', 'Data/Library/problems/10109/10.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('51', '6', '1', '0', '6', '4', '10', 'Data/Library/problems/10109/1.in', 'Data/Library/problems/10109/1.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('52', '6', '2', '0', '3', '4', '10', 'Data/Library/problems/10109/2.in', 'Data/Library/problems/10109/2.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('53', '6', '3', '0', '3', '4', '10', 'Data/Library/problems/10109/3.in', 'Data/Library/problems/10109/3.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('54', '6', '4', '0', '3', '4', '10', 'Data/Library/problems/10109/4.in', 'Data/Library/problems/10109/4.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('55', '6', '5', '0', '3', '4', '10', 'Data/Library/problems/10109/5.in', 'Data/Library/problems/10109/5.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('56', '6', '6', '0', '3', '4', '10', 'Data/Library/problems/10109/6.in', 'Data/Library/problems/10109/6.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('57', '6', '7', '0', '3', '4', '10', 'Data/Library/problems/10109/7.in', 'Data/Library/problems/10109/7.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('58', '6', '8', '0', '7', '364', '10', 'Data/Library/problems/10109/8.in', 'Data/Library/problems/10109/8.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('59', '6', '9', '0', '3', '4', '10', 'Data/Library/problems/10109/9.in', 'Data/Library/problems/10109/9.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('60', '6', '10', '0', '3', '4', '10', 'Data/Library/problems/10109/10.in', 'Data/Library/problems/10109/10.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('61', '7', '1', '5', '0', '0', '0', 'Data/Library/problems/10101/1.in', 'Data/Library/problems/10101/1.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('62', '7', '2', '5', '0', '0', '0', 'Data/Library/problems/10101/2.in', 'Data/Library/problems/10101/2.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('63', '7', '3', '5', '0', '0', '0', 'Data/Library/problems/10101/3.in', 'Data/Library/problems/10101/3.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('64', '7', '4', '5', '0', '0', '0', 'Data/Library/problems/10101/4.in', 'Data/Library/problems/10101/4.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('65', '7', '5', '5', '0', '0', '0', 'Data/Library/problems/10101/5.in', 'Data/Library/problems/10101/5.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('66', '7', '6', '5', '0', '0', '0', 'Data/Library/problems/10101/6.in', 'Data/Library/problems/10101/6.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('67', '7', '7', '5', '0', '0', '0', 'Data/Library/problems/10101/7.in', 'Data/Library/problems/10101/7.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('68', '7', '8', '5', '0', '0', '0', 'Data/Library/problems/10101/8.in', 'Data/Library/problems/10101/8.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('69', '7', '9', '5', '0', '0', '0', 'Data/Library/problems/10101/9.in', 'Data/Library/problems/10101/9.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('70', '7', '10', '5', '0', '0', '0', 'Data/Library/problems/10101/10.in', 'Data/Library/problems/10101/10.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('71', '8', '1', '0', '7', '360', '10', 'Data/Library/problems/10104/1.in', 'Data/Library/problems/10104/1.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('72', '8', '2', '0', '7', '360', '10', 'Data/Library/problems/10104/2.in', 'Data/Library/problems/10104/2.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('73', '8', '3', '1', '6', '360', '0', 'Data/Library/problems/10104/3.in', 'Data/Library/problems/10104/3.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('74', '8', '4', '1', '6', '360', '0', 'Data/Library/problems/10104/4.in', 'Data/Library/problems/10104/4.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('75', '8', '5', '1', '7', '364', '0', 'Data/Library/problems/10104/5.in', 'Data/Library/problems/10104/5.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('76', '8', '6', '4', '338', '364', '0', 'Data/Library/problems/10104/6.in', 'Data/Library/problems/10104/6.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('77', '8', '7', '4', '254', '364', '0', 'Data/Library/problems/10104/7.in', 'Data/Library/problems/10104/7.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('78', '8', '8', '4', '247', '360', '0', 'Data/Library/problems/10104/8.in', 'Data/Library/problems/10104/8.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('79', '8', '9', '4', '415', '360', '0', 'Data/Library/problems/10104/9.in', 'Data/Library/problems/10104/9.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('80', '8', '10', '4', '369', '364', '0', 'Data/Library/problems/10104/10.in', 'Data/Library/problems/10104/10.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('81', '9', '1', '5', '0', '0', '0', 'Data/Library/problems/10111/1.in', 'Data/Library/problems/10111/1.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('82', '9', '2', '5', '0', '0', '0', 'Data/Library/problems/10111/2.in', 'Data/Library/problems/10111/2.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('83', '9', '3', '5', '0', '0', '0', 'Data/Library/problems/10111/3.in', 'Data/Library/problems/10111/3.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('84', '9', '4', '5', '0', '0', '0', 'Data/Library/problems/10111/4.in', 'Data/Library/problems/10111/4.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('85', '9', '5', '5', '0', '0', '0', 'Data/Library/problems/10111/5.in', 'Data/Library/problems/10111/5.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('86', '9', '6', '5', '0', '0', '0', 'Data/Library/problems/10111/6.in', 'Data/Library/problems/10111/6.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('87', '9', '7', '5', '0', '0', '0', 'Data/Library/problems/10111/7.in', 'Data/Library/problems/10111/7.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('88', '9', '8', '5', '0', '0', '0', 'Data/Library/problems/10111/8.in', 'Data/Library/problems/10111/8.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('89', '9', '9', '5', '0', '0', '0', 'Data/Library/problems/10111/9.in', 'Data/Library/problems/10111/9.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('90', '9', '10', '5', '0', '0', '0', 'Data/Library/problems/10111/10.in', 'Data/Library/problems/10111/10.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('91', '10', '1', '1', '6', '360', '0', 'Data/Library/problems/10111/1.in', 'Data/Library/problems/10111/1.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('92', '10', '2', '4', '406', '364', '0', 'Data/Library/problems/10111/2.in', 'Data/Library/problems/10111/2.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('93', '10', '3', '4', '416', '360', '0', 'Data/Library/problems/10111/3.in', 'Data/Library/problems/10111/3.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('94', '10', '4', '4', '371', '364', '0', 'Data/Library/problems/10111/4.in', 'Data/Library/problems/10111/4.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('95', '10', '5', '0', '4', '360', '10', 'Data/Library/problems/10111/5.in', 'Data/Library/problems/10111/5.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('96', '10', '6', '1', '7', '360', '0', 'Data/Library/problems/10111/6.in', 'Data/Library/problems/10111/6.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('97', '10', '7', '0', '6', '360', '10', 'Data/Library/problems/10111/7.in', 'Data/Library/problems/10111/7.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('98', '10', '8', '4', '426', '364', '0', 'Data/Library/problems/10111/8.in', 'Data/Library/problems/10111/8.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('99', '10', '9', '0', '4', '364', '10', 'Data/Library/problems/10111/9.in', 'Data/Library/problems/10111/9.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('100', '10', '10', '1', '6', '364', '0', 'Data/Library/problems/10111/10.in', 'Data/Library/problems/10111/10.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('101', '11', '1', '1', '6', '4', '0', 'Data/Library/problems/10111/1.in', 'Data/Library/problems/10111/1.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('102', '11', '2', '4', '426', '364', '0', 'Data/Library/problems/10111/2.in', 'Data/Library/problems/10111/2.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('103', '11', '3', '4', '459', '364', '0', 'Data/Library/problems/10111/3.in', 'Data/Library/problems/10111/3.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('104', '11', '4', '4', '444', '364', '0', 'Data/Library/problems/10111/4.in', 'Data/Library/problems/10111/4.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('105', '11', '5', '0', '4', '4', '10', 'Data/Library/problems/10111/5.in', 'Data/Library/problems/10111/5.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('106', '11', '6', '1', '7', '4', '0', 'Data/Library/problems/10111/6.in', 'Data/Library/problems/10111/6.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('107', '11', '7', '0', '5', '4', '10', 'Data/Library/problems/10111/7.in', 'Data/Library/problems/10111/7.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('108', '11', '8', '4', '414', '364', '0', 'Data/Library/problems/10111/8.in', 'Data/Library/problems/10111/8.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('109', '11', '9', '0', '6', '4', '10', 'Data/Library/problems/10111/9.in', 'Data/Library/problems/10111/9.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('110', '11', '10', '1', '7', '364', '0', 'Data/Library/problems/10111/10.in', 'Data/Library/problems/10111/10.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('111', '12', '1', '0', '9', '1068', '10', 'Data/Library/problems/10109/1.in', 'Data/Library/problems/10109/1.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('112', '12', '2', '0', '7', '1068', '10', 'Data/Library/problems/10109/2.in', 'Data/Library/problems/10109/2.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('113', '12', '3', '0', '7', '1072', '10', 'Data/Library/problems/10109/3.in', 'Data/Library/problems/10109/3.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('114', '12', '4', '0', '8', '1068', '10', 'Data/Library/problems/10109/4.in', 'Data/Library/problems/10109/4.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('115', '12', '5', '0', '6', '1072', '10', 'Data/Library/problems/10109/5.in', 'Data/Library/problems/10109/5.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('116', '12', '6', '1', '7', '1072', '0', 'Data/Library/problems/10109/6.in', 'Data/Library/problems/10109/6.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('117', '12', '7', '0', '8', '1072', '10', 'Data/Library/problems/10109/7.in', 'Data/Library/problems/10109/7.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('118', '12', '8', '0', '7', '1068', '10', 'Data/Library/problems/10109/8.in', 'Data/Library/problems/10109/8.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('119', '12', '9', '1', '7', '1072', '0', 'Data/Library/problems/10109/9.in', 'Data/Library/problems/10109/9.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('120', '12', '10', '1', '7', '1072', '0', 'Data/Library/problems/10109/10.in', 'Data/Library/problems/10109/10.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('121', '13', '1', '0', '5', '4', '0', 'Data/Library/problems/10109/1.in', 'Data/Library/problems/10109/1.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('122', '13', '2', '0', '5', '4', '0', 'Data/Library/problems/10109/2.in', 'Data/Library/problems/10109/2.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('123', '13', '3', '0', '6', '4', '0', 'Data/Library/problems/10109/3.in', 'Data/Library/problems/10109/3.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('124', '13', '4', '0', '7', '4', '0', 'Data/Library/problems/10109/4.in', 'Data/Library/problems/10109/4.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('125', '13', '5', '0', '6', '4', '0', 'Data/Library/problems/10109/5.in', 'Data/Library/problems/10109/5.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('126', '13', '6', '0', '4', '4', '0', 'Data/Library/problems/10109/6.in', 'Data/Library/problems/10109/6.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('127', '13', '7', '0', '7', '4', '0', 'Data/Library/problems/10109/7.in', 'Data/Library/problems/10109/7.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('128', '13', '8', '0', '6', '4', '0', 'Data/Library/problems/10109/8.in', 'Data/Library/problems/10109/8.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('129', '13', '9', '0', '7', '4', '0', 'Data/Library/problems/10109/9.in', 'Data/Library/problems/10109/9.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('130', '13', '10', '0', '7', '4', '0', 'Data/Library/problems/10109/10.in', 'Data/Library/problems/10109/10.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('131', '14', '1', '5', '0', '0', '0', 'Data/Library/problems/10111/1.in', 'Data/Library/problems/10111/1.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('132', '14', '2', '5', '0', '0', '0', 'Data/Library/problems/10111/2.in', 'Data/Library/problems/10111/2.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('133', '14', '3', '5', '0', '0', '0', 'Data/Library/problems/10111/3.in', 'Data/Library/problems/10111/3.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('134', '14', '4', '5', '0', '0', '0', 'Data/Library/problems/10111/4.in', 'Data/Library/problems/10111/4.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('135', '14', '5', '5', '0', '0', '0', 'Data/Library/problems/10111/5.in', 'Data/Library/problems/10111/5.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('136', '14', '6', '5', '0', '0', '0', 'Data/Library/problems/10111/6.in', 'Data/Library/problems/10111/6.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('137', '14', '7', '5', '0', '0', '0', 'Data/Library/problems/10111/7.in', 'Data/Library/problems/10111/7.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('138', '14', '8', '5', '0', '0', '0', 'Data/Library/problems/10111/8.in', 'Data/Library/problems/10111/8.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('139', '14', '9', '5', '0', '0', '0', 'Data/Library/problems/10111/9.in', 'Data/Library/problems/10111/9.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('140', '14', '10', '5', '0', '0', '0', 'Data/Library/problems/10111/10.in', 'Data/Library/problems/10111/10.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('141', '15', '1', '1', '5', '4', '0', 'Data/Library/problems/10111/1.in', 'Data/Library/problems/10111/1.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('142', '15', '2', '1', '14', '360', '0', 'Data/Library/problems/10111/2.in', 'Data/Library/problems/10111/2.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('143', '15', '3', '1', '6', '4', '0', 'Data/Library/problems/10111/3.in', 'Data/Library/problems/10111/3.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('144', '15', '4', '1', '3', '4', '0', 'Data/Library/problems/10111/4.in', 'Data/Library/problems/10111/4.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('145', '15', '5', '0', '5', '4', '10', 'Data/Library/problems/10111/5.in', 'Data/Library/problems/10111/5.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('146', '15', '6', '1', '3', '4', '0', 'Data/Library/problems/10111/6.in', 'Data/Library/problems/10111/6.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('147', '15', '7', '0', '7', '4', '10', 'Data/Library/problems/10111/7.in', 'Data/Library/problems/10111/7.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('148', '15', '8', '4', '379', '360', '0', 'Data/Library/problems/10111/8.in', 'Data/Library/problems/10111/8.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('149', '15', '9', '0', '3', '4', '10', 'Data/Library/problems/10111/9.in', 'Data/Library/problems/10111/9.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('150', '15', '10', '1', '7', '4', '0', 'Data/Library/problems/10111/10.in', 'Data/Library/problems/10111/10.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('151', '16', '1', '8', '0', '0', '0', 'Data/Library/problems/10101/1.in', 'Data/Library/problems/10101/1.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('152', '16', '2', '8', '0', '0', '0', 'Data/Library/problems/10101/2.in', 'Data/Library/problems/10101/2.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('153', '16', '3', '8', '0', '0', '0', 'Data/Library/problems/10101/3.in', 'Data/Library/problems/10101/3.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('154', '16', '4', '8', '0', '0', '0', 'Data/Library/problems/10101/4.in', 'Data/Library/problems/10101/4.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('155', '16', '5', '8', '0', '0', '0', 'Data/Library/problems/10101/5.in', 'Data/Library/problems/10101/5.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('156', '16', '6', '8', '0', '0', '0', 'Data/Library/problems/10101/6.in', 'Data/Library/problems/10101/6.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('157', '16', '7', '8', '0', '0', '0', 'Data/Library/problems/10101/7.in', 'Data/Library/problems/10101/7.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('158', '16', '8', '8', '0', '0', '0', 'Data/Library/problems/10101/8.in', 'Data/Library/problems/10101/8.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('159', '16', '9', '8', '0', '0', '0', 'Data/Library/problems/10101/9.in', 'Data/Library/problems/10101/9.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('160', '16', '10', '8', '0', '0', '0', 'Data/Library/problems/10101/10.in', 'Data/Library/problems/10101/10.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('161', '0', '1', '8', '0', '0', '0', 'Data/Ladder/problems/10101/1.in', 'Data/Ladder/problems/10101/1.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('162', '0', '2', '8', '0', '0', '0', 'Data/Ladder/problems/10101/2.in', 'Data/Ladder/problems/10101/2.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('163', '0', '3', '8', '0', '0', '0', 'Data/Ladder/problems/10101/3.in', 'Data/Ladder/problems/10101/3.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('164', '0', '4', '8', '0', '0', '0', 'Data/Ladder/problems/10101/4.in', 'Data/Ladder/problems/10101/4.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('165', '0', '5', '8', '0', '0', '0', 'Data/Ladder/problems/10101/5.in', 'Data/Ladder/problems/10101/5.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('166', '0', '6', '8', '0', '0', '0', 'Data/Ladder/problems/10101/6.in', 'Data/Ladder/problems/10101/6.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('167', '0', '7', '8', '0', '0', '0', 'Data/Ladder/problems/10101/7.in', 'Data/Ladder/problems/10101/7.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('168', '0', '8', '8', '0', '0', '0', 'Data/Ladder/problems/10101/8.in', 'Data/Ladder/problems/10101/8.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('169', '0', '9', '8', '0', '0', '0', 'Data/Ladder/problems/10101/9.in', 'Data/Ladder/problems/10101/9.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('170', '0', '10', '8', '0', '0', '0', 'Data/Ladder/problems/10101/10.in', 'Data/Ladder/problems/10101/10.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('171', '17', '1', '8', '0', '0', '0', 'Data/Ladder/problems/10101/1.in', 'Data/Ladder/problems/10101/1.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('172', '17', '2', '8', '0', '0', '0', 'Data/Ladder/problems/10101/2.in', 'Data/Ladder/problems/10101/2.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('173', '17', '3', '8', '0', '0', '0', 'Data/Ladder/problems/10101/3.in', 'Data/Ladder/problems/10101/3.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('174', '17', '4', '8', '0', '0', '0', 'Data/Ladder/problems/10101/4.in', 'Data/Ladder/problems/10101/4.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('175', '17', '5', '8', '0', '0', '0', 'Data/Ladder/problems/10101/5.in', 'Data/Ladder/problems/10101/5.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('176', '17', '6', '8', '0', '0', '0', 'Data/Ladder/problems/10101/6.in', 'Data/Ladder/problems/10101/6.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('177', '17', '7', '8', '0', '0', '0', 'Data/Ladder/problems/10101/7.in', 'Data/Ladder/problems/10101/7.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('178', '17', '8', '8', '0', '0', '0', 'Data/Ladder/problems/10101/8.in', 'Data/Ladder/problems/10101/8.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('179', '17', '9', '8', '0', '0', '0', 'Data/Ladder/problems/10101/9.in', 'Data/Ladder/problems/10101/9.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('180', '17', '10', '8', '0', '0', '0', 'Data/Ladder/problems/10101/10.in', 'Data/Ladder/problems/10101/10.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('181', '18', '1', '8', '0', '0', '0', 'Data/Ladder/problems/10103/1.in', 'Data/Ladder/problems/10103/1.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('182', '18', '2', '8', '0', '0', '0', 'Data/Ladder/problems/10103/2.in', 'Data/Ladder/problems/10103/2.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('183', '18', '3', '8', '0', '0', '0', 'Data/Ladder/problems/10103/3.in', 'Data/Ladder/problems/10103/3.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('184', '18', '4', '8', '0', '0', '0', 'Data/Ladder/problems/10103/4.in', 'Data/Ladder/problems/10103/4.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('185', '18', '5', '8', '0', '0', '0', 'Data/Ladder/problems/10103/5.in', 'Data/Ladder/problems/10103/5.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('186', '18', '6', '8', '0', '0', '0', 'Data/Ladder/problems/10103/6.in', 'Data/Ladder/problems/10103/6.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('187', '18', '7', '8', '0', '0', '0', 'Data/Ladder/problems/10103/7.in', 'Data/Ladder/problems/10103/7.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('188', '18', '8', '8', '0', '0', '0', 'Data/Ladder/problems/10103/8.in', 'Data/Ladder/problems/10103/8.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('189', '18', '9', '8', '0', '0', '0', 'Data/Ladder/problems/10103/9.in', 'Data/Ladder/problems/10103/9.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('190', '18', '10', '8', '0', '0', '0', 'Data/Ladder/problems/10103/10.in', 'Data/Ladder/problems/10103/10.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('191', '19', '1', '8', '0', '0', '0', 'Data/Ladder/problems/10102/1.in', 'Data/Ladder/problems/10102/1.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('192', '19', '2', '8', '0', '0', '0', 'Data/Ladder/problems/10102/2.in', 'Data/Ladder/problems/10102/2.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('193', '19', '3', '8', '0', '0', '0', 'Data/Ladder/problems/10102/3.in', 'Data/Ladder/problems/10102/3.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('194', '19', '4', '8', '0', '0', '0', 'Data/Ladder/problems/10102/4.in', 'Data/Ladder/problems/10102/4.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('195', '19', '5', '8', '0', '0', '0', 'Data/Ladder/problems/10102/5.in', 'Data/Ladder/problems/10102/5.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('196', '19', '6', '8', '0', '0', '0', 'Data/Ladder/problems/10102/6.in', 'Data/Ladder/problems/10102/6.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('197', '19', '7', '8', '0', '0', '0', 'Data/Ladder/problems/10102/7.in', 'Data/Ladder/problems/10102/7.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('198', '19', '8', '8', '0', '0', '0', 'Data/Ladder/problems/10102/8.in', 'Data/Ladder/problems/10102/8.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('199', '19', '9', '8', '0', '0', '0', 'Data/Ladder/problems/10102/9.in', 'Data/Ladder/problems/10102/9.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('200', '19', '10', '8', '0', '0', '0', 'Data/Ladder/problems/10102/10.in', 'Data/Ladder/problems/10102/10.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('201', '20', '1', '8', '0', '0', '0', 'Data/Ladder/problems/10105/1.in', 'Data/Ladder/problems/10105/1.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('202', '20', '2', '8', '0', '0', '0', 'Data/Ladder/problems/10105/2.in', 'Data/Ladder/problems/10105/2.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('203', '20', '3', '8', '0', '0', '0', 'Data/Ladder/problems/10105/3.in', 'Data/Ladder/problems/10105/3.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('204', '20', '4', '8', '0', '0', '0', 'Data/Ladder/problems/10105/4.in', 'Data/Ladder/problems/10105/4.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('205', '20', '5', '8', '0', '0', '0', 'Data/Ladder/problems/10105/5.in', 'Data/Ladder/problems/10105/5.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('206', '20', '6', '8', '0', '0', '0', 'Data/Ladder/problems/10105/6.in', 'Data/Ladder/problems/10105/6.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('207', '20', '7', '8', '0', '0', '0', 'Data/Ladder/problems/10105/7.in', 'Data/Ladder/problems/10105/7.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('208', '20', '8', '8', '0', '0', '0', 'Data/Ladder/problems/10105/8.in', 'Data/Ladder/problems/10105/8.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('209', '20', '9', '8', '0', '0', '0', 'Data/Ladder/problems/10105/9.in', 'Data/Ladder/problems/10105/9.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('210', '20', '10', '8', '0', '0', '0', 'Data/Ladder/problems/10105/10.in', 'Data/Ladder/problems/10105/10.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('211', '21', '1', '8', '0', '0', '0', 'Data/Ladder/problems/10101/1.in', 'Data/Ladder/problems/10101/1.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('212', '21', '2', '8', '0', '0', '0', 'Data/Ladder/problems/10101/2.in', 'Data/Ladder/problems/10101/2.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('213', '21', '3', '8', '0', '0', '0', 'Data/Ladder/problems/10101/3.in', 'Data/Ladder/problems/10101/3.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('214', '21', '4', '8', '0', '0', '0', 'Data/Ladder/problems/10101/4.in', 'Data/Ladder/problems/10101/4.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('215', '21', '5', '8', '0', '0', '0', 'Data/Ladder/problems/10101/5.in', 'Data/Ladder/problems/10101/5.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('216', '21', '6', '8', '0', '0', '0', 'Data/Ladder/problems/10101/6.in', 'Data/Ladder/problems/10101/6.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('217', '21', '7', '8', '0', '0', '0', 'Data/Ladder/problems/10101/7.in', 'Data/Ladder/problems/10101/7.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('218', '21', '8', '8', '0', '0', '0', 'Data/Ladder/problems/10101/8.in', 'Data/Ladder/problems/10101/8.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('219', '21', '9', '8', '0', '0', '0', 'Data/Ladder/problems/10101/9.in', 'Data/Ladder/problems/10101/9.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('220', '21', '10', '8', '0', '0', '0', 'Data/Ladder/problems/10101/10.in', 'Data/Ladder/problems/10101/10.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('221', '22', '1', '8', '0', '0', '0', 'Data/Ladder/problems/10111/1.in', 'Data/Ladder/problems/10111/1.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('222', '22', '2', '8', '0', '0', '0', 'Data/Ladder/problems/10111/2.in', 'Data/Ladder/problems/10111/2.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('223', '22', '3', '8', '0', '0', '0', 'Data/Ladder/problems/10111/3.in', 'Data/Ladder/problems/10111/3.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('224', '22', '4', '8', '0', '0', '0', 'Data/Ladder/problems/10111/4.in', 'Data/Ladder/problems/10111/4.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('225', '22', '5', '8', '0', '0', '0', 'Data/Ladder/problems/10111/5.in', 'Data/Ladder/problems/10111/5.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('226', '22', '6', '8', '0', '0', '0', 'Data/Ladder/problems/10111/6.in', 'Data/Ladder/problems/10111/6.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('227', '22', '7', '8', '0', '0', '0', 'Data/Ladder/problems/10111/7.in', 'Data/Ladder/problems/10111/7.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('228', '22', '8', '8', '0', '0', '0', 'Data/Ladder/problems/10111/8.in', 'Data/Ladder/problems/10111/8.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('229', '22', '9', '8', '0', '0', '0', 'Data/Ladder/problems/10111/9.in', 'Data/Ladder/problems/10111/9.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('230', '22', '10', '8', '0', '0', '0', 'Data/Ladder/problems/10111/10.in', 'Data/Ladder/problems/10111/10.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('231', '23', '1', '8', '0', '0', '0', 'Data/Ladder/problems/10105/1.in', 'Data/Ladder/problems/10105/1.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('232', '23', '2', '8', '0', '0', '0', 'Data/Ladder/problems/10105/2.in', 'Data/Ladder/problems/10105/2.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('233', '23', '3', '8', '0', '0', '0', 'Data/Ladder/problems/10105/3.in', 'Data/Ladder/problems/10105/3.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('234', '23', '4', '8', '0', '0', '0', 'Data/Ladder/problems/10105/4.in', 'Data/Ladder/problems/10105/4.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('235', '23', '5', '8', '0', '0', '0', 'Data/Ladder/problems/10105/5.in', 'Data/Ladder/problems/10105/5.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('236', '23', '6', '8', '0', '0', '0', 'Data/Ladder/problems/10105/6.in', 'Data/Ladder/problems/10105/6.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('237', '23', '7', '8', '0', '0', '0', 'Data/Ladder/problems/10105/7.in', 'Data/Ladder/problems/10105/7.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('238', '23', '8', '8', '0', '0', '0', 'Data/Ladder/problems/10105/8.in', 'Data/Ladder/problems/10105/8.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('239', '23', '9', '8', '0', '0', '0', 'Data/Ladder/problems/10105/9.in', 'Data/Ladder/problems/10105/9.out', '10');
INSERT INTO `ladder_judge_detail` VALUES ('240', '23', '10', '8', '0', '0', '0', 'Data/Ladder/problems/10105/10.in', 'Data/Ladder/problems/10105/10.out', '10');

-- ----------------------------
-- Table structure for ladder_user_problem
-- ----------------------------
DROP TABLE IF EXISTS `ladder_user_problem`;
CREATE TABLE `ladder_user_problem` (
  `id` int(6) NOT NULL AUTO_INCREMENT,
  `contest_id` int(11) NOT NULL,
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
  `score` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of ladder_user_problem
-- ----------------------------
INSERT INTO `ladder_user_problem` VALUES ('17', '1', '1', '10101', '1503480525', '0', '0', '0', '4', 'C++', 'OneCode', 'Data/Ladder/code/onecode/10101_1.cpp', '0', '20');
INSERT INTO `ladder_user_problem` VALUES ('18', '1', '1', '10103', '1503480551', '1', '0', '0', '12', 'C++', 'OneCode', 'Data/Ladder/code/onecode/10103_1.cpp', '0', '10');
INSERT INTO `ladder_user_problem` VALUES ('19', '1', '1', '10102', '1503496895', '2', '0', '0', '13', 'C++', 'OneCode', 'Data/Ladder/code/onecode/10102_1.cpp', '0', '3');
INSERT INTO `ladder_user_problem` VALUES ('20', '1', '10', '10105', '1503499756', '1', '0', '0', '20', 'C++', 'wuying', 'Data/Ladder/code/wuying/10105_1.cpp', '0', '0');
INSERT INTO `ladder_user_problem` VALUES ('21', '1', '10', '10101', '1503500011', '0', '0', '0', '19', 'C++', 'wuying', 'Data/Ladder/code/wuying/10101_1.cpp', '0', '40');
INSERT INTO `ladder_user_problem` VALUES ('22', '2', '1', '10111', '1503572222', '8', '0', '0', '4', 'C++', 'OneCode', 'Data/Ladder/code/onecode/10111_1.cpp', '0', '0');
INSERT INTO `ladder_user_problem` VALUES ('23', '1', '1', '10105', '1503573712', '8', '0', '0', '11', 'C++', 'OneCode', 'Data/Ladder/code/onecode/10105_1.cpp', '0', '0');

-- ----------------------------
-- Table structure for level
-- ----------------------------
DROP TABLE IF EXISTS `level`;
CREATE TABLE `level` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` text NOT NULL,
  `content` text NOT NULL,
  `entrance_title` varchar(255) NOT NULL,
  `status` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=78 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of level
-- ----------------------------

-- ----------------------------
-- Table structure for level_msg
-- ----------------------------
DROP TABLE IF EXISTS `level_msg`;
CREATE TABLE `level_msg` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `level_id` int(11) NOT NULL DEFAULT '1',
  `level_title` varchar(255) NOT NULL,
  `level_abstract` text NOT NULL,
  `pass_number` int(11) NOT NULL DEFAULT '0',
  `level_name` varchar(255) NOT NULL,
  `problem_number` int(11) NOT NULL DEFAULT '0',
  `status` int(11) NOT NULL DEFAULT '0',
  `pre_level_id` int(11) NOT NULL DEFAULT '0',
  `least_pass_number` int(11) NOT NULL DEFAULT '0',
  `priority` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=47 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of level_msg
-- ----------------------------

-- ----------------------------
-- Table structure for login_msg
-- ----------------------------
DROP TABLE IF EXISTS `login_msg`;
CREATE TABLE `login_msg` (
  `id` int(5) unsigned NOT NULL AUTO_INCREMENT,
  `ip` varchar(30) NOT NULL,
  `login_time` int(12) NOT NULL,
  `status` varchar(100) NOT NULL,
  `area` varchar(100) DEFAULT NULL,
  `username` varchar(40) NOT NULL,
  `password` varchar(40) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1607 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of login_msg
-- ----------------------------
INSERT INTO `login_msg` VALUES ('1503', '183.157.91.209', '1503123576', '登录成功', '浙江省杭州市电信', 'onecode', '199477', '1');
INSERT INTO `login_msg` VALUES ('1504', '183.157.91.209', '1503123594', '退出成功', '浙江省杭州市电信', 'onecode', '', '1');
INSERT INTO `login_msg` VALUES ('1505', '183.157.91.209', '1503123600', '密码错误', '浙江省杭州市电信', 'onecode', '199477', '1');
INSERT INTO `login_msg` VALUES ('1506', '183.157.91.209', '1503123606', '登录成功', '浙江省杭州市电信', 'onecode', 'onecode', '1');
INSERT INTO `login_msg` VALUES ('1507', '183.157.91.209', '1503123862', '登录成功', '浙江省杭州市电信', 'onecode', 'onecode', '1');
INSERT INTO `login_msg` VALUES ('1508', '183.157.91.209', '1503126561', '用户已被禁用', '浙江省杭州市电信', 'wlxsq', 'sssssssssssss', '2');
INSERT INTO `login_msg` VALUES ('1509', '183.157.91.209', '1503126603', '用户已被禁用', '浙江省杭州市电信', 'wlxsq', 'wlxsq123456', '2');
INSERT INTO `login_msg` VALUES ('1510', '183.157.91.209', '1503126616', '登录成功', '浙江省杭州市电信', 'wlxsq', 'wlxsq123456', '2');
INSERT INTO `login_msg` VALUES ('1511', '183.157.91.209', '1503137615', '登录成功', '浙江省杭州市电信', 'wlxsq', 'wlxsq123456', '2');
INSERT INTO `login_msg` VALUES ('1512', '183.157.91.209', '1503137900', '登录成功', '浙江省杭州市电信', 'onecode', 'onecode', '1');
INSERT INTO `login_msg` VALUES ('1513', '115.205.101.242', '1503140209', '密码错误', '浙江省杭州市电信', 'OneCode', '12345687', '1');
INSERT INTO `login_msg` VALUES ('1514', '115.205.101.242', '1503140570', '登录成功', '浙江省杭州市电信', 'zhihuikaicheng', '123456', '5');
INSERT INTO `login_msg` VALUES ('1515', '115.205.101.242', '1503140582', '登录成功', '浙江省杭州市电信', 'wlxsq', 'wlxsq123456', '2');
INSERT INTO `login_msg` VALUES ('1516', '115.205.101.242', '1503140698', '登录成功', '浙江省杭州市电信', 'wlxsq', 'wlxsq123456', '2');
INSERT INTO `login_msg` VALUES ('1517', '115.205.101.242', '1503141217', '退出成功', '浙江省杭州市电信', 'onecode', '', '1');
INSERT INTO `login_msg` VALUES ('1518', '115.205.101.242', '1503141219', '登录成功', '浙江省杭州市电信', 'xkchen', '199477', '4');
INSERT INTO `login_msg` VALUES ('1519', '115.205.101.242', '1503141383', '退出成功', '浙江省杭州市电信', 'xkchen', '', '4');
INSERT INTO `login_msg` VALUES ('1520', '115.205.101.242', '1503141395', '登录成功', '浙江省杭州市电信', 'onecode', 'onecode', '1');
INSERT INTO `login_msg` VALUES ('1521', '115.205.101.242', '1503149059', '密码错误', '浙江省杭州市电信', 'OneCode', 'OneCode', '1');
INSERT INTO `login_msg` VALUES ('1522', '115.205.101.242', '1503149064', '登录成功', '浙江省杭州市电信', 'onecode', 'onecode', '1');
INSERT INTO `login_msg` VALUES ('1523', '115.205.101.242', '1503149094', '退出成功', '浙江省杭州市电信', 'onecode', '', '1');
INSERT INTO `login_msg` VALUES ('1524', '115.205.101.242', '1503149099', '登录成功', '浙江省杭州市电信', 'wlxsq', 'wlxsq123456', '2');
INSERT INTO `login_msg` VALUES ('1525', '219.82.229.160', '1503150647', '登录成功', '浙江省杭州市华数宽带', 'syc', '123456', '6');
INSERT INTO `login_msg` VALUES ('1526', '219.82.229.160', '1503152169', '登录成功', '浙江省杭州市华数宽带', 'syc', '740108', '6');
INSERT INTO `login_msg` VALUES ('1527', '115.205.101.242', '1503162868', '登录成功', '浙江省杭州市电信', 'wlxsq', 'wlxsq123456', '2');
INSERT INTO `login_msg` VALUES ('1528', '115.205.101.242', '1503195902', '登录成功', '浙江省杭州市电信', 'onecode', 'onecode', '1');
INSERT INTO `login_msg` VALUES ('1529', '124.160.154.61', '1503204087', '登录成功', '浙江省嘉兴市联通ADSL', 'onecode', 'onecode', '1');
INSERT INTO `login_msg` VALUES ('1530', '124.160.154.61', '1503226616', '登录成功', '浙江省嘉兴市联通ADSL', 'onecode', 'onecode', '1');
INSERT INTO `login_msg` VALUES ('1531', '218.109.61.162', '1503272587', '登录成功', '浙江省杭州市华数宽带', 'syc', '740108', '6');
INSERT INTO `login_msg` VALUES ('1532', '115.205.101.242', '1503283377', '登录成功', '浙江省杭州市电信', 'onecode', 'onecode', '1');
INSERT INTO `login_msg` VALUES ('1533', '115.205.101.242', '1503283958', '登录成功', '浙江省杭州市电信', 'onecode', 'onecode', '1');
INSERT INTO `login_msg` VALUES ('1534', '115.205.101.242', '1503284109', '登录成功', '浙江省杭州市电信', 'wlxsq', 'wlxsq123456', '2');
INSERT INTO `login_msg` VALUES ('1535', '115.192.153.147', '1503284409', '用户已被禁用', '浙江省杭州市电信', 'LJC00147', 'zziszz', '8');
INSERT INTO `login_msg` VALUES ('1536', '115.192.153.147', '1503284488', '登录成功', '浙江省杭州市电信', 'LJC00147', 'zziszz', '8');
INSERT INTO `login_msg` VALUES ('1537', '115.192.153.147', '1503284607', '登录成功', '浙江省杭州市电信', 'LJC00147', 'zziszz', '8');
INSERT INTO `login_msg` VALUES ('1538', '115.205.101.242', '1503286539', '退出成功', '浙江省杭州市电信', 'wlxsq', '', '2');
INSERT INTO `login_msg` VALUES ('1539', '115.205.101.242', '1503286542', '密码错误', '浙江省杭州市电信', 'OneCode', 'OneCode', '1');
INSERT INTO `login_msg` VALUES ('1540', '115.205.101.242', '1503286545', '登录成功', '浙江省杭州市电信', 'onecode', 'onecode', '1');
INSERT INTO `login_msg` VALUES ('1541', '115.192.153.147', '1503288167', '登录成功', '浙江省杭州市电信', 'LJC00147', 'zhouzhuan20041112', '8');
INSERT INTO `login_msg` VALUES ('1542', '115.205.101.242', '1503289198', '登录成功', '浙江省杭州市电信', 'onecode', 'onecode', '1');
INSERT INTO `login_msg` VALUES ('1543', '115.205.101.242', '1503289340', '登录成功', '浙江省杭州市电信', 'onecode', 'onecode', '1');
INSERT INTO `login_msg` VALUES ('1544', '115.205.101.242', '1503291488', '登录成功', '浙江省杭州市电信', 'onecode', 'onecode', '1');
INSERT INTO `login_msg` VALUES ('1545', '183.150.36.198', '1503294290', '登录成功', '浙江省衢州市电信', '晴天', '123456', '7');
INSERT INTO `login_msg` VALUES ('1546', '183.150.36.198', '1503294352', '登录成功', '浙江省衢州市电信', '晴天', '123456', '7');
INSERT INTO `login_msg` VALUES ('1547', '183.150.36.198', '1503294734', '登录成功', '浙江省衢州市电信', '晴天', '123456', '7');
INSERT INTO `login_msg` VALUES ('1548', '183.150.36.198', '1503294923', '登录成功', '浙江省衢州市电信', '晴天', '123456', '7');
INSERT INTO `login_msg` VALUES ('1549', '183.150.36.198', '1503295114', '退出成功', '浙江省衢州市电信', '晴天', '', '7');
INSERT INTO `login_msg` VALUES ('1550', '183.150.36.198', '1503295116', '登录成功', '浙江省衢州市电信', '晴天', 'lyjqy5263', '7');
INSERT INTO `login_msg` VALUES ('1551', '115.205.101.242', '1503296305', '登录成功', '浙江省杭州市电信', 'onecode', 'onecode', '1');
INSERT INTO `login_msg` VALUES ('1552', '115.205.101.242', '1503298645', '登录成功', '浙江省杭州市电信', 'onecode', 'onecode', '1');
INSERT INTO `login_msg` VALUES ('1553', '115.205.101.242', '1503301551', '登录成功', '浙江省杭州市电信', 'zbs', '123456', '3');
INSERT INTO `login_msg` VALUES ('1554', '115.205.101.242', '1503301620', '退出成功', '浙江省杭州市电信', 'zbs', '', '3');
INSERT INTO `login_msg` VALUES ('1555', '115.205.101.242', '1503301712', '登录成功', '浙江省杭州市电信', 'onecode', 'onecode', '1');
INSERT INTO `login_msg` VALUES ('1556', '115.205.101.242', '1503301912', '登录成功', '浙江省杭州市电信', 'zbs', '123456', '3');
INSERT INTO `login_msg` VALUES ('1557', '115.205.101.242', '1503302491', '登录成功', '浙江省杭州市电信', 'onecode', 'onecode', '1');
INSERT INTO `login_msg` VALUES ('1558', '115.205.101.242', '1503306149', '登录成功', '浙江省杭州市电信', 'onecode', 'onecode', '1');
INSERT INTO `login_msg` VALUES ('1559', '115.205.101.242', '1503308949', '登录成功', '浙江省杭州市电信', 'onecode', 'onecode', '1');
INSERT INTO `login_msg` VALUES ('1560', '115.205.101.242', '1503311221', '登录成功', '浙江省杭州市电信', 'syc', '740108', '6');
INSERT INTO `login_msg` VALUES ('1561', '115.205.101.242', '1503314681', '登录成功', '浙江省杭州市电信', 'onecode', 'onecode', '1');
INSERT INTO `login_msg` VALUES ('1562', '115.192.153.147', '1503314781', '密码错误', '浙江省杭州市电信', 'LJC00147', 'zziszz', '8');
INSERT INTO `login_msg` VALUES ('1563', '115.192.153.147', '1503314789', '登录成功', '浙江省杭州市电信', 'LJC00147', 'zhouzhuan20041112', '8');
INSERT INTO `login_msg` VALUES ('1564', '115.205.101.242', '1503315846', '登录成功', '浙江省杭州市电信', 'onecode', 'onecode', '1');
INSERT INTO `login_msg` VALUES ('1565', '115.205.101.242', '1503317149', '登录成功', '浙江省杭州市电信', 'onecode', 'onecode', '1');
INSERT INTO `login_msg` VALUES ('1566', '115.192.153.147', '1503318580', '密码错误', '浙江省杭州市电信', 'LJC00147', 'zziszz', '8');
INSERT INTO `login_msg` VALUES ('1567', '115.192.153.147', '1503318586', '登录成功', '浙江省杭州市电信', 'LJC00147', 'zhouzhuan20041112', '8');
INSERT INTO `login_msg` VALUES ('1568', '115.205.101.242', '1503319230', '登录成功', '浙江省杭州市电信', 'onecode', 'onecode', '1');
INSERT INTO `login_msg` VALUES ('1569', '115.205.101.242', '1503319763', '登录成功', '浙江省杭州市电信', 'onecode', 'onecode', '1');
INSERT INTO `login_msg` VALUES ('1570', '115.205.101.242', '1503320799', '登录成功', '浙江省杭州市电信', 'syc', '740108', '6');
INSERT INTO `login_msg` VALUES ('1571', '115.192.153.147', '1503322136', '登录成功', '浙江省杭州市电信', 'LJC00147', 'zhouzhuan20041112', '8');
INSERT INTO `login_msg` VALUES ('1572', '115.205.101.242', '1503323494', '登录成功', '浙江省杭州市电信', 'onecode', 'onecode', '1');
INSERT INTO `login_msg` VALUES ('1573', '115.205.101.242', '1503323850', '登录成功', '浙江省杭州市电信', 'onecode', 'onecode', '1');
INSERT INTO `login_msg` VALUES ('1574', '115.205.101.242', '1503327527', '登录成功', '浙江省杭州市电信', 'onecode', 'onecode', '1');
INSERT INTO `login_msg` VALUES ('1575', '115.205.101.242', '1503333499', '登录成功', '浙江省杭州市电信', 'onecode', 'onecode', '1');
INSERT INTO `login_msg` VALUES ('1576', '115.192.153.147', '1503364243', '登录成功', '浙江省杭州市电信', 'LJC00147', 'zhouzhuan20041112', '8');
INSERT INTO `login_msg` VALUES ('1577', '115.205.101.242', '1503366739', '登录成功', '浙江省杭州市电信', 'xkchen', '199477', '4');
INSERT INTO `login_msg` VALUES ('1578', '115.205.101.242', '1503367203', '退出成功', '浙江省杭州市电信', 'xkchen', '', '4');
INSERT INTO `login_msg` VALUES ('1579', '115.205.101.242', '1503367212', '登录成功', '浙江省杭州市电信', 'wlxsq', 'wlxsq123456', '2');
INSERT INTO `login_msg` VALUES ('1580', '115.192.153.147', '1503369580', '登录成功', '浙江省杭州市电信', 'LJC00147', 'zhouzhuan20041112', '8');
INSERT INTO `login_msg` VALUES ('1581', '115.205.101.242', '1503370626', '登录成功', '浙江省杭州市电信', 'xkchen', '199477', '4');
INSERT INTO `login_msg` VALUES ('1582', '115.205.101.242', '1503372425', '登录成功', '浙江省杭州市电信', 'onecode', 'onecode', '1');
INSERT INTO `login_msg` VALUES ('1583', '115.205.101.242', '1503373366', '登录成功', '浙江省杭州市电信', 'onecode', 'onecode', '1');
INSERT INTO `login_msg` VALUES ('1584', '115.205.101.242', '1503374408', '登录成功', '浙江省杭州市电信', 'Medoric', '1234567890', '9');
INSERT INTO `login_msg` VALUES ('1585', '115.205.101.242', '1503378523', '登录成功', '浙江省杭州市电信', 'onecode', 'onecode', '1');
INSERT INTO `login_msg` VALUES ('1586', '115.205.101.242', '1503381013', '登录成功', '浙江省杭州市电信', 'onecode', 'onecode', '1');
INSERT INTO `login_msg` VALUES ('1587', '60.177.36.22', '1503383386', '登录成功', '浙江省杭州市电信ADSL', 'Medoric', '1234567890', '9');
INSERT INTO `login_msg` VALUES ('1588', '125.105.29.99', '1503383395', '登录成功', '浙江省衢州市电信', '晴天', 'lyjqy5263', '7');
INSERT INTO `login_msg` VALUES ('1589', '125.105.29.99', '1503383617', '登录成功', '浙江省衢州市电信', '晴天', 'lyjqy5263', '7');
INSERT INTO `login_msg` VALUES ('1590', '125.105.29.99', '1503385325', '退出成功', '浙江省衢州市电信', '晴天', '', '7');
INSERT INTO `login_msg` VALUES ('1591', '125.105.29.99', '1503385332', '登录成功', '浙江省衢州市电信', '晴天', 'lyjqy5263', '7');
INSERT INTO `login_msg` VALUES ('1592', '125.105.29.99', '1503385725', '退出成功', '浙江省衢州市电信', '晴天', '', '7');
INSERT INTO `login_msg` VALUES ('1593', '125.105.29.99', '1503385732', '登录成功', '浙江省衢州市电信', '晴天', 'lyjqy5263', '7');
INSERT INTO `login_msg` VALUES ('1594', '115.205.101.242', '1503385757', '登录成功', '浙江省杭州市电信', 'onecode', 'onecode', '1');
INSERT INTO `login_msg` VALUES ('1595', '60.177.36.22', '1503385777', '登录成功', '浙江省杭州市电信ADSL', 'Medoric', '1234567890', '9');
INSERT INTO `login_msg` VALUES ('1596', '0.0.0.0', '1503387395', '登录成功', 'IANA保留地址CZ88.NET', 'onecode', 'onecode', '1');
INSERT INTO `login_msg` VALUES ('1597', '0.0.0.0', '1503393656', '退出成功', 'IANA保留地址CZ88.NET', 'onecode', '', '1');
INSERT INTO `login_msg` VALUES ('1598', '0.0.0.0', '1503393670', '登录成功', 'IANA保留地址CZ88.NET', 'wlxsq', 'wlxsq123456', '2');
INSERT INTO `login_msg` VALUES ('1599', '0.0.0.0', '1503458303', '登录成功', 'IANA保留地址CZ88.NET', 'onecode', 'onecode', '1');
INSERT INTO `login_msg` VALUES ('1600', '0.0.0.0', '1503477539', '退出成功', 'IANA保留地址CZ88.NET', 'onecode', '', '1');
INSERT INTO `login_msg` VALUES ('1601', '0.0.0.0', '1503477550', '登录成功', 'IANA保留地址CZ88.NET', 'onecode', 'onecode', '1');
INSERT INTO `login_msg` VALUES ('1602', '0.0.0.0', '1503499732', '退出成功', 'IANA保留地址CZ88.NET', 'onecode', '', '1');
INSERT INTO `login_msg` VALUES ('1603', '0.0.0.0', '1503499741', '登录成功', 'IANA保留地址CZ88.NET', 'wuying', 'wuying', '10');
INSERT INTO `login_msg` VALUES ('1604', '0.0.0.0', '1503541563', '登录成功', 'IANA保留地址CZ88.NET', 'onecode', 'onecode', '1');
INSERT INTO `login_msg` VALUES ('1605', '0.0.0.0', '1503555294', '登录成功', 'IANA保留地址CZ88.NET', 'onecode', 'onecode', '1');
INSERT INTO `login_msg` VALUES ('1606', '0.0.0.0', '1503626511', '登录成功', 'IANA保留地址CZ88.NET', 'onecode', 'onecode', '1');

-- ----------------------------
-- Table structure for problem
-- ----------------------------
DROP TABLE IF EXISTS `problem`;
CREATE TABLE `problem` (
  `id` int(5) unsigned NOT NULL AUTO_INCREMENT,
  `title` text NOT NULL,
  `time_limit` int(7) NOT NULL DEFAULT '1000',
  `memory_limit` int(7) NOT NULL DEFAULT '32768',
  `submissions` int(7) NOT NULL DEFAULT '0',
  `accepted` int(7) NOT NULL DEFAULT '0',
  `description` text NOT NULL,
  `input` text NOT NULL,
  `output` text NOT NULL,
  `sample_input` text NOT NULL,
  `sample_output` text NOT NULL,
  `author` text NOT NULL,
  `source` text NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `output_limit` int(9) DEFAULT '0',
  `case_number` int(11) NOT NULL DEFAULT '10',
  `difficulty` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10123 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of problem
-- ----------------------------
INSERT INTO `problem` VALUES ('10101', '正面交锋', '1000', '32768', '0', '0', '在上次偷袭了B军大本营之后，B军非常愤怒，正式向A军宣战。\r\nA军又获取了一些情报。\r\nB军有n个士兵，每个士兵有a[i]的生命值，以及b[i]的防御力。\r\nA军一共有m个士兵，第i个士兵，需要消耗k[i]的体力，造成p[i]点伤害。\r\n当然，如果A军派出第i个士兵打在B军第j个士兵的话，会使得B军第j个士兵的生命值减少p[i]-b[j]，当然如果伤害小于防御，那么攻击就不会奏效。\r\n如果某个B军士兵的生命值降为0或以下，那么这个B军士兵就会被消灭。\r\n假设每个A军士兵都开了挂，可以无限次上阵杀敌，而且从不会受伤。\r\n请问A军最少消耗多少体力，就可以消灭所有的B军士兵。', '数据的第一行有两个整数n，m,表示B军有n个士兵，A军有m个士兵。\n接下来n行，每行两个整数，a[i],b[i]，分别表示B军每个士兵的生命值和防御力。\n再接下来m行，每行两个整数k[i]和p[i]，分别表示A军每个士兵的体力消耗和技能的伤害值。\n数据范围:\n1<=n<=100000\n1<=m<=1000\n1<=a[i]<=1000\n0<=b[i]<=10\n0<=k[i]<=100000\n0<=p[i]<=1000', '对于给出的数据，输出最小的体力消耗值，如果不能消灭所有的B军士兵，输出-1。', '1 2\r\n3 5\r\n10 7\r\n8 6', '18', 'WuYing', 'OneCode', '1', '0', '10', '2');
INSERT INTO `problem` VALUES ('10102', '偷袭大本营', '1000', '32768', '0', '0', '两军交战，现在A军要偷袭B军的大本营，根据情报显示，B军有n个士兵，每个士兵有一个防御值，其中第i个士兵的防御值是a[i]。\r\n而A军有m个士兵，每一个士兵都有一个攻击值，其中第i个士兵的攻击值是b[i]。\r\nA军的士兵能杀死B军的士兵的前提是A军的士兵的攻击值大于等于B军的士兵的防御值。B军全军覆没的情况下A军才算胜利。\r\n很显然，可能存在很多方案使得A军大获全胜，但是，为了定制合理的作战方案，A军必须找出攻击值之和最小的方案，以备下次的作战。\r\n请你编制一个程序，来找出攻击值之和最小的那种方案，如果不存在A军胜利的方案，则输出\"defeat\"。\r\n注意，一个A军士兵只能杀死一个B军士兵，且最多只能用一次。', '数据的第一行是正整数n和m(1<=n,m<=100000);接下来一行有n个数，即B军每个士兵的防御值。再接下来一行有m个数，即A军每个士兵的攻击值。士兵的攻击值和防御值均不超过int型范围。', '对于给出的数据，输出最小的攻击值之和，如果无解，则输出\"defeat\"。', '2 3\r\n5 4\r\n7 8 4', '11', 'WuYing', 'OneCode', '1', '0', '10', '2');
INSERT INTO `problem` VALUES ('10103', '智慧吴迎与智障恺成', '1000', '32768', '0', '0', '智障恺成写题时经常遇到问题，需要来请教智慧吴迎，智慧吴迎先要花上一些时间教智障恺成写出解决问题的程序，然后还要花上一些时间去执行程序得到问题的答案。\r\n现在智障恺成遇到了n个问题需要请教智慧吴迎，已知，第i个问题需要智慧吴迎花B[i]的时间教智障恺成写代码，然后智障恺成就可以立刻地无间断地执行程序，\r\n并且J[i]分钟后就可以得到答案。现在你需要帮助智慧吴迎，来选择解决问题的顺序，使得所有问题得到答案的时间尽可能地早。注意，不能同时教两道题的代码怎么写，但是可以同时运行程序。', '数据的第一行是问题的个数n(1<=n<=1000)。\r\n接下来一行有n行。每行有两个整数B和J，即写代码的时间和执行程序的时间。', '输出所有任务完成的最短时间。', '3\r\n3 3\r\n4 4\r\n5 5', '15', 'WuYing', 'OneCode', '1', '0', '10', '2');
INSERT INTO `problem` VALUES ('10104', '智障恺成分糖果', '1000', '32768', '1', '0', '圆桌旁边坐着n个小朋友，智障恺成给这n个小朋友分糖果。糖果总数是可以被n整除的，也就是说每个人可以分成一样数量的糖果，但是智障恺成分给大家的糖果数并不一致。\r\n这让分得少的小朋友非常不开心。为了公平起见，智慧吴迎小朋友们分给左右相邻的小朋友一些糖果，最终使得n个人的糖果数一样。你的任务就是求出被转手的糖果数量的最小值。\r\n比如，n=4，且4个人一开始分得1,2,5,4时，只需转移4枚金币(第3个人给第2个人两个糖果，第2个人和第4个人分别给第1个人1个糖果)即可实现每人手中的糖果数量相等。', '数据的第一行是小朋友的个数n(1<=n<=100000)。\r\n接下来的一行有n个数，分别代表每个小朋友最初分得的糖果数。每个数的范围均在int型的范围内。', '输出被转手金币数量的最小值。', '4\r\n1 2 3 4', '4', 'WuYing', 'OneCode', '1', '0', '10', '2');
INSERT INTO `problem` VALUES ('10105', '分饺子', '1000', '32768', '1', '1', '公司的早餐，每天都是饺子，这是大家最喜欢的早餐了。但是呢，现在就有一个问题了，分饺子。公司大厨文潇每天负责给大家做饺子，但是文潇给大家做饺子从来不数做多少个，他每次都是随机做m个，但是每天我们必须把饺子给吃完。那么就得把饺子分配给每一个人吃，公司共有n个人,每个人早餐至少吃一个饺子，那么这个问题就很头疼了，该怎样分饺子好呢？你能帮文潇把所有的方案数算出来么，让文潇自己挑.假设每个人没区别，即7个饺子，分成1 1 5和5 1 1和1 5 1是属于同一种方案', '输入一行，两个整数m，n,表示饺子数和n个人（1<=n<=m<=2000）', '输出一行，将m个饺子分成不超过n组的方案数,结果对4399取模.', '7 3', '4', 'wlxsq', 'OneCode', '1', '0', '10', '2');
INSERT INTO `problem` VALUES ('10106', '一码运动会', '1000', '32768', '2', '0', '一码公司要开运动会，总共n个员工，排成了一个队列，每个员工的编号代表他所属的小组，每个小组要派出至少一个员工参加第一届一码运动会，要求派出的员工在队列中连续。问最少需要多少个连续排在一起的员工才能成功举办运动会.', '输入第一行包含一个整数n(1&lt;=n&lt;=1e6),表示队列中员工的总数\r\n输入第二行包含n个整数Xi(1&lt;=Xi&lt;=1e6),表示每个员工所属的小组', '输出最少需要派出的员工数.', '5\r\n1 8 8 3 3', '4', 'wlxsq', 'OneCode', '1', '0', '10', '2');
INSERT INTO `problem` VALUES ('10107', '简单排队', '1000', '32768', '0', '0', '一码小学每天升旗，每天升旗就需要站好队形，站队这个问题是很简单，但是一个个作为少先队员，站队形就需要做到“快静齐”三个字，这才是一个优秀的少先队员。\n我们的站队规则是这样的，每个队的所有人先到操场集合，并排站好，即每个人一列，站的位置随机。当所有人站好之后，我们需要让该队的所有列站成一列，由身高站队，个子高的站在个子矮的人的后面。每个人的身高我们离散化后编好号从1到n，表示每个人的相对身高，n个人相当于n个队列，我们规定，每个人排队只能站到比他身高相对高1的人的前面，如果要移动该队列，则只能将该队列移动到比该队列身高最高个高一的前面，而且整个队列也跟着一起移动，如图所示，我们要移动345队列，我们只能将该队列移动到6的前面，我们把位置为i的队列移动到位置为j的前面，移动距离为abs(i-j),我们为了做到快静齐，现在要求排完整个队列所需要移动的最小距离。\n<img src=\"__PUBLIC__/img/10107.png\"></img>', '第一行输入一个整数n(1<=n<=10)，表示该队列的人数\r\n第二行输入n个数Xi，表示相对升高编号的人所站的位置(Xi[1,n]),身高编号唯一', '输出一行，排完整个队列所需要移动的最小距离', '10\r\n1 2 3 4 5 6 7 8 9 10', '9', 'wlxsq', 'OneCode', '1', '0', '10', '2');
INSERT INTO `problem` VALUES ('10108', '解密宝藏', '1000', '32768', '0', '0', '勇敢的赵老师做了一个无比美妙刺激的梦。在梦中，他到达了一个小岛，这个小岛是曾经海盗聚集的地方，盗上可能埋下了各种宝藏。\r\n这个小岛可以用一个n*m大小的矩阵表示，矩阵中的每一小块表示小岛中的一块陆地，方块的边长为1，有一些小块是曾经海盗为防止其他海盗侵略留下的陷阱，这些块是不能通过的,用\'#\'表示。还有一些小块是海盗曾经盗来的宝藏，用英文字母表示,每一份宝藏都被锁在了石门后面，要想获得宝藏，你只有按照密文里面的内容执行，才能触发开启石门的机关。\r\n在刚到达小岛的时候，赵老师就获得了一份解密宝藏的密文,密文中右k条密语，每条密语的格式为：“向x方向走y米”.现在赵老师已经知道，所有宝藏的所在地了，但是用这份密文能够解密多少份宝藏呢？', '第一行包含两个整数n和m,表示整个地图,(3<=n,m<=1000)\n接下来n行m列,其中\'#\'表示陷阱，在地图矩形中，矩形的四周一定是陷阱。其余的表示可行走区域。大写字母\'A\'到‘Z’表示的是每一份宝藏的编号，也是宝藏所在的位置。\n接下来有一行输入一个整数k(1<=k<=1e5)\n接下来有k行，每行一条密语，密语内容为x y，其中x有四种取值“N”，“S”，“E”，“W”,表示该密语需要走的方向（分别对应北，南，东，西），y表示朝该方向走几步，y是一个整数(1<=y<=1000);', '共一行，按字典序打印出所有能够解密的宝藏编号，如果没有解密出任何宝藏，则打印“so sad!”(没有引号).', '6 10\r\n##########\r\n#K#..#####\r\n#.#..##.##\r\n#..L.#...#\r\n###D###A.#\r\n##########\r\n4\r\nN 2\r\nS 1\r\nE 1\r\nW 2', 'AD', 'wlxsq', 'OneCode', '1', '0', '10', '2');
INSERT INTO `problem` VALUES ('10109', '完美三角形', '1000', '32768', '5', '3', '近日，我们的陈老师在网上购买了一批木棍，这些木棍堪称完美，每一根长度大小都一样，且长度为1.陈老师买回了这些木棍之后就不管了，直接把任务交给了我们的恺成同学。任务是什么呢？需要我们的恺成同学编写一个程序，程序的功能就是每当用户查询一个数n,表示我们有有一个边长为n的等边三角形，如图所示.该程序需要计算出完美三角形的个数.完美三角形就是边长为1的三角形.\n<img src=\"__PUBLIC__/img/10109.png\"></img>', '输入一个整数n(0&lt;n&lt;=10^20),为等边三角形的边长;', '输出完美三角形的个数', '3', '9', 'wlxsq', 'OneCode', '1', '0', '10', '2');
INSERT INTO `problem` VALUES ('10110', '完美三角形2', '1000', '32768', '0', '0', '不知道我们的上一题完美三角形你们有没有做出来，orz...不过我们的智慧恺成同学是真的很智慧，轻轻松松的就搞定了这个问题，开心的他跑去向我们的陈老师邀功去了，激动的说到：&quot;陈老师，陈老师，你就说我牛不牛，6不6,轻轻松松就搞定了你这题，so easy嘛，哈哈哈哈哈&quot;，陈老师狡黠的笑了笑，淡定的说到：&quot;你还是太年轻了！这只是入门，先给你点成就感而已，现在才是难题呢！！你要是半小时能解决，我就把这些完美木棍送给你。&quot; 虽然我们的智慧恺成并不想要这些木棍，但是能得到陈老师的认可，那还是蛮不错的。所以自信的回答到：&quot;尽管放马过来吧，哼哼！！！&quot;\n大家肯定知道，如图所示，上一题我们是统计边长为n的等边三角形中完美三角形的个数，现在我们的问题依旧是这个，不过我们的对完美三角形重新做了定义，边长相等的三角形我们就叫做完美三角形。不知道聪明的你们能否帮我们的恺成解决这个呢？\n<img src=\"__PUBLIC__/img/10110.png\"></img>', '输入一个整数n(0&lt;n&lt;=50000),为等边三角形的边长;', '输出完美三角形的个数', '3', '13', 'wlxsq', 'OneCode', '1', '0', '10', '2');
INSERT INTO `problem` VALUES ('10111', '素数和', '1000', '32768', '3', '0', '我们的张漂亮老师最近有点头疼，给学生上C语言基础课，被问问题了，竟然没有答上来，很是尴尬。事情是这样的，那天，张漂亮老师给同学们上完了什么是素数这节课后，就问同学们关于素数有什么问题不会的，都可以提出来。我们调皮的zz同学嗖的一下就站出来问：“我们已经知道什么是素数了，那么如果告诉你一个整数n，问他是三个素数的和的方案数是多少呢？', '输入一行，一个正整数n(n<=10000)', '输出一行，打印和为n的方案数', '9', '2', 'wlxsq', 'OneCode', '1', '0', '10', '2');
INSERT INTO `problem` VALUES ('10112', '羊羊羊之信息羊', '1000', '32768', '0', '0', '在羊羊羊部落，每一次有狼进攻，我们就需要互发信号，让所有的羊都躲起来，我们知道羊窝都是并排在一条直线上，所以，我们需要选出一批羊做我们羊羊部落的信息羊。我们给每一只信息羊配备一个羊羊羊村长发明的信息接收器，每次有狼进攻，所有的信息羊能在第一时间收到信息，他们需要在第一时间通知周围羊窝的羊，快速藏起来。但是由于信息接收器成本过高，羊羊羊部落经济有限，我们在保证没一个羊窝的安全下我们需要指定尽可能少的羊作为信息羊，以减少成本。我们已知羊窝n个并排在一条直线，并且羊窝的位置Xi,以及每只信息羊能够在第一时间通知其他羊的范围R。我们最少需要指定多少个羊窝的羊作为信息羊？如样例：指定7，20，50羊窝位置的羊作为信息羊即可.', '输入第一行，包含两个整数n,R,表示烽火台的总个数以及每个烽火台能够传递的范围。（1<=n,r<=10000）\r\n第二行输入n个整数，表示n个烽火台的位置Xi(0<=Xi<=10000)', '输出一行，最少要被点燃的烽火台的个数。', '6 10\r\n1 7 15 20 30 50', '3', 'wlxsq', 'OneCode', '1', '0', '10', '2');
INSERT INTO `problem` VALUES ('10113', '原地踏步走', '1000', '32768', '0', '0', '暑假马上就要过去了，面对即将跨进大学校门的准大学生来说，他们进入大学之后，军训将会是他们进入大学之后的第一个集体活动。\r\n还记得wlxsq同学军训的时候，每天都要站军姿，向左转向右转，and so on...那叫一个无聊啊orz...所以wlxsq的教官就想了一个有趣的小游戏，给大家放松放松。\r\n游戏规则是教官一口气喊一大串口令，问执行完这些口令之后，大家的位置与口令执行前的位置的距离是多少？1s时间内回答正确的同学原地踏步走，没回答上的同学要接受惩罚，原地俯卧撑50个。教官的口令为面朝某个方向，前进一定的距离。为了简化游戏的难度，让更多的同学不需要被惩罚，我们规定，方向只有如图所示的8个方向，并且我们将口令内容简化为八个方向对应的数字。移动的距离为1个单位，还有一个特殊的规定当方向为偶数时，移动的距离是奇数移动的距离的根号2倍。比如“123”表示先向1方向移动1个单位，再向2方向移动根号2个单位，最后再向3方向移动1个单位，问你移动完之后距离出发点的距离是多少？wlxsq不想接受惩罚，你们能帮他快速的计算出来吗？', '输入一个字符串s(s的长度不超过1000),表示教官一口气喊下的口令。', '输出执行口令后距离出发点的距离，保留两位小数', '123', '2.83', 'wlxsq', 'OneCode', '1', '0', '10', '2');
INSERT INTO `problem` VALUES ('10114', '整数和', '1000', '32768', '0', '0', '还记得素数和那道题么？没错，就是那道zz同学出的把我们张漂亮老师也没有做出来的题目，可是一直让我们张漂亮老师耿耿于怀。张老师一直想着怎么也出一道题目，把你们都给难住，找回一下场面。没办法orz...女人是记仇的0.0哈哈\r\n这不，张老师突然来了灵感。改改题面，看看你们有没有真的理解素数和这个题目的思想？现在张老师给你们一个数n，n表示4个数的和，即a+b+c+d=n;其中a,b,c,d四个数在张老师给定的m个数中会放回的抽取4个（每个数可以用多次）。问你是否存在这样4个数使得四个数的和为n。如果存在输出Yes;否则输出No.', '第一行输入两个整数n,m(1<=m<=1000,1<=n<=1e8)\r\n第二行输入m个数Ki,(a,b,c,d从这m个数中可放回的抽取）,(1<=Ki<=1e8)', '如果存在这样的4个数，则输入Yes;否则输出No;', '10 3\r\n1 3 5', 'Yes', 'wlxsq', 'OneCode', '1', '0', '10', '2');
INSERT INTO `problem` VALUES ('10115', '报数', '1000', '131072', '0', '0', '我们定义了一个定的报数规则：所有人依次排好，从第一个人开始报数，每个人报的数字只有1，且每个人报1的个数不限，即每一个人可以报1，也可以报11...也可以报111...111(n个，n&lt;=N),所有人报的1加起来为N个,有多少种报数方法？', '输入一行，表示一个整数N，即所有人报的1加起来为N(1&lt;=N&lt;=50)', '输出一行，对应的N的报数有多少种方法', '2', '2', 'wlxsq', 'OneCode', '1', '0', '10', '3');
INSERT INTO `problem` VALUES ('10116', '报数2', '1000', '131072', '0', '0', '有一天，焰姐和桥姐两人因都不愿意去拖地，他们两个选择玩个游戏，谁输了谁就去拖地。她们定了这样一个游戏规则，两个人轮流报数，报数范围在[1,m]，由焰姐开始报数，将每一个报的数都加起来，谁先加到n，谁获胜。假设两个人绝顶聪明，都能做出最优的选择。例如n=5,m=1,由于m=1，所以只能报1，因为焰姐开始，所以焰姐必胜。', '输入两个整数n,m（1&lt;=n,m&lt;=1000000）', '输出能否必胜？能输出“YES”，不能输出“NO”', '5 1', 'YES', 'wlxsq', 'OneCode', '1', '0', '10', '3');
INSERT INTO `problem` VALUES ('10117', '数独游戏', '1000', '131072', '0', '0', '数独是源自18世纪瑞士的一种数学游戏。是一种运用纸、笔进行演算的逻辑游戏。玩家需要根据9×9盘面上的已知数字，推理出所有剩余空格的数字，并满足每一行、每一列、每一个粗线宫（3*3）内的数字均含1-9，不重复。\r\n数独盘面是个九宫，每一宫又分为九个小格。在这八十一格中给出一定的已知数字和解题条件，利用逻辑和推理，在其他的空格上填入1-9的数字。使1-9每个数字在每一行、每一列和每一宫中都只出现一次，所以又称“九宫格”。\r\n给你这样一个九宫格，你能求出这个数独的解么？保证数独有唯一解', '输入一个输入，每个数字表示对应位置的值，0表示该位置还为填数字。', '输入数独的解，格式如样例所示，行末无空格.', '000000000\r\n002301000\r\n000000076\r\n900680000\r\n000000400\r\n001000200\r\n000002104\r\n000070000\r\n600090000', '598746312\r\n762351948\r\n143829576\r\n925684731\r\n376215489\r\n481937265\r\n837562194\r\n219478653\r\n654193827', 'wlxsq', 'OneCode', '1', '0', '10', '3');
INSERT INTO `problem` VALUES ('10118', '数数', '1000', '131072', '0', '0', '我们学算法，搞竞赛，一定得有自己的思想和理解，这样才能在这条路上走得更远。今天，wlxsq就想考考大家的小学数学有没有过关。没错，就是考大家的数数有没有过关。是不是很简单呢？我们给你一个区间[a,b]，问你区间中的整数中1出现的次数?例如：[1,13]，中包含1的数字有1、10、11、12、13,所以[a,b]区间总共出现了6次1.是不是很简单呢？那就开始证明自己吧', '输入一行，两个整数a, b(0&lt;=a, b&lt;=1e8).表示区间[a,b]', '输出[a,b]区间中1出现的次数', '1 13', '6', 'wlxsq', 'OneCode', '1', '0', '10', '3');
INSERT INTO `problem` VALUES ('10119', '羊羊羊之好战羊', '1000', '131072', '0', '0', '羊羊羊部落是一个神奇的部落。有各种各样的新鲜事，有趣的事，各色各类的羊。今天我们需要的介绍的就是好战羊。好战羊除了很能打架外，平时和普同羊没什么区别，但是，好战羊能感应到周围的好战羊，当他感应到周围有别的好战羊时，他会变的异常好战。所以给这些好战羊分配羊窝的时候，可难倒我们的羊羊羊村长在羊村，共有n间羊窝，羊窝并排在一条线上，每间羊窝对应在Xi的位置。现在我们有m只好战羊，我们需要给这m只好战羊安排羊窝，我们必须给每一只羊安排羊窝，但是好战羊之间的距离有需要尽可能的远。每只羊都分配到窝的情况下，羊与羊之间的最小距离是多少？例如，1 2 8 4 9将3只羊分配到1，4，9位置，他们的距离是最远的，好战羊之间的最小距离是3.', '第一行输入两个整数n,m;表示有n间羊窝，m只好战羊.(2&lt;=m&lt;=n&lt;=100000)\r\n第二行输入n间羊窝的位置xi.（0&lt;=xi&lt;=1e9）', '好战羊之间的距离在尽可能远离其他好战羊的情况下的最小距离.', '5 3\r\n1 2 8 4 9', '3', 'wlxsq', 'OneCode', '1', '0', '10', '3');
INSERT INTO `problem` VALUES ('10120', '一码运动会2', '1000', '131072', '0', '0', '一码公司开运动会，所有人都站成一排，共有n个员工，告诉你每个员工的身高，问你从第一个员工开始，每连续m个员工中身高最高的是多少？例如：共有5个员工，他们的身高是{1 3 2 4 2},则他们每连续三个员工的身高则是{[1 3 2] 4 2},{1 [3 2 4] 2},{1 3 [2 4 2]}对应连续m个的最大值3 4 4.', '第一行输入两个整数n,m(1&lt;=m&lt;=n&lt;=10^6)\r\n第二行输入每个员工对应的身高Xi(0&lt;=Xi&lt;=10^6);', '输出每连续m个员工的最高身高。', '5 3\r\n1 3 2 4 2', '3 4 4', 'wlxsq', 'OneCode', '1', '0', '10', '3');
INSERT INTO `problem` VALUES ('10121', '真假栈', '1000', '131072', '0', '0', '我们吴老师给同学们上完栈这个基础课之后，就想看看同学们能不能灵活的运用栈这么一个东西，所以就出了这么一道真假栈的题目。已知1 2 3 4 5是某栈的压入顺序，再给一个出栈顺序4 5 3 2 1，这个顺序可能是入栈顺序的一种对应的出栈顺序吗？如果可能，那这个栈就称之为真栈，否则则为假栈，例如4 5 3 2 1 则与1 2 3 4 5对应，为真栈，4 5 3 1 2则与1 2 3 4 5 不对应，为假栈。', '第一行输入一个整数n（1&lt;=n&lt;=1000），表示栈的元素个数.\r\n第二行输入栈的压入顺序Ai(1&lt;=Ai&lt;=1000).\r\n第三行输入出栈顺序Bi(1&lt;=Bi&lt;=1000).', '如果是真栈，那么输出YES，否则输出NO', '5\r\n1 2 3 4 5\r\n4 5 3 2 1', 'YES', 'wlxsq', 'OneCode', '1', '0', '10', '3');
INSERT INTO `problem` VALUES ('10122', '数&quot;ONECODE&quot;', '1000', '131072', '0', '0', '给出一个字符串S2，S2为字符串&quot;ONECODE&quot;的一个子串。（即S2为字符串&quot;ONECODE&quot;中连续的一段字符串）\r\n现在给定一个字符串S1, 找出在S1中有多少个子序列，恰好构成S2。S1的子序列，即从S1中按从左到右的顺序拿出若干个字符（可以不连续）组成的新字符串。', '第一行一个字符串S1(1&lt;=|S1|&lt;=100000)\r\n第二行一个字符串S2', '一行一个整数，表示S1中恰好构成S2的子序列个数。由于答案比较大，我们把答案mod 1000000007。', 'ONECODEONECODE\r\nONECODE', '12', 'zkc', 'OneCode', '1', '0', '20', '3');

-- ----------------------------
-- Table structure for problem_label
-- ----------------------------
DROP TABLE IF EXISTS `problem_label`;
CREATE TABLE `problem_label` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `problem_id` int(11) NOT NULL,
  `label_id` int(11) NOT NULL,
  `status` int(5) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=319 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of problem_label
-- ----------------------------
INSERT INTO `problem_label` VALUES ('297', '10101', '28', '0');
INSERT INTO `problem_label` VALUES ('298', '10102', '29', '0');
INSERT INTO `problem_label` VALUES ('299', '10103', '29', '0');
INSERT INTO `problem_label` VALUES ('300', '10104', '30', '0');
INSERT INTO `problem_label` VALUES ('301', '10105', '28', '0');
INSERT INTO `problem_label` VALUES ('302', '10106', '31', '0');
INSERT INTO `problem_label` VALUES ('303', '10107', '32', '0');
INSERT INTO `problem_label` VALUES ('304', '10108', '28', '0');
INSERT INTO `problem_label` VALUES ('305', '10109', '33', '0');
INSERT INTO `problem_label` VALUES ('306', '10110', '30', '0');
INSERT INTO `problem_label` VALUES ('307', '10111', '35', '0');
INSERT INTO `problem_label` VALUES ('308', '10112', '29', '0');
INSERT INTO `problem_label` VALUES ('309', '10113', '35', '0');
INSERT INTO `problem_label` VALUES ('310', '10114', '36', '0');
INSERT INTO `problem_label` VALUES ('311', '10115', '37', '0');
INSERT INTO `problem_label` VALUES ('312', '10116', '38', '0');
INSERT INTO `problem_label` VALUES ('313', '10117', '32', '0');
INSERT INTO `problem_label` VALUES ('314', '10118', '35', '0');
INSERT INTO `problem_label` VALUES ('315', '10119', '36', '0');
INSERT INTO `problem_label` VALUES ('316', '10120', '31', '0');
INSERT INTO `problem_label` VALUES ('317', '10121', '39', '0');
INSERT INTO `problem_label` VALUES ('318', '10122', '28', '0');

-- ----------------------------
-- Table structure for register
-- ----------------------------
DROP TABLE IF EXISTS `register`;
CREATE TABLE `register` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(40) NOT NULL,
  `password` varchar(40) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `school` varchar(30) NOT NULL DEFAULT '0',
  `motto` varchar(100) NOT NULL,
  `mail` varchar(50) NOT NULL,
  `realname` varchar(20) NOT NULL,
  `major` varchar(30) NOT NULL,
  `nickname` varchar(50) NOT NULL,
  `register_time` int(11) NOT NULL,
  `hash` varchar(255) NOT NULL,
  `ip` varchar(255) NOT NULL,
  `area` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of register
-- ----------------------------

-- ----------------------------
-- Table structure for train_comment
-- ----------------------------
DROP TABLE IF EXISTS `train_comment`;
CREATE TABLE `train_comment` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `level_msg_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `comment` text NOT NULL,
  `submit_time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of train_comment
-- ----------------------------

-- ----------------------------
-- Table structure for train_judge_detail
-- ----------------------------
DROP TABLE IF EXISTS `train_judge_detail`;
CREATE TABLE `train_judge_detail` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of train_judge_detail
-- ----------------------------

-- ----------------------------
-- Table structure for train_problem
-- ----------------------------
DROP TABLE IF EXISTS `train_problem`;
CREATE TABLE `train_problem` (
  `id` int(5) unsigned NOT NULL AUTO_INCREMENT,
  `level_msg_id` int(11) NOT NULL DEFAULT '0',
  `title` text NOT NULL,
  `time_limit` int(7) NOT NULL DEFAULT '1000',
  `memory_limit` int(7) NOT NULL DEFAULT '32768',
  `submissions` int(7) NOT NULL DEFAULT '0',
  `accepted` int(7) NOT NULL DEFAULT '0',
  `description` text NOT NULL,
  `input` text NOT NULL,
  `output` text NOT NULL,
  `sample_input` text NOT NULL,
  `sample_output` text NOT NULL,
  `author` text NOT NULL,
  `source` text NOT NULL,
  `status` int(9) NOT NULL DEFAULT '1',
  `output_limit` int(9) DEFAULT '0',
  `case_number` int(11) NOT NULL DEFAULT '10',
  `difficulty` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of train_problem
-- ----------------------------

-- ----------------------------
-- Table structure for train_rank
-- ----------------------------
DROP TABLE IF EXISTS `train_rank`;
CREATE TABLE `train_rank` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `level_msg_id` int(11) NOT NULL,
  `solve_problem` int(11) NOT NULL,
  `submissions` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=49 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of train_rank
-- ----------------------------

-- ----------------------------
-- Table structure for train_user_problem
-- ----------------------------
DROP TABLE IF EXISTS `train_user_problem`;
CREATE TABLE `train_user_problem` (
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
  `level_msg_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of train_user_problem
-- ----------------------------

-- ----------------------------
-- Table structure for user
-- ----------------------------
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(40) NOT NULL,
  `password` varchar(255) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '2',
  `root` int(1) NOT NULL DEFAULT '0',
  `accepted` int(10) NOT NULL DEFAULT '0',
  `submissions` int(10) NOT NULL DEFAULT '0',
  `solve_problem` int(10) NOT NULL DEFAULT '0',
  `school` varchar(255) NOT NULL DEFAULT '0',
  `Submitted_problem` int(10) NOT NULL DEFAULT '0',
  `motto` varchar(100) NOT NULL,
  `mail` varchar(50) NOT NULL,
  `realname` varchar(255) NOT NULL,
  `major` varchar(255) NOT NULL,
  `nickname` varchar(255) NOT NULL,
  `register_time` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of user
-- ----------------------------
INSERT INTO `user` VALUES ('1', 'onecode', '13d4a3ad1ee99a20332e860bdaa26e38', '1', '2', '0', '0', '0', '0', '0', 'WA光环', 'oj@onecode.com.cn', 'onecode', '', 'OneCode', '1111111');
INSERT INTO `user` VALUES ('2', 'wlxsq', 'afa0b08c55c59a7f137d60bc7b7fab07', '1', '0', '1', '1', '1', '一码小学', '1', '无敌是多么寂寞', 'wlxsq@onecode.com.cn', 'wlxsq', '', 'wlxsq', '1503126485');
INSERT INTO `user` VALUES ('3', 'zbs', 'c26be8aaf53b15054896983b43eb6a65', '1', '0', '0', '0', '0', '', '0', '', '2831532315@qq.com', '张宝升', '', 'zbs', '1503139561');
INSERT INTO `user` VALUES ('4', 'xkchen', '85cc07785c625a87a82f2db80ebba7e1', '1', '0', '0', '0', '0', '', '0', '哈哈哈哈哈哈哈哈哈哈', 'xkchen@onecode.com.cn', '陈学康', '', 'xkchen', '1503140455');
INSERT INTO `user` VALUES ('5', 'zhihuikaicheng', 'c26be8aaf53b15054896983b43eb6a65', '1', '0', '0', '0', '0', '双鸭山大学', '0', '', 'zkc@onecode.com.cn', '张恺成', '', '妹子恺成', '1503140534');
INSERT INTO `user` VALUES ('6', 'syc', '23b09b1872c89f94eb95685d241efcec', '1', '0', '1', '3', '1', '', '2', 'AC!!!', '3142994876@qq.com', '孙宇辰', '', 'syc', '1503140668');
INSERT INTO `user` VALUES ('7', '晴天', 'b7815335205e609beec8d365fabf323b', '1', '0', '1', '3', '1', '', '2', '。。。', '3376340133@qq.com', '姜清扬', '', '晴天', '1503284056');
INSERT INTO `user` VALUES ('8', 'LJC00147', '035a156802eeab2138ab4c0b347a42eb', '1', '0', '1', '1', '1', '杭州市文澜中学', '1', '我不入地狱，谁入地狱~', '2030602527@qq.com', '周转', '信奥', 'secret', '1503284379');
INSERT INTO `user` VALUES ('9', 'Medoric', 'fa89baaa2dbe6870ea9bca6e7c5337cf', '1', '0', '0', '4', '0', '', '1', '', 'medoric@163.com', '陈昱澄', '', 'Medoric', '1503374348');
INSERT INTO `user` VALUES ('10', 'wuying', 'f052151bb982289311c1ff0b73101371', '1', '0', '0', '0', '0', '', '0', '', 'wuying@qq.com', 'wuying', '', 'wuying', '1503499720');

-- ----------------------------
-- Table structure for user_problem
-- ----------------------------
DROP TABLE IF EXISTS `user_problem`;
CREATE TABLE `user_problem` (
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
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of user_problem
-- ----------------------------
INSERT INTO `user_problem` VALUES ('1', '2', '10105', '1503126719', '0', '24', '7268', '468', 'C++', 'wlxsq', 'Data/Library/code/wlxsq/10105_1.cpp', '0');
INSERT INTO `user_problem` VALUES ('2', '6', '10106', '1503151340', '4', '450', '364', '267', 'C++', 'syc', 'Data/Library/code/syc/10106_1.cpp', '0');
INSERT INTO `user_problem` VALUES ('3', '6', '10109', '1503152064', '1', '6', '4', '129', 'C++', 'syc', 'Data/Library/code/syc/10109_1.cpp', '0');
INSERT INTO `user_problem` VALUES ('4', '7', '10106', '1503296101', '1', '132', '1336', '324', 'C++', '晴天', 'Data/Library/code/晴天/10106_1.cpp', '0');
INSERT INTO `user_problem` VALUES ('5', '6', '10109', '1503315346', '0', '7', '4', '497', 'C++', 'syc', 'Data/Library/code/syc/10109_2.cpp', '0');
INSERT INTO `user_problem` VALUES ('6', '8', '10109', '1503323766', '0', '7', '364', '518', 'C++', 'secret', 'Data/Library/code/LJC00147/10109_1.cpp', '0');
INSERT INTO `user_problem` VALUES ('7', '4', '10101', '1503367078', '5', '0', '0', '3', 'C++', 'xkchen', 'Data/Library/code/xkchen/10101_1.cpp', '0');
INSERT INTO `user_problem` VALUES ('8', '9', '10104', '1503379433', '4', '415', '364', '449', 'C++', 'Medoric', 'Data/Library/code/Medoric/10104_1.cpp', '0');
INSERT INTO `user_problem` VALUES ('9', '9', '10111', '1503383397', '5', '0', '0', '516', 'C++', 'Medoric', 'Data/Library/code/Medoric/10111_1.cpp', '0');
INSERT INTO `user_problem` VALUES ('10', '9', '10111', '1503383617', '4', '426', '364', '689', 'C++', 'Medoric', 'Data/Library/code/Medoric/10111_2.cpp', '0');
INSERT INTO `user_problem` VALUES ('11', '9', '10111', '1503383650', '4', '459', '364', '543', 'C++', 'Medoric', 'Data/Library/code/Medoric/10111_3.cpp', '0');
INSERT INTO `user_problem` VALUES ('12', '7', '10109', '1503385386', '1', '9', '1072', '96', 'C++', '晴天', 'Data/Library/code/晴天/10109_1.cpp', '0');
INSERT INTO `user_problem` VALUES ('13', '7', '10109', '1503385681', '0', '7', '4', '666', 'C++', '晴天', 'Data/Library/code/晴天/10109_2.cpp', '0');
INSERT INTO `user_problem` VALUES ('14', '9', '10111', '1503385786', '5', '0', '0', '517', 'C++', 'Medoric', 'Data/Library/code/Medoric/10111_4.cpp', '0');
INSERT INTO `user_problem` VALUES ('15', '9', '10111', '1503385857', '4', '379', '360', '552', 'C++', 'Medoric', 'Data/Library/code/Medoric/10111_5.cpp', '0');
INSERT INTO `user_problem` VALUES ('16', '2', '10101', '1503393684', '8', '0', '0', '12', 'C++', 'wlxsq', 'Data/Library/code/wlxsq/10101_1.cpp', '0');
