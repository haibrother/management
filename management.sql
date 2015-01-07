-- --------------------------------------------------------
-- 主机:                           127.0.0.1
-- 服务器版本:                        5.5.32-log - MySQL Community Server (GPL)
-- 服务器操作系统:                      Win32
-- HeidiSQL 版本:                  9.1.0.4867
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- 导出 management 的数据库结构
DROP DATABASE IF EXISTS `management`;
CREATE DATABASE IF NOT EXISTS `management` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `management`;


-- 导出  表 management.accounts 结构
DROP TABLE IF EXISTS `accounts`;
CREATE TABLE IF NOT EXISTS `accounts` (
  `order` int(9) unsigned zerofill NOT NULL AUTO_INCREMENT,
  `account` varchar(10) DEFAULT 'A01',
  `deal` varchar(10) DEFAULT NULL,
  `login` varchar(10) DEFAULT NULL,
  `name` varchar(20) DEFAULT NULL,
  `money` double NOT NULL DEFAULT '0',
  `comment` varchar(32) NOT NULL DEFAULT '0',
  PRIMARY KEY (`order`),
  KEY `index_login` (`login`),
  KEY `index_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 正在导出表  management.accounts 的数据：~0 rows (大约)
/*!40000 ALTER TABLE `accounts` DISABLE KEYS */;
/*!40000 ALTER TABLE `accounts` ENABLE KEYS */;


-- 导出  表 management.emails 结构
DROP TABLE IF EXISTS `emails`;
CREATE TABLE IF NOT EXISTS `emails` (
  `email_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `email_to` text NOT NULL COMMENT '目标邮箱',
  `email_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '发送时间',
  `email_debugger` text NOT NULL COMMENT '日志',
  PRIMARY KEY (`email_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='服务器数据表';

-- 正在导出表  management.emails 的数据：~0 rows (大约)
/*!40000 ALTER TABLE `emails` DISABLE KEYS */;
/*!40000 ALTER TABLE `emails` ENABLE KEYS */;


-- 导出  表 management.options 结构
DROP TABLE IF EXISTS `options`;
CREATE TABLE IF NOT EXISTS `options` (
  `option_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `option_name` varchar(64) NOT NULL DEFAULT '' COMMENT '设置名称',
  `option_value` longtext NOT NULL COMMENT '设置值',
  `autoload` varchar(20) NOT NULL DEFAULT 'yes' COMMENT '是否自动加载',
  PRIMARY KEY (`option_id`),
  UNIQUE KEY `option_name` (`option_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='系统设置数据表';

-- 正在导出表  management.options 的数据：~0 rows (大约)
/*!40000 ALTER TABLE `options` DISABLE KEYS */;
/*!40000 ALTER TABLE `options` ENABLE KEYS */;


-- 导出  表 management.trade 结构
DROP TABLE IF EXISTS `trade`;
CREATE TABLE IF NOT EXISTS `trade` (
  `order` int(9) unsigned zerofill NOT NULL AUTO_INCREMENT,
  `account` varchar(10) DEFAULT 'A01',
  `deal` varchar(10) DEFAULT NULL,
  `trade_type` enum('open','closed','deposit') DEFAULT NULL,
  `login` varchar(10) DEFAULT NULL,
  `name` varchar(20) DEFAULT NULL,
  `open_time` datetime DEFAULT '1970-01-01 00:00:00',
  `balance` double NOT NULL DEFAULT '0',
  `broker_fee` double NOT NULL DEFAULT '24',
  `equity` double NOT NULL DEFAULT '0',
  `margin` double NOT NULL DEFAULT '0',
  `free_Margin` double NOT NULL DEFAULT '0',
  `type` varchar(16) DEFAULT NULL,
  `symbol` varchar(16) DEFAULT NULL,
  `lots` varchar(16) DEFAULT NULL,
  `volume` float(10,2) unsigned NOT NULL DEFAULT '0.00',
  `close_time` datetime DEFAULT '1970-01-01 00:00:00',
  `close_price` double NOT NULL DEFAULT '0',
  `open_price` double NOT NULL DEFAULT '0',
  `market_price` double NOT NULL DEFAULT '0',
  `commission` double NOT NULL DEFAULT '0',
  `taxes` double NOT NULL DEFAULT '0',
  `agent` double NOT NULL DEFAULT '0',
  `swap` double NOT NULL DEFAULT '0',
  `profit` double NOT NULL DEFAULT '0',
  `pips` double NOT NULL DEFAULT '0',
  `comment` varchar(32) DEFAULT NULL,
  `new_comment` varchar(32) DEFAULT NULL,
  `version` tinyint(3) NOT NULL DEFAULT '1' COMMENT '版本号',
  `version_month` varchar(7) DEFAULT NULL COMMENT '版本年月',
  `operator` varchar(20) DEFAULT NULL,
  `ctime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`order`),
  KEY `Index 2` (`login`),
  KEY `version_month` (`version_month`),
  KEY `trade_type` (`trade_type`),
  KEY `type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 数据导出被取消选择。


-- 导出  表 management.trade_delete 结构
DROP TABLE IF EXISTS `trade_delete`;
CREATE TABLE IF NOT EXISTS `trade_delete` (
  `order` int(9) unsigned zerofill NOT NULL AUTO_INCREMENT,
  `account` varchar(10) DEFAULT 'A01',
  `deal` varchar(10) DEFAULT NULL,
  `trade_type` enum('open','closed','deposit') DEFAULT NULL,
  `login` varchar(10) DEFAULT NULL,
  `name` varchar(20) DEFAULT NULL,
  `open_time` datetime DEFAULT '1970-01-01 00:00:00',
  `balance` double NOT NULL DEFAULT '0',
  `broker_fee` double NOT NULL DEFAULT '24',
  `equity` double NOT NULL DEFAULT '0',
  `margin` double NOT NULL DEFAULT '0',
  `free_Margin` double NOT NULL DEFAULT '0',
  `type` varchar(16) DEFAULT NULL,
  `symbol` varchar(16) DEFAULT NULL,
  `lots` varchar(16) DEFAULT NULL,
  `volume` float(10,2) unsigned NOT NULL DEFAULT '0.00',
  `close_time` datetime DEFAULT '1970-01-01 00:00:00',
  `close_price` double NOT NULL DEFAULT '0',
  `open_price` double NOT NULL DEFAULT '0',
  `market_price` double NOT NULL DEFAULT '0',
  `commission` double NOT NULL DEFAULT '0',
  `taxes` double NOT NULL DEFAULT '0',
  `agent` double NOT NULL DEFAULT '0',
  `swap` double NOT NULL DEFAULT '0',
  `profit` double NOT NULL DEFAULT '0',
  `pips` double NOT NULL DEFAULT '0',
  `comment` varchar(32) DEFAULT NULL,
  `new_comment` varchar(32) DEFAULT NULL,
  `version` tinyint(3) NOT NULL DEFAULT '1' COMMENT '版本号',
  `version_month` varchar(7) DEFAULT NULL COMMENT '版本年月',
  `operator` varchar(20) DEFAULT NULL,
  `ctime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `mtime` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`order`),
  KEY `Index 2` (`login`),
  KEY `version_month` (`version_month`),
  KEY `trade_type` (`trade_type`),
  KEY `type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 数据导出被取消选择。
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;


-- 导出  表 management.users 结构
DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_login` varchar(50) NOT NULL DEFAULT '' COMMENT '工号',
  `user_pass` varchar(64) NOT NULL DEFAULT '' COMMENT '密码加密',
  `user_nicename` varchar(50) NOT NULL DEFAULT '' COMMENT '别名',
  `user_department` varchar(50) NOT NULL DEFAULT '' COMMENT '部门',
  `user_email` varchar(100) NOT NULL DEFAULT '' COMMENT 'E-mail地址',
  `user_registered` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '注册日期',
  `user_status` int(8) NOT NULL DEFAULT '0' COMMENT '状态：0,创始管理员(最高权限用户);1,超级管理员;2,管理员;3,用户;',
  `user_last_ip` varchar(100) NOT NULL DEFAULT '' COMMENT '客户端IP',
  `user_last_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '注册日期',
  PRIMARY KEY (`user_id`),
  KEY `user_login` (`user_login`),
  KEY `user_nicename` (`user_nicename`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COMMENT='用户数据表';

-- 正在导出表  management.users 的数据：~7 rows (大约)
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` (`user_id`, `user_login`, `user_pass`, `user_nicename`, `user_department`, `user_email`, `user_registered`, `user_status`, `user_last_ip`, `user_last_time`) VALUES
	(1, 'admin', '28c8edde3d61a0411511d3b1866f0636c4ca4238a0b923820dcc509a6f75849b', '', 'teat', 'testa@qq.com', '0000-00-00 00:00:00', 1, '127.0.0.1', '2014-12-30 09:06:00'),
	(2, '411', '665f644e43731ff9db3d341da5c827e1c81e728d9d4c2f636f067f89cc14862c', '', 'fafa', 'test@qq.com', '2014-11-27 14:56:50', 2, '127.0.0.1', '2014-11-27 16:06:11'),
	(3, '415', '28c8edde3d61a0411511d3b1866f0636c4ca4238a0b923820dcc509a6f75849b', '', '1', '111@qq.com', '2014-11-27 15:05:51', 3, '127.0.0.1', '2014-11-27 15:05:51'),
	(4, '414', '28c8edde3d61a0411511d3b1866f0636c4ca4238a0b923820dcc509a6f75849b', '', 'test', 'tesa@qq.com', '2014-11-27 15:09:38', 3, '127.0.0.1', '2014-12-03 20:22:18'),
	(5, '132', '28c8edde3d61a0411511d3b1866f0636c4ca4238a0b923820dcc509a6f75849b', '', 'test', 'testa@qq.com', '2014-12-03 20:26:50', 3, '127.0.0.1', '2014-12-03 20:28:11'),
	(6, '133', '28c8edde3d61a0411511d3b1866f0636c4ca4238a0b923820dcc509a6f75849b', '', 'test', 'testa@qq.com', '2014-12-03 20:27:10', 2, '127.0.0.1', '2014-12-03 20:28:23'),
	(7, '417', '28c8edde3d61a0411511d3b1866f0636c4ca4238a0b923820dcc509a6f75849b', '', 'testq', 'test@qq.com', '2014-12-26 10:09:07', 3, '127.0.0.1', '2014-12-26 10:09:13');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
