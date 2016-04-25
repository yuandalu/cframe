-- phpMyAdmin SQL Dump
-- version 4.2.10
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: 2016-04-21 18:14:13
-- 服务器版本： 5.6.21
-- PHP Version: 5.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `yuandalu`
--
CREATE DATABASE IF NOT EXISTS `yuandalu` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `yuandalu`;

-- --------------------------------------------------------

--
-- 表的结构 `adm_actlog`
--

DROP TABLE IF EXISTS `adm_actlog`;
CREATE TABLE IF NOT EXISTS `adm_actlog` (
`id` int(10) NOT NULL,
  `username` varchar(32) NOT NULL,
  `action` varchar(175) NOT NULL DEFAULT '',
  `content` text NOT NULL COMMENT '操作内容',
  `action_time` datetime NOT NULL,
  `ip` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- 表的结构 `adm_authnode`
--

DROP TABLE IF EXISTS `adm_authnode`;
CREATE TABLE IF NOT EXISTS `adm_authnode` (
  `id` int(10) unsigned NOT NULL,
  `ctime` datetime NOT NULL,
  `utime` datetime NOT NULL,
  `aid` int(10) unsigned NOT NULL DEFAULT '0',
  `contr` varchar(35) NOT NULL DEFAULT '',
  `action` varchar(35) NOT NULL DEFAULT '',
  `verify` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '验证规则'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- 转存表中的数据 `adm_authnode`
--

INSERT INTO `adm_authnode` (`id`, `ctime`, `utime`, `aid`, `contr`, `action`, `verify`) VALUES
(11, '2016-04-21 17:56:05', '2016-04-21 17:56:05', 11, 'AdmAuth', 'index', 3),
(12, '2016-04-21 17:56:05', '2016-04-21 17:56:05', 11, 'AdmAuth', 'add', 3),
(13, '2016-04-21 17:56:05', '2016-04-21 17:56:05', 11, 'AdmAuth', 'addauth', 3),
(14, '2016-04-21 17:56:05', '2016-04-21 17:56:05', 11, 'AdmAuth', 'list', 3),
(15, '2016-04-21 17:56:05', '2016-04-21 17:56:05', 11, 'AdmAuthNode', 'index', 3),
(16, '2016-04-21 17:56:06', '2016-04-21 17:56:06', 11, 'AdmAuthNode', 'add', 3),
(17, '2016-04-21 17:56:06', '2016-04-21 17:56:06', 11, 'AdmAuthNode', 'updateAuths', 3),
(18, '2016-04-21 17:56:06', '2016-04-21 17:56:06', 11, 'AdmAuthNode', 'edit', 3),
(19, '2016-04-21 17:56:06', '2016-04-21 17:56:06', 11, 'AdmAuthNode', 'modify', 3),
(20, '2016-04-21 17:56:06', '2016-04-21 17:56:06', 11, 'AdmAuthNode', 'list', 3),
(21, '2016-04-21 17:56:06', '2016-04-21 17:56:06', 11, 'AdmMenu', 'index', 3),
(22, '2016-04-21 17:56:06', '2016-04-21 17:56:06', 11, 'AdmMenu', 'add', 3),
(23, '2016-04-21 17:56:06', '2016-04-21 17:56:06', 11, 'AdmMenu', 'edit', 3),
(24, '2016-04-21 17:56:06', '2016-04-21 17:56:06', 11, 'AdmMenu', 'modify', 3),
(25, '2016-04-21 17:56:06', '2016-04-21 17:56:06', 11, 'AdmMenu', 'list', 3),
(26, '2016-04-21 17:56:06', '2016-04-21 17:56:06', 11, 'AdmUser', 'index', 3),
(27, '2016-04-21 17:56:07', '2016-04-21 17:56:07', 11, 'AdmUser', 'add', 3),
(28, '2016-04-21 17:56:07', '2016-04-21 17:56:07', 11, 'AdmUser', 'addGrade', 3),
(29, '2016-04-21 17:56:07', '2016-04-21 17:56:07', 11, 'AdmUser', 'modifyauth', 3),
(30, '2016-04-21 17:56:07', '2016-04-21 17:56:07', 11, 'AdmUser', 'forbidden', 3),
(31, '2016-04-21 17:56:07', '2016-04-21 17:56:07', 11, 'AdmUser', 'checkname', 3),
(32, '2016-04-21 17:56:07', '2016-04-21 17:56:07', 11, 'AdmUser', 'delauth', 3),
(33, '2016-04-21 17:56:07', '2016-04-21 17:56:07', 11, 'AdmUser', 'saverole', 3),
(34, '2016-04-21 17:56:07', '2016-04-21 17:56:07', 11, 'AdmUser', 'deleteuserauth', 3),
(35, '2016-04-21 17:56:07', '2016-04-21 17:56:07', 11, 'Entity', 'index', 3),
(36, '2016-04-21 17:56:07', '2016-04-21 17:56:07', 11, 'Entity', 'indexSubmit', 3),
(37, '2016-04-21 17:56:08', '2016-04-21 17:56:08', 11, 'Entity', 'ajaxQueryEntity', 3),
(38, '2016-04-21 17:56:08', '2016-04-21 17:56:08', 11, 'Entity', 'ajaxQueryTableName', 3),
(39, '2016-04-21 17:56:08', '2016-04-21 17:56:08', 0, 'Include', 'showimg', 2),
(40, '2016-04-21 17:56:08', '2016-04-21 17:56:08', 0, 'Include', 'showHtmlImage', 2),
(41, '2016-04-21 17:56:08', '2016-04-21 17:56:08', 0, 'Index', 'index', 2),
(42, '2016-04-21 17:56:08', '2016-04-21 17:56:08', 0, 'Index', 'login', 2),
(43, '2016-04-21 17:56:08', '2016-04-21 17:56:08', 0, 'Index', 'doLogin', 2),
(44, '2016-04-21 17:56:08', '2016-04-21 17:56:08', 0, 'Index', 'logout', 2),
(45, '2016-04-21 17:56:08', '2016-04-21 17:56:08', 11, 'Log', 'index', 3),
(46, '2016-04-21 17:56:08', '2016-04-21 17:56:08', 11, 'Log', 'operate', 3),
(47, '2016-04-21 17:56:08', '2016-04-21 17:56:08', 11, 'Log', 'getLogDetail', 3),
(48, '2016-04-21 17:56:08', '2016-04-21 17:56:08', 0, 'TestClass', 'index', 1),
(49, '2016-04-21 17:56:08', '2016-04-21 17:56:08', 0, 'TestClass', 'add', 1),
(50, '2016-04-21 17:56:08', '2016-04-21 17:56:08', 0, 'TestClass', 'delete', 1),
(51, '2016-04-21 17:56:08', '2016-04-21 17:56:08', 0, 'TestClass', 'list', 1),
(52, '2016-04-21 17:56:09', '2016-04-21 17:56:09', 0, 'TestClass', 'export', 1),
(53, '2016-04-21 17:56:09', '2016-04-21 17:56:09', 0, 'User', 'index', 1),
(54, '2016-04-21 17:56:09', '2016-04-21 17:56:09', 0, 'User', 'add', 1),
(55, '2016-04-21 17:56:09', '2016-04-21 17:56:09', 0, 'User', 'delete', 1),
(56, '2016-04-21 17:56:09', '2016-04-21 17:56:09', 0, 'User', 'list', 1),
(57, '2016-04-21 17:56:09', '2016-04-21 17:56:09', 0, 'User', 'export', 1);

-- --------------------------------------------------------

--
-- 表的结构 `adm_auths`
--

DROP TABLE IF EXISTS `adm_auths`;
CREATE TABLE IF NOT EXISTS `adm_auths` (
  `id` int(10) unsigned NOT NULL,
  `ctime` datetime NOT NULL,
  `utime` datetime NOT NULL,
  `name` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- 转存表中的数据 `adm_auths`
--

INSERT INTO `adm_auths` (`id`, `ctime`, `utime`, `name`) VALUES
(11, '2016-04-21 17:56:55', '2016-04-21 17:56:55', '系统管理');

-- --------------------------------------------------------

--
-- 表的结构 `adm_loginlog`
--

DROP TABLE IF EXISTS `adm_loginlog`;
CREATE TABLE IF NOT EXISTS `adm_loginlog` (
`id` int(10) NOT NULL,
  `username` varchar(32) NOT NULL,
  `ip` varchar(15) NOT NULL,
  `login_time` datetime NOT NULL,
  `result` tinyint(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- 表的结构 `adm_menus`
--

DROP TABLE IF EXISTS `adm_menus`;
CREATE TABLE IF NOT EXISTS `adm_menus` (
  `id` int(10) unsigned NOT NULL,
  `ctime` datetime NOT NULL,
  `utime` datetime NOT NULL,
  `name` varchar(32) NOT NULL DEFAULT '',
  `url` varchar(128) NOT NULL DEFAULT '',
  `sort` tinyint(4) NOT NULL,
  `oneclass` varchar(35) NOT NULL COMMENT '一级导航',
  `groupid` int(11) NOT NULL,
  `aid` int(4) NOT NULL,
  `curr_menu` char(64) NOT NULL DEFAULT '',
  `curr_submenu` char(64) NOT NULL DEFAULT '',
  `icon` varchar(64) NOT NULL DEFAULT 'fa fa-circle-o text-aqua'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- 转存表中的数据 `adm_menus`
--

INSERT INTO `adm_menus` (`id`, `ctime`, `utime`, `name`, `url`, `sort`, `oneclass`, `groupid`, `aid`, `curr_menu`, `curr_submenu`, `icon`) VALUES
(11, '2014-07-28 01:44:30', '2014-07-28 01:44:30', '菜单管理', '/AdmMenu/list', 0, '系统管理', 11, 11, 'manage', 'manage_admmenu', 'fa-cogs'),
(12, '2014-07-28 02:23:05', '2014-07-28 02:23:05', '后台用户', '/AdmUser/index/', 0, '系统管理', 11, 11, 'manage', 'manage_admuser', 'fa-circle-o text-aqua'),
(13, '2014-07-28 02:24:30', '2014-07-28 02:24:30', '权限管理', '/AdmAuth/list', 0, '系统管理', 11, 11, 'manage', 'manage_admauth', 'fa-circle-o text-aqua'),
(14, '2014-07-28 02:26:10', '2014-07-28 02:26:10', '权限节点', '/AdmAuthNode/list', 0, '系统管理', 11, 11, 'manage', 'manage_admauthnode', 'fa-circle-o text-aqua'),
(15, '2014-07-28 23:34:33', '2014-07-28 23:34:33', '登陆日志', '/Log/index', 0, '系统管理', 11, 11, 'manage', 'manage_log', 'fa-circle-o text-aqua'),
(16, '2014-07-28 23:39:02', '2014-07-28 23:39:02', '操作日志', '/Log/operate', 0, '系统管理', 11, 11, 'manage', 'manage_operate', 'fa-circle-o text-aqua');

-- --------------------------------------------------------

--
-- 表的结构 `adm_userauth`
--

DROP TABLE IF EXISTS `adm_userauth`;
CREATE TABLE IF NOT EXISTS `adm_userauth` (
  `id` int(10) unsigned NOT NULL,
  `ctime` datetime NOT NULL,
  `utime` datetime NOT NULL,
  `user` varchar(32) NOT NULL DEFAULT '',
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `aid` int(10) unsigned NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- 转存表中的数据 `adm_userauth`
--

INSERT INTO `adm_userauth` (`id`, `ctime`, `utime`, `user`, `uid`, `aid`) VALUES
(11, '2016-04-21 18:11:50', '2016-04-21 18:11:50', 'dingdejing', 11, 11);

-- --------------------------------------------------------

--
-- 表的结构 `adm_users`
--

DROP TABLE IF EXISTS `adm_users`;
CREATE TABLE IF NOT EXISTS `adm_users` (
  `id` int(10) unsigned NOT NULL,
  `name` varchar(10) NOT NULL,
  `ename` varchar(32) NOT NULL,
  `depart` char(10) NOT NULL,
  `position` char(10) NOT NULL,
  `role` char(10) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '状态'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=COMPACT;

--
-- 转存表中的数据 `adm_users`
--

INSERT INTO `adm_users` (`id`, `name`, `ename`, `depart`, `position`, `role`, `status`) VALUES
(11, '管理员', 'dingdejing', '技术', '工程师', '超级管理员', 1);

-- --------------------------------------------------------

--
-- 表的结构 `sys_dbcache`
--

DROP TABLE IF EXISTS `sys_dbcache`;
CREATE TABLE IF NOT EXISTS `sys_dbcache` (
  `skey` varchar(32) NOT NULL,
  `expiry` int(11) NOT NULL,
  `value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- 表的结构 `sys_idgenter`
--

DROP TABLE IF EXISTS `sys_idgenter`;
CREATE TABLE IF NOT EXISTS `sys_idgenter` (
  `obj` varchar(32) NOT NULL DEFAULT '',
  `id` int(11) NOT NULL DEFAULT '1',
  `step` tinyint(4) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- 转存表中的数据 `sys_idgenter`
--

INSERT INTO `sys_idgenter` (`obj`, `id`, `step`) VALUES
('adm_authnode', 57, 1),
('adm_auths', 11, 1),
('adm_menugroup', 11, 1),
('adm_menus', 16, 1),
('adm_userauth', 11, 1),
('adm_users', 11, 1),
('test_class', 100000, 1),
('users', 100002, 1);

-- --------------------------------------------------------

--
-- 表的结构 `sys_sessions`
--

DROP TABLE IF EXISTS `sys_sessions`;
CREATE TABLE IF NOT EXISTS `sys_sessions` (
  `skey` varchar(32) NOT NULL DEFAULT '',
  `expiry` int(11) DEFAULT NULL,
  `value` varchar(5000) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- 表的结构 `test_class`
--

DROP TABLE IF EXISTS `test_class`;
CREATE TABLE IF NOT EXISTS `test_class` (
  `id` int(10) unsigned NOT NULL,
  `ctime` datetime NOT NULL,
  `utime` datetime NOT NULL,
  `testdatetime` datetime NOT NULL COMMENT 'datetime',
  `testdata` date NOT NULL COMMENT 'data',
  `testtime` time NOT NULL COMMENT 'time',
  `testint` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'int',
  `testtinyint` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT 'tinyint',
  `testvarchar` varchar(32) NOT NULL DEFAULT 'default' COMMENT 'varchar',
  `testint_table` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'int_table'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='测试';

-- --------------------------------------------------------

--
-- 表的结构 `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) unsigned NOT NULL,
  `ctime` datetime NOT NULL,
  `utime` datetime NOT NULL,
  `mobile` char(11) NOT NULL DEFAULT '' COMMENT '手机号',
  `nickname` varchar(32) NOT NULL DEFAULT '' COMMENT '昵称',
  `password` char(32) NOT NULL DEFAULT '' COMMENT '密码',
  `salt` char(6) NOT NULL DEFAULT '' COMMENT '加密',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '状态'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户';

--
-- 转存表中的数据 `users`
--

INSERT INTO `users` (`id`, `ctime`, `utime`, `mobile`, `nickname`, `password`, `salt`, `status`) VALUES
(100001, '2016-04-17 09:06:25', '2016-04-17 09:06:48', '15652202721', '夏雨晴空', '632dcc9bed4b755c74a9876b3cad3ad1', '32ed87', 1),
(100002, '2016-04-17 15:38:34', '2016-04-17 15:38:34', '15652202756', 'yuandalu', '632dcc9bed4b755c74a9876b3cad3ad1', '32ed87', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `adm_actlog`
--
ALTER TABLE `adm_actlog`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `adm_authnode`
--
ALTER TABLE `adm_authnode`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `adm_auths`
--
ALTER TABLE `adm_auths`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `adm_loginlog`
--
ALTER TABLE `adm_loginlog`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `adm_menus`
--
ALTER TABLE `adm_menus`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `adm_userauth`
--
ALTER TABLE `adm_userauth`
 ADD PRIMARY KEY (`id`), ADD KEY `user` (`user`);

--
-- Indexes for table `adm_users`
--
ALTER TABLE `adm_users`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `ename` (`ename`);

--
-- Indexes for table `sys_dbcache`
--
ALTER TABLE `sys_dbcache`
 ADD UNIQUE KEY `skey` (`skey`);

--
-- Indexes for table `sys_idgenter`
--
ALTER TABLE `sys_idgenter`
 ADD PRIMARY KEY (`obj`);

--
-- Indexes for table `sys_sessions`
--
ALTER TABLE `sys_sessions`
 ADD PRIMARY KEY (`skey`), ADD KEY `sessions_expiry` (`expiry`);

--
-- Indexes for table `test_class`
--
ALTER TABLE `test_class`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
 ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `adm_actlog`
--
ALTER TABLE `adm_actlog`
MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `adm_loginlog`
--
ALTER TABLE `adm_loginlog`
MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
