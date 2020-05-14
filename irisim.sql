-- --------------------------------------------------------
-- 主机:                           localhost
-- 服务器版本:                        8.0.11 - MySQL Community Server - GPL
-- 服务器操作系统:                      Win64
-- HeidiSQL 版本:                  11.0.0.5962
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


-- 导出 iris 的数据库结构
DROP DATABASE IF EXISTS `iris`;
CREATE DATABASE IF NOT EXISTS `iris` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `iris`;

-- 导出  表 iris.iris_friend 结构
DROP TABLE IF EXISTS `iris_friend`;
CREATE TABLE IF NOT EXISTS `iris_friend` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL COMMENT '用户ID',
  `friend_id` int(11) NOT NULL COMMENT '好友ID',
  `friend_group_id` int(11) NOT NULL COMMENT '好友分组ID',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8 COMMENT='好友列表';

-- 正在导出表  iris.iris_friend 的数据：~2 rows (大约)
DELETE FROM `iris_friend`;
/*!40000 ALTER TABLE `iris_friend` DISABLE KEYS */;
INSERT INTO `iris_friend` (`id`, `uid`, `friend_id`, `friend_group_id`) VALUES
	(43, 2, 1, 2),
	(44, 1, 2, 1);
/*!40000 ALTER TABLE `iris_friend` ENABLE KEYS */;

-- 导出  表 iris.iris_friend_group 结构
DROP TABLE IF EXISTS `iris_friend_group`;
CREATE TABLE IF NOT EXISTS `iris_friend_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL COMMENT '用户ID',
  `name` varchar(20) NOT NULL COMMENT '分组名称',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='好友分组';

-- 正在导出表  iris.iris_friend_group 的数据：~2 rows (大约)
DELETE FROM `iris_friend_group`;
/*!40000 ALTER TABLE `iris_friend_group` DISABLE KEYS */;
INSERT INTO `iris_friend_group` (`id`, `uid`, `name`) VALUES
	(1, 1, '好友列表'),
	(2, 2, '好友列表');
/*!40000 ALTER TABLE `iris_friend_group` ENABLE KEYS */;

-- 导出  表 iris.iris_friend_request 结构
DROP TABLE IF EXISTS `iris_friend_request`;
CREATE TABLE IF NOT EXISTS `iris_friend_request` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `from_id` int(11) NOT NULL COMMENT '请求的发起者',
  `to_id` int(11) NOT NULL COMMENT '请求的接收者',
  `friend_group_id` int(11) NOT NULL COMMENT '接收者同意后添加到发起者对应的分组中',
  `status` tinyint(1) NOT NULL COMMENT '状态:1-请求中 2-同意 3-拒绝',
  `read` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否已读:0-否 1-是',
  `postscript` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '' COMMENT '请求附言',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  `update_time` datetime DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`),
  KEY `to_id` (`to_id`),
  KEY `from_id` (`from_id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8 COMMENT='好友申请';

-- 正在导出表  iris.iris_friend_request 的数据：~2 rows (大约)
DELETE FROM `iris_friend_request`;
/*!40000 ALTER TABLE `iris_friend_request` DISABLE KEYS */;
INSERT INTO `iris_friend_request` (`id`, `from_id`, `to_id`, `friend_group_id`, `status`, `read`, `postscript`, `create_time`, `update_time`) VALUES
	(18, 1, 2, 1, 2, 1, '', '2020-05-08 17:04:24', '2020-05-12 15:57:29');
/*!40000 ALTER TABLE `iris_friend_request` ENABLE KEYS */;

-- 导出  表 iris.iris_group 结构
DROP TABLE IF EXISTS `iris_group`;
CREATE TABLE IF NOT EXISTS `iris_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL COMMENT '群主ID',
  `name` varchar(20) NOT NULL COMMENT '群名称',
  `avatar` varchar(100) NOT NULL COMMENT '群头像',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='群组';

-- 正在导出表  iris.iris_group 的数据：~1 rows (大约)
DELETE FROM `iris_group`;
/*!40000 ALTER TABLE `iris_group` DISABLE KEYS */;
INSERT INTO `iris_group` (`id`, `uid`, `name`, `avatar`) VALUES
	(1, 1, '测试1群', '/asset/layui/images/1.jpg'),
	(3, 2, '测试2群', '/asset/layui/images/1.jpg');
/*!40000 ALTER TABLE `iris_group` ENABLE KEYS */;

-- 导出  表 iris.iris_group_user 结构
DROP TABLE IF EXISTS `iris_group_user`;
CREATE TABLE IF NOT EXISTS `iris_group_user` (
  `group_id` int(11) NOT NULL COMMENT '群ID',
  `uid` int(11) NOT NULL COMMENT '用户ID',
  `role` tinyint(1) NOT NULL DEFAULT '3' COMMENT '角色:1-群主 2-管理员 3-普通成员',
  PRIMARY KEY (`group_id`,`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='群成员';

-- 正在导出表  iris.iris_group_user 的数据：~2 rows (大约)
DELETE FROM `iris_group_user`;
/*!40000 ALTER TABLE `iris_group_user` DISABLE KEYS */;
INSERT INTO `iris_group_user` (`group_id`, `uid`, `role`) VALUES
	(1, 1, 1),
	(1, 2, 3),
	(3, 2, 3);
/*!40000 ALTER TABLE `iris_group_user` ENABLE KEYS */;

-- 导出  表 iris.iris_user 结构
DROP TABLE IF EXISTS `iris_user`;
CREATE TABLE IF NOT EXISTS `iris_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(15) NOT NULL COMMENT '用户名',
  `password` varchar(100) NOT NULL COMMENT '密码',
  `avatar` varchar(100) NOT NULL COMMENT '头像',
  `sign` varchar(30) NOT NULL COMMENT '签名',
  `status` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'online' COMMENT '状态:online-在线  offline-离线',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  `last_login_time` datetime NOT NULL COMMENT '最后登录时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='用户表';

-- 正在导出表  iris.iris_user 的数据：~3 rows (大约)
DELETE FROM `iris_user`;
/*!40000 ALTER TABLE `iris_user` DISABLE KEYS */;
INSERT INTO `iris_user` (`id`, `username`, `password`, `avatar`, `sign`, `status`, `create_time`, `last_login_time`) VALUES
	(1, 'admin', '$2y$10$QyguZ.2CxpC5dR2NC4R4/OBzn9PwEI6Rw7dwo7aFQ6IjTahPJpsKm', '/asset/layui/images/1.jpg', '我是admin测试账号', 'online', '2020-04-27 17:12:18', '2020-05-14 14:05:15'),
	(2, 'root', '$2y$10$QyguZ.2CxpC5dR2NC4R4/OBzn9PwEI6Rw7dwo7aFQ6IjTahPJpsKm', '/asset/layui/images/1.jpg', '我是root测试账号', 'online', '2020-04-27 17:37:44', '2020-05-14 11:24:32');
/*!40000 ALTER TABLE `iris_user` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
