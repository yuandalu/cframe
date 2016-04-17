-- phpMyAdmin SQL Dump
-- version 4.4.14
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: 2016-04-17 09:08:12
-- 服务器版本： 5.7.11
-- PHP Version: 7.0.5

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

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
  `content` text COMMENT '操作内容',
  `action_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- 表的结构 `adm_authnode`
--

DROP TABLE IF EXISTS `adm_authnode`;
CREATE TABLE IF NOT EXISTS `adm_authnode` (
  `id` int(10) unsigned NOT NULL,
  `ctime` datetime DEFAULT CURRENT_TIMESTAMP,
  `utime` datetime DEFAULT CURRENT_TIMESTAMP,
  `aid` int(10) unsigned NOT NULL DEFAULT '0',
  `contr` varchar(35) NOT NULL DEFAULT '',
  `action` varchar(35) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- 转存表中的数据 `adm_authnode`
--

INSERT INTO `adm_authnode` (`id`, `ctime`, `utime`, `aid`, `contr`, `action`) VALUES
(11, '2016-04-16 05:51:50', '2016-04-16 05:51:50', 11, 'AdmAuth', 'index'),
(12, '2016-04-16 05:51:50', '2016-04-16 05:51:50', 11, 'AdmAuth', 'add'),
(13, '2016-04-16 05:51:50', '2016-04-16 05:51:50', 11, 'AdmAuth', 'addauth'),
(14, '2016-04-16 05:51:50', '2016-04-16 05:51:50', 11, 'AdmAuth', 'list'),
(15, '2016-04-16 05:51:50', '2016-04-16 05:51:50', 11, 'AdmAuthNode', 'index'),
(16, '2016-04-16 05:51:50', '2016-04-16 05:51:50', 11, 'AdmAuthNode', 'add'),
(17, '2016-04-16 05:51:50', '2016-04-16 05:51:50', 11, 'AdmAuthNode', 'addauth'),
(18, '2016-04-16 05:51:50', '2016-04-16 05:51:50', 11, 'AdmAuthNode', 'updateAuths'),
(19, '2016-04-16 05:51:50', '2016-04-16 05:51:50', 11, 'AdmAuthNode', 'edit'),
(20, '2016-04-16 05:51:50', '2016-04-16 05:51:50', 11, 'AdmAuthNode', 'modify'),
(21, '2016-04-16 05:51:50', '2016-04-16 05:51:50', 11, 'AdmAuthNode', 'list'),
(22, '2016-04-16 05:51:50', '2016-04-16 05:51:50', 11, 'AdmMenu', 'index'),
(23, '2016-04-16 05:51:50', '2016-04-16 05:51:50', 11, 'AdmMenu', 'add'),
(24, '2016-04-16 05:51:50', '2016-04-16 05:51:50', 11, 'AdmMenu', 'edit'),
(25, '2016-04-16 05:51:50', '2016-04-16 05:51:50', 11, 'AdmMenu', 'modify'),
(26, '2016-04-16 05:51:50', '2016-04-16 05:51:50', 11, 'AdmMenu', 'list'),
(27, '2016-04-16 05:51:50', '2016-04-16 05:51:50', 11, 'AdmUser', 'index'),
(28, '2016-04-16 05:51:50', '2016-04-16 05:51:50', 11, 'AdmUser', 'add'),
(29, '2016-04-16 05:51:50', '2016-04-16 05:51:50', 11, 'AdmUser', 'addGrade'),
(30, '2016-04-16 05:51:50', '2016-04-16 05:51:50', 11, 'AdmUser', 'modifyauth'),
(31, '2016-04-16 05:51:50', '2016-04-16 05:51:50', 11, 'AdmUser', 'forbidden'),
(32, '2016-04-16 05:51:50', '2016-04-16 05:51:50', 11, 'AdmUser', 'checkname'),
(33, '2016-04-16 05:51:50', '2016-04-16 05:51:50', 11, 'AdmUser', 'delauth'),
(34, '2016-04-16 05:51:50', '2016-04-16 05:51:50', 11, 'AdmUser', 'saverole'),
(35, '2016-04-16 05:51:50', '2016-04-16 05:51:50', 11, 'AdmUser', 'deleteuserauth'),
(36, '2016-04-16 05:51:50', '2016-04-16 05:51:50', 11, 'Entity', 'index'),
(37, '2016-04-16 05:51:50', '2016-04-16 05:51:50', 11, 'Entity', 'indexSubmit'),
(38, '2016-04-16 05:51:50', '2016-04-16 05:51:50', 11, 'Entity', 'ajaxQueryEntity'),
(39, '2016-04-16 05:51:50', '2016-04-16 05:51:50', 11, 'Entity', 'ajaxQueryTableName'),
(40, '2016-04-16 05:51:50', '2016-04-16 05:51:50', 0, 'Include', 'showimg'),
(41, '2016-04-16 05:51:50', '2016-04-16 05:51:50', 0, 'Include', 'showHtmlImage'),
(42, '2016-04-16 05:51:50', '2016-04-16 05:51:50', 0, 'Index', 'index'),
(43, '2016-04-16 05:51:50', '2016-04-16 05:51:50', 0, 'Index', 'notlogin'),
(44, '2016-04-16 05:51:50', '2016-04-16 05:51:50', 0, 'Index', 'noauth'),
(45, '2016-04-16 05:51:50', '2016-04-16 05:51:50', 0, 'Index', 'login'),
(46, '2016-04-16 05:51:50', '2016-04-16 05:51:50', 0, 'Index', 'logout'),
(47, '2016-04-16 05:51:50', '2016-04-16 05:51:50', 11, 'Log', 'index'),
(48, '2016-04-16 05:51:50', '2016-04-16 05:51:50', 11, 'Log', 'operate'),
(49, '2016-04-16 05:51:50', '2016-04-16 05:51:50', 11, 'Log', 'getLogDetail'),
(50, '2016-04-16 05:51:50', '2016-04-16 05:51:50', 0, 'TestClass', 'index'),
(51, '2016-04-16 05:51:50', '2016-04-16 05:51:50', 0, 'TestClass', 'add'),
(52, '2016-04-16 05:51:50', '2016-04-16 05:51:50', 0, 'TestClass', 'delete'),
(53, '2016-04-16 05:51:50', '2016-04-16 05:51:50', 0, 'TestClass', 'list'),
(54, '2016-04-16 05:51:50', '2016-04-16 05:51:50', 0, 'TestClass', 'export');

-- --------------------------------------------------------

--
-- 表的结构 `adm_auths`
--

DROP TABLE IF EXISTS `adm_auths`;
CREATE TABLE IF NOT EXISTS `adm_auths` (
  `id` int(10) unsigned NOT NULL,
  `ctime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `utime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `name` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- 转存表中的数据 `adm_auths`
--

INSERT INTO `adm_auths` (`id`, `ctime`, `utime`, `name`) VALUES
(11, '2016-04-16 05:52:58', '2016-04-16 05:52:58', '系统管理');

-- --------------------------------------------------------

--
-- 表的结构 `adm_loginlog`
--

DROP TABLE IF EXISTS `adm_loginlog`;
CREATE TABLE IF NOT EXISTS `adm_loginlog` (
  `id` int(10) NOT NULL,
  `username` varchar(32) NOT NULL,
  `ip` varchar(15) NOT NULL,
  `login_time` datetime DEFAULT CURRENT_TIMESTAMP,
  `result` tinyint(3) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4;

--
-- 转存表中的数据 `adm_loginlog`
--

INSERT INTO `adm_loginlog` (`id`, `username`, `ip`, `login_time`, `result`) VALUES
(1, '315836628', '127.0.0.1', '2016-04-16 06:26:34', 0),
(2, '315836628', '127.0.0.1', '2016-04-16 06:27:15', 1),
(3, 'dingdejing', '127.0.0.1', '2016-04-16 06:27:25', 1),
(4, 'qq', '127.0.0.1', '2016-04-16 06:27:39', 1),
(5, 'qq', '127.0.0.1', '2016-04-16 06:31:00', 1),
(6, 'dingdejing', '127.0.0.1', '2016-04-16 06:36:25', 1),
(7, 'dingdejing', '127.0.0.1', '2016-04-16 07:24:20', 0),
(8, 'dingdejing', '127.0.0.1', '2016-04-16 07:24:35', 1),
(9, 'dingdejing', '127.0.0.1', '2016-04-17 08:42:21', 1),
(10, 'dingdejing', '127.0.0.1', '2016-04-17 08:42:46', 1);

-- --------------------------------------------------------

--
-- 表的结构 `adm_menus`
--

DROP TABLE IF EXISTS `adm_menus`;
CREATE TABLE IF NOT EXISTS `adm_menus` (
  `id` int(10) unsigned NOT NULL,
  `ctime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `utime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `name` varchar(25) NOT NULL DEFAULT '',
  `url` varchar(128) NOT NULL DEFAULT '',
  `sort` tinyint(4) NOT NULL,
  `oneclass` varchar(35) NOT NULL COMMENT '一级导航',
  `groupid` int(11) NOT NULL,
  `aid` int(4) NOT NULL,
  `curr_menu` char(75) NOT NULL DEFAULT '',
  `curr_submenu` char(75) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- 转存表中的数据 `adm_menus`
--

INSERT INTO `adm_menus` (`id`, `ctime`, `utime`, `name`, `url`, `sort`, `oneclass`, `groupid`, `aid`, `curr_menu`, `curr_submenu`) VALUES
(11, '2014-07-28 01:44:30', '2014-07-28 01:44:30', '菜单管理', '/AdmMenu/list', 0, '系统管理', 11, 11, 'manage', 'manage_admmenu'),
(12, '2014-07-28 02:23:05', '2014-07-28 02:23:05', '后台用户', '/AdmUser/index/', 0, '系统管理', 11, 11, 'manage', 'manage_admuser'),
(13, '2014-07-28 02:24:30', '2014-07-28 02:24:30', '权限管理', '/AdmAuth/list', 0, '系统管理', 11, 11, 'manage', 'manage_admauth'),
(14, '2014-07-28 02:26:10', '2014-07-28 02:26:10', '权限节点', '/AdmAuthNode/list', 0, '系统管理', 11, 11, 'manage', 'manage_admauthnode'),
(15, '2014-07-28 23:34:33', '2014-07-28 23:34:33', '登陆日志', '/Log/index', 0, '系统管理', 11, 11, 'manage', 'manage_log'),
(16, '2014-07-28 23:39:02', '2014-07-28 23:39:02', '操作日志', '/Log/operate', 0, '系统管理', 11, 11, 'manage', 'manage_operate');

-- --------------------------------------------------------

--
-- 表的结构 `adm_userauth`
--

DROP TABLE IF EXISTS `adm_userauth`;
CREATE TABLE IF NOT EXISTS `adm_userauth` (
  `id` int(10) unsigned NOT NULL,
  `ctime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `utime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user` varchar(32) NOT NULL DEFAULT '',
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `aid` int(10) unsigned NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- 转存表中的数据 `adm_userauth`
--

