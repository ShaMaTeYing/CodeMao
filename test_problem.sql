/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50553
Source Host           : localhost:3306
Source Database       : onecode

Target Server Type    : MYSQL
Target Server Version : 50553
File Encoding         : 65001

Date: 2017-10-02 00:11:35
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for test_problem
-- ----------------------------
DROP TABLE IF EXISTS `test_problem`;
CREATE TABLE `test_problem` (
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
  `label` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10002 DEFAULT CHARSET=utf8;