INSERT INTO `adm_userauth` (`id`, `ctime`, `utime`, `user`, `uid`, `aid`) VALUES
(11, '2016-04-16 05:57:25', '2016-04-16 05:57:25', 'dingdejing', 11, 11);

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
(11, '管理员', 'dingdejing', '技术', '工程师', '超级管理员', 1),
(12, '315836628', 'qq', '技术', '员工', '编辑', 1);

-- --------------------------------------------------------

--
-- 表的结构 `sys_dbcache`
--

DROP TABLE IF EXISTS `sys_dbcache`;
CREATE TABLE IF NOT EXISTS `sys_dbcache` (
  `skey` varchar(32) NOT NULL,
  `expiry` int(11) NOT NULL,
  `value` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- 转存表中的数据 `sys_dbcache`
--

INSERT INTO `sys_dbcache` (`skey`, `expiry`, `value`) VALUES
('0cc175b9c0f1b6a831c399e269772661', 1460881100, 's:7:"DBCache";');

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
('adm_authnode', 54, 1),
('adm_auths', 11, 1),
('adm_menugroup', 11, 1),
('adm_menus', 16, 1),
('adm_userauth', 11, 1),
('adm_users', 12, 1),
('test_table', 14, 1),
('users', 100001, 1);

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

--
-- 转存表中的数据 `sys_sessions`
--

INSERT INTO `sys_sessions` (`skey`, `expiry`, `value`) VALUES
('ehe7d4stv6q0f55ffdo9g0vu83', 1460891236, 'security_code|s:4:"EGJL";adminUser|s:10:"dingdejing";'),
('vc1fa0ja5kuf64u9rpuintsj41', 1460801989, 'a|i:1;');

-- --------------------------------------------------------

--
-- 表的结构 `test_table`
--

DROP TABLE IF EXISTS `test_table`;
CREATE TABLE IF NOT EXISTS `test_table` (
  `id` int(10) unsigned NOT NULL,
  `ctime` datetime DEFAULT CURRENT_TIMESTAMP,
  `utime` datetime DEFAULT CURRENT_TIMESTAMP,
  `tdata` datetime DEFAULT CURRENT_TIMESTAMP COMMENT '时间',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT 'status',
  `tableidname` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'idname',
  `name` varchar(20) NOT NULL DEFAULT '' COMMENT 'test'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='测试';

--
-- 转存表中的数据 `test_table`
--

INSERT INTO `test_table` (`id`, `ctime`, `utime`, `tdata`, `status`, `tableidname`, `name`) VALUES
(12, '2016-04-17 08:50:27', '2016-04-17 08:50:27', '2015-01-01 12:12:12', 1, 0, '2222'),
(13, '2016-04-17 08:51:08', '2016-04-17 08:51:08', '2015-01-01 12:12:15', 1, 12, '666'),
(14, '2016-04-17 08:51:46', '2016-04-17 08:51:46', '2015-01-01 12:12:12', 1, 13, '666');

-- --------------------------------------------------------

--
-- 表的结构 `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) unsigned NOT NULL,
  `ctime` datetime DEFAULT CURRENT_TIMESTAMP,
  `utime` datetime DEFAULT CURRENT_TIMESTAMP,
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
(100001, '2016-04-17 09:06:25', '2016-04-17 09:06:48', '15652202721', '夏雨晴空', '666', '666', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `adm_actlog`
--
ALTER TABLE `adm_actlog`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `adm_auths`
--
ALTER TABLE `adm_auths`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `adm_loginlog`
--
ALTER TABLE `adm_loginlog`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `adm_menus`
--
ALTER TABLE `adm_menus`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `adm_userauth`
--
ALTER TABLE `adm_userauth`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user` (`user`);

--
-- Indexes for table `adm_users`
--
ALTER TABLE `adm_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ename` (`ename`);

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
  ADD PRIMARY KEY (`skey`),
  ADD KEY `sessions_expiry` (`expiry`);

--
-- Indexes for table `test_table`
--
ALTER TABLE `test_table`
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
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=11;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
