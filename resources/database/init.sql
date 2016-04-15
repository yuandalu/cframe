-- phpMyAdmin SQL Dump
-- version 4.2.10
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: 2016-04-15 20:17:03
-- 服务器版本： 5.6.21
-- PHP Version: 5.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `site_main`
--

-- --------------------------------------------------------

--
-- 表的结构 `adm_actlog`
--

CREATE TABLE IF NOT EXISTS `adm_actlog` (
`id` int(10) NOT NULL,
  `username` varchar(28) CHARACTER SET utf8 NOT NULL,
  `action` varchar(175) CHARACTER SET utf8 NOT NULL,
  `content` text CHARACTER SET utf8 COMMENT '操作内容',
  `action_time` datetime NOT NULL,
  `ip` varchar(172) CHARACTER SET utf8 NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=39479 DEFAULT CHARSET=utf8mb4;

--
-- 转存表中的数据 `adm_actlog`
--

INSERT INTO `adm_actlog` (`id`, `username`, `action`, `content`, `action_time`, `ip`) VALUES
(39401, '315836628', '修改栏目[]', 'a:2:{s:6:"action";s:14:"修改栏目[]";s:7:"content";a:7:{s:8:"oneclass";s:12:"系统管理";s:4:"name";s:12:"登陆日志";s:3:"aid";s:2:"11";s:4:"sort";s:1:"0";s:3:"url";s:10:"/log/index";s:9:"curr_menu";s:3:"log";s:12:"curr_submenu";s:9:"log_index";}}', '2014-07-28 23:35:31', '127.0.0.1'),
(39402, '315836628', '修改栏目[]', 'a:2:{s:6:"action";s:14:"修改栏目[]";s:7:"content";a:7:{s:8:"oneclass";s:12:"系统管理";s:4:"name";s:12:"登陆日志";s:3:"aid";s:2:"11";s:4:"sort";s:1:"0";s:3:"url";s:10:"/log/index";s:9:"curr_menu";s:3:"log";s:12:"curr_submenu";s:3:"log";}}', '2014-07-28 23:36:12', '127.0.0.1'),
(39403, '315836628', '修改程序权限[20]', 'a:2:{s:6:"action";s:22:"修改程序权限[20]";s:7:"content";a:3:{s:5:"contr";s:4:"User";s:6:"action";s:4:"list";s:3:"aid";s:2:"12";}}', '2014-07-28 23:40:43', '127.0.0.1'),
(39404, '315836628', '修改栏目[]', 'a:2:{s:6:"action";s:14:"修改栏目[]";s:7:"content";a:7:{s:8:"oneclass";s:12:"系统管理";s:4:"name";s:12:"操作日志";s:3:"aid";s:2:"11";s:4:"sort";s:1:"0";s:3:"url";s:12:"/log/operate";s:9:"curr_menu";s:6:"manage";s:12:"curr_submenu";s:7:"operate";}}', '2014-07-28 23:52:19', '127.0.0.1'),
(39405, '315836628', '修改栏目[]', 'a:2:{s:6:"action";s:14:"修改栏目[]";s:7:"content";a:7:{s:8:"oneclass";s:12:"系统管理";s:4:"name";s:12:"登陆日志";s:3:"aid";s:2:"11";s:4:"sort";s:1:"0";s:3:"url";s:10:"/log/index";s:9:"curr_menu";s:6:"manage";s:12:"curr_submenu";s:3:"log";}}', '2014-07-28 23:52:31', '127.0.0.1'),
(39406, '315836628', '修改栏目[菜单管理]', 'a:2:{s:6:"action";s:26:"修改栏目[菜单管理]";s:7:"content";a:7:{s:8:"oneclass";s:12:"用户管理";s:4:"name";s:12:"用户列表";s:3:"aid";s:2:"12";s:4:"sort";s:1:"0";s:3:"url";s:10:"/user/list";s:9:"curr_menu";s:4:"user";s:12:"curr_submenu";s:10:"user_index";}}', '2014-07-29 00:18:24', '127.0.0.1'),
(39407, '315836628', '修改栏目[菜单管理]', 'a:2:{s:6:"action";s:26:"修改栏目[菜单管理]";s:7:"content";a:7:{s:8:"oneclass";s:12:"系统管理";s:4:"name";s:12:"操作日志";s:3:"aid";s:2:"12";s:4:"sort";s:1:"0";s:3:"url";s:12:"/log/operate";s:9:"curr_menu";s:6:"manage";s:12:"curr_submenu";s:7:"operate";}}', '2014-07-29 00:18:58', '127.0.0.1'),
(39408, '315836628', '修改栏目[菜单管理]', 'a:2:{s:6:"action";s:26:"修改栏目[菜单管理]";s:7:"content";a:7:{s:8:"oneclass";s:12:"系统管理";s:4:"name";s:12:"操作日志";s:3:"aid";s:2:"11";s:4:"sort";s:1:"0";s:3:"url";s:12:"/log/operate";s:9:"curr_menu";s:6:"manage";s:12:"curr_submenu";s:7:"operate";}}', '2014-07-29 00:19:54', '127.0.0.1'),
(39409, 'system', 'test', 's:4:"test";', '2016-04-14 14:42:55', '127.0.0.1'),
(39410, 'system', 'test', 's:4:"test";', '2016-04-14 14:42:58', '127.0.0.1'),
(39411, 'system', 'test', 's:4:"test";', '2016-04-14 14:42:59', '127.0.0.1'),
(39412, 'system', 'test', 's:4:"test";', '2016-04-14 14:42:59', '127.0.0.1'),
(39413, 'system', 'test', 's:4:"test";', '2016-04-14 14:42:59', '127.0.0.1'),
(39414, 'system', 'test', 's:4:"test";', '2016-04-14 14:43:00', '127.0.0.1'),
(39415, 'system', 'test', 's:4:"test";', '2016-04-14 14:43:01', '127.0.0.1'),
(39416, 'system', 'test', 's:4:"test";', '2016-04-14 14:43:01', '127.0.0.1'),
(39417, 'system', 'test', 's:4:"test";', '2016-04-14 14:45:34', '127.0.0.1'),
(39418, 'system', 'test', 's:4:"test";', '2016-04-14 14:45:44', '127.0.0.1'),
(39419, 'system', 'test', 's:4:"test";', '2016-04-14 14:45:45', '127.0.0.1'),
(39420, 'system', 'test', 's:4:"test";', '2016-04-14 14:45:45', '127.0.0.1'),
(39421, 'system', 'test', 's:4:"test";', '2016-04-14 14:45:47', '127.0.0.1'),
(39422, 'system', 'test', 's:4:"test";', '2016-04-14 14:45:47', '127.0.0.1'),
(39423, 'system', 'test', 's:4:"test";', '2016-04-14 14:45:49', '127.0.0.1'),
(39424, 'system', 'test', 's:4:"test";', '2016-04-14 14:45:50', '127.0.0.1'),
(39425, 'system', 'test', 's:4:"test";', '2016-04-14 14:45:51', '127.0.0.1'),
(39426, 'system', 'test', 's:4:"test";', '2016-04-14 14:45:52', '127.0.0.1'),
(39427, 'system', 'test', 's:4:"test";', '2016-04-14 14:45:53', '127.0.0.1'),
(39428, 'system', 'test', 's:4:"test";', '2016-04-14 14:45:54', '127.0.0.1'),
(39429, '', 'test', 's:4:"test";', '2016-04-14 14:46:09', '127.0.0.1'),
(39430, '', 'test', 's:4:"test";', '2016-04-14 14:46:10', '127.0.0.1'),
(39431, '', 'test', 's:4:"test";', '2016-04-14 14:46:11', '127.0.0.1'),
(39432, '', 'test', 's:4:"test";', '2016-04-14 14:46:11', '127.0.0.1'),
(39433, '', 'test', 's:4:"test";', '2016-04-14 14:46:12', '127.0.0.1'),
(39434, '', 'test', 's:4:"test";', '2016-04-14 14:46:13', '127.0.0.1'),
(39435, '', 'test', 's:4:"test";', '2016-04-14 14:46:13', '127.0.0.1'),
(39436, '', 'test', 's:4:"test";', '2016-04-14 14:46:13', '127.0.0.1'),
(39437, '', 'test', 's:4:"test";', '2016-04-14 14:46:14', '127.0.0.1'),
(39438, '', 'test', 's:4:"test";', '2016-04-14 14:46:14', '127.0.0.1'),
(39439, '', 'test', 's:4:"test";', '2016-04-14 14:46:14', '127.0.0.1'),
(39440, '', 'test', 's:4:"test";', '2016-04-14 14:46:15', '127.0.0.1'),
(39441, '', 'test', 's:4:"test";', '2016-04-14 14:46:15', '127.0.0.1'),
(39442, '', 'test', 's:4:"test";', '2016-04-14 14:46:16', '127.0.0.1'),
(39443, '', 'test', 's:4:"test";', '2016-04-14 14:46:16', '127.0.0.1'),
(39444, 'system', 'test', 's:4:"test";', '2016-04-14 14:46:26', '127.0.0.1'),
(39445, 'system', 'test', 's:4:"test";', '2016-04-14 14:47:14', '127.0.0.1'),
(39446, 'system', 'test', 's:4:"test";', '2016-04-14 14:47:15', '127.0.0.1'),
(39447, 'system', 'test', 's:4:"test";', '2016-04-14 14:47:16', '127.0.0.1'),
(39448, 'system', 'test', 's:4:"test";', '2016-04-14 14:47:17', '127.0.0.1'),
(39449, 'system', 'test', 's:4:"test";', '2016-04-14 14:47:19', '127.0.0.1'),
(39450, 'system', 'test', 's:4:"test";', '2016-04-14 14:47:20', '127.0.0.1'),
(39451, 'system', 'test', 's:4:"test";', '2016-04-14 14:47:43', '127.0.0.1'),
(39452, 'system', 'test', 's:4:"test";', '2016-04-14 14:47:44', '127.0.0.1'),
(39453, 'system', 'test', 's:4:"test";', '2016-04-14 14:47:46', '127.0.0.1'),
(39454, 'system', 'test', 's:4:"test";', '2016-04-14 14:47:47', '127.0.0.1'),
(39455, 'system', 'test', 's:4:"test";', '2016-04-14 14:47:47', '127.0.0.1'),
(39456, 'system', 'test', 's:4:"test";', '2016-04-14 14:47:48', '127.0.0.1'),
(39457, 'system', 'test', 's:4:"test";', '2016-04-14 14:47:48', '127.0.0.1'),
(39458, 'system', 'test', 's:4:"test";', '2016-04-14 14:47:49', '127.0.0.1'),
(39459, 'system', 'test', 's:4:"test";', '2016-04-14 14:47:49', '127.0.0.1'),
(39460, 'system', 'test', 's:4:"test";', '2016-04-14 14:47:49', '127.0.0.1'),
(39461, 'system', 'test', 's:4:"test";', '2016-04-14 14:59:54', '127.0.0.1'),
(39462, 'system', 'test', 's:4:"test";', '2016-04-14 15:00:05', '127.0.0.1'),
(39463, 'system', 'test', 's:4:"test";', '2016-04-14 15:01:16', '127.0.0.1'),
(39464, 'system', 'test', 's:4:"test";', '2016-04-14 15:01:42', '127.0.0.1'),
(39465, 'system', 'test', 's:4:"test";', '2016-04-14 15:01:56', '127.0.0.1'),
(39466, 'system', 'test', 's:4:"test";', '2016-04-14 15:05:24', '127.0.0.1'),
(39467, 'system', 'test', 's:4:"test";', '2016-04-14 15:06:45', '127.0.0.1'),
(39468, 'system', 'test', 's:4:"test";', '2016-04-14 15:36:49', '127.0.0.1'),
(39469, 'system', 'test', 's:4:"test";', '2016-04-14 15:37:06', '127.0.0.1'),
(39470, 'system', 'test', 's:4:"test";', '2016-04-14 15:37:08', '127.0.0.1'),
(39471, 'system', 'test', 's:4:"test";', '2016-04-14 15:37:09', '127.0.0.1'),
(39472, 'system', '修改栏目[]', 'a:2:{s:6:"action";s:14:"修改栏目[]";s:7:"content";a:7:{s:8:"oneclass";s:12:"系统管理";s:4:"name";s:12:"操作日志";s:3:"aid";s:2:"11";s:4:"sort";s:1:"1";s:3:"url";s:13:"/log/operates";s:9:"curr_menu";s:6:"manage";s:12:"curr_submenu";s:7:"operate";}}', '2016-04-15 14:14:05', '192.168.0.18'),
(39473, 'system', '修改栏目[]', 'a:2:{s:6:"action";s:14:"修改栏目[]";s:7:"content";a:7:{s:8:"oneclass";s:12:"系统管理";s:4:"name";s:12:"操作日志";s:3:"aid";s:2:"11";s:4:"sort";s:1:"1";s:3:"url";s:12:"/log/operate";s:9:"curr_menu";s:6:"manage";s:12:"curr_submenu";s:7:"operate";}}', '2016-04-15 14:14:11', '192.168.0.18'),
(39474, 'system', '修改栏目[]', 'a:2:{s:6:"action";s:14:"修改栏目[]";s:7:"content";a:7:{s:8:"oneclass";s:12:"系统管理";s:4:"name";s:12:"操作日志";s:3:"aid";s:2:"11";s:4:"sort";s:1:"1";s:3:"url";s:12:"/log/operate";s:9:"curr_menu";s:7:"manages";s:12:"curr_submenu";s:7:"operate";}}', '2016-04-15 14:15:42', '192.168.0.18'),
(39475, 'system', '修改栏目[]', 'a:2:{s:6:"action";s:14:"修改栏目[]";s:7:"content";a:7:{s:8:"oneclass";s:12:"系统管理";s:4:"name";s:12:"操作日志";s:3:"aid";s:2:"11";s:4:"sort";s:1:"1";s:3:"url";s:12:"/log/operate";s:9:"curr_menu";s:6:"manage";s:12:"curr_submenu";s:7:"operate";}}', '2016-04-15 14:15:47', '192.168.0.18'),
(39476, 'system', '修改栏目[]', 'a:2:{s:6:"action";s:14:"修改栏目[]";s:7:"content";a:7:{s:8:"oneclass";s:12:"系统管理";s:4:"name";s:12:"操作日志";s:3:"aid";s:2:"11";s:4:"sort";s:1:"1";s:3:"url";s:12:"/log/operate";s:9:"curr_menu";s:7:"manages";s:12:"curr_submenu";s:7:"operate";}}', '2016-04-15 14:15:52', '192.168.0.18'),
(39477, 'system', '修改栏目[]', 'a:2:{s:6:"action";s:14:"修改栏目[]";s:7:"content";a:7:{s:8:"oneclass";s:12:"系统管理";s:4:"name";s:12:"操作日志";s:3:"aid";s:2:"11";s:4:"sort";s:1:"1";s:3:"url";s:12:"/log/operate";s:9:"curr_menu";s:6:"manage";s:12:"curr_submenu";s:7:"operate";}}', '2016-04-15 14:15:56', '192.168.0.18'),
(39478, 'system', '修改程序权限[27]', 'a:2:{s:6:"action";s:22:"修改程序权限[27]";s:7:"content";a:3:{s:5:"contr";s:3:"666";s:6:"action";s:3:"123";s:3:"aid";s:2:"18";}}', '2016-04-15 19:31:00', '192.168.0.18');

-- --------------------------------------------------------

--
-- 表的结构 `adm_authnode`
--

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
(24, '2016-04-15 19:04:21', '2016-04-15 19:04:21', 18, '12', '123'),
(25, '2016-04-15 19:04:28', '2016-04-15 19:04:28', 18, '12', '123'),
(26, '2016-04-15 19:04:32', '2016-04-15 19:04:32', 18, '12', '123'),
(27, '2016-04-15 19:04:36', '2016-04-15 19:04:36', 18, '666', '123');

-- --------------------------------------------------------

--
-- 表的结构 `adm_auths`
--

CREATE TABLE IF NOT EXISTS `adm_auths` (
  `id` int(10) unsigned NOT NULL,
  `ctime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `utime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `name` varchar(15) CHARACTER SET utf8 NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- 转存表中的数据 `adm_auths`
--

INSERT INTO `adm_auths` (`id`, `ctime`, `utime`, `name`) VALUES
(11, '2014-07-28 01:38:36', '2014-07-28 01:38:36', '系统管理'),
(12, '2014-07-28 23:05:12', '2014-07-28 23:05:12', '用户管理'),
(15, '2016-04-15 18:26:09', '2016-04-15 18:26:09', 'test'),
(18, '2016-04-15 18:27:35', '2016-04-15 18:27:35', 'tests'),
(20, '2016-04-15 18:29:30', '2016-04-15 18:29:30', 'ok'),
(22, '2016-04-15 18:31:41', '2016-04-15 18:31:41', 'ssss'),
(24, '2016-04-15 18:31:59', '2016-04-15 18:31:59', 'sss'),
(34, '2016-04-15 18:36:42', '2016-04-15 18:36:42', 'sdfs'),
(35, '2016-04-15 18:36:59', '2016-04-15 18:36:59', '35'),
(37, '2016-04-15 18:38:16', '2016-04-15 18:38:16', '36'),
(39, '2016-04-15 18:38:54', '2016-04-15 18:38:54', '38'),
(43, '2016-04-15 18:41:10', '2016-04-15 18:41:10', '41'),
(44, '2016-04-15 18:42:08', '2016-04-15 18:42:08', '44'),
(45, '2016-04-15 18:43:56', '2016-04-15 18:43:56', '45'),
(46, '2016-04-15 18:44:17', '2016-04-15 18:44:17', '46'),
(47, '2016-04-15 18:44:26', '2016-04-15 18:44:26', '47'),
(48, '2016-04-15 18:44:33', '2016-04-15 18:44:33', '48'),
(49, '2016-04-15 18:45:55', '2016-04-15 18:45:55', '49'),
(50, '2016-04-15 18:46:03', '2016-04-15 18:46:03', '50'),
(51, '2016-04-15 18:48:33', '2016-04-15 18:48:33', '51ok');

-- --------------------------------------------------------

--
-- 表的结构 `adm_loginlog`
--

CREATE TABLE IF NOT EXISTS `adm_loginlog` (
`id` int(10) NOT NULL,
  `username` varchar(28) CHARACTER SET utf8 NOT NULL,
  `ip` varchar(255) CHARACTER SET utf8 NOT NULL,
  `login_time` datetime DEFAULT CURRENT_TIMESTAMP,
  `result` tinyint(3) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=37552 DEFAULT CHARSET=utf8mb4;

--
-- 转存表中的数据 `adm_loginlog`
--

INSERT INTO `adm_loginlog` (`id`, `username`, `ip`, `login_time`, `result`) VALUES
(37524, '315836628', '127.0.0.1', '2014-07-28 23:46:47', 0),
(37525, '315836628', '127.0.0.1', '2014-07-28 23:47:11', 1),
(37526, 'dingdejing', '127.0.0.1', '2014-07-29 00:04:39', 1),
(37527, '315836628', '127.0.0.1', '2014-07-29 00:05:00', 1),
(37528, 'dingdejing', '127.0.0.1', '2014-07-29 00:15:00', 1),
(37529, '315836628', '127.0.0.1', '2014-07-29 00:18:03', 1),
(37530, 'dingdejing', '127.0.0.1', '2014-07-29 00:19:16', 1),
(37531, '315836628', '127.0.0.1', '2014-07-29 00:19:43', 1),
(37532, 'dingdejing', '127.0.0.1', '2014-07-29 00:20:11', 1),
(37533, '315836628', '127.0.0.1', '2014-07-29 00:23:36', 1),
(37534, 'dingdejing', '127.0.0.1', '2014-07-29 21:30:54', 1),
(37535, 'admin', '192.168.0.18', '2016-04-15 10:40:12', 0),
(37536, 'admin', '192.168.0.18', '2016-04-15 10:41:06', 0),
(37537, 'admin', '192.168.0.18', '2016-04-15 10:41:20', 0),
(37538, 'admin', '192.168.0.18', '2016-04-15 10:41:44', 0),
(37539, 'admin', '192.168.0.18', '2016-04-15 10:43:41', 0),
(37540, '315836628', '192.168.0.18', '2016-04-15 10:43:59', 0),
(37541, 'admin', '192.168.0.18', '2016-04-15 10:46:09', 0),
(37542, 'admin', '192.168.0.18', '2016-04-15 10:46:38', 1),
(37543, 'admin', '192.168.0.18', '2016-04-15 20:00:42', 0),
(37544, 'dingdejing', '192.168.0.18', '2016-04-15 20:01:43', 0),
(37545, 'dingdejing', '192.168.0.18', '2016-04-15 20:02:14', 0),
(37546, 'zhangxiaobo', '192.168.0.18', '2016-04-15 20:02:32', 0),
(37547, 'w', '192.168.0.18', '2016-04-15 20:03:29', 0),
(37548, 'wwww', '192.168.0.18', '2016-04-15 20:03:51', 0),
(37549, 'www', '192.168.0.18', '2016-04-15 20:13:12', 1),
(37550, 'admin', '192.168.0.18', '2016-04-15 20:15:23', 1),
(37551, 'admin', '192.168.0.18', '2016-04-15 20:15:48', 1);

-- --------------------------------------------------------

--
-- 表的结构 `adm_menus`
--

CREATE TABLE IF NOT EXISTS `adm_menus` (
  `id` int(10) unsigned NOT NULL,
  `ctime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `utime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `name` varchar(25) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `url` varchar(175) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `sort` tinyint(4) NOT NULL,
  `oneclass` varchar(35) CHARACTER SET utf8 NOT NULL COMMENT '一级导航',
  `groupid` int(11) NOT NULL,
  `aid` int(4) NOT NULL,
  `curr_menu` char(75) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `curr_submenu` char(75) CHARACTER SET utf8 NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- 转存表中的数据 `adm_menus`
--

INSERT INTO `adm_menus` (`id`, `ctime`, `utime`, `name`, `url`, `sort`, `oneclass`, `groupid`, `aid`, `curr_menu`, `curr_submenu`) VALUES
(18, '2014-07-28 01:44:30', '2014-07-28 01:44:30', '菜单管理', '/AdmMenu/list', 0, '系统管理', 13, 11, 'manage', 'menu_add'),
(19, '2014-07-28 02:23:05', '2014-07-28 02:23:05', '后台用户', '/AdmUser/index/', 0, '系统管理', 13, 11, 'manage', 'manage_index'),
(20, '2014-07-28 02:24:30', '2014-07-28 02:24:30', '权限管理', '/AdmAuth/list', 0, '系统管理', 13, 11, 'manage', 'auths_add'),
(21, '2014-07-28 02:26:10', '2014-07-28 02:26:10', '权限节点', '/AdmAuthNode/list', 0, '系统管理', 13, 11, 'manage', 'controauth_add'),
(22, '2014-07-28 02:37:03', '2014-07-28 02:37:03', '用户列表', '/user/list', 0, '用户管理', 14, 12, 'user', 'user_index'),
(23, '2014-07-28 23:34:33', '2014-07-28 23:34:33', '登陆日志', '/Log/index', 0, '系统管理', 13, 11, 'manage', 'log'),
(24, '2014-07-28 23:39:02', '2014-07-28 23:39:02', '操作日志', '/Log/operate', 0, '系统管理', 13, 11, 'manage', 'operate'),
(26, '2016-04-15 14:41:41', '2016-04-15 14:41:41', '22', '22', 2, '11', 15, 12, '1', '2'),
(29, '2016-04-15 14:43:18', '2016-04-15 14:43:18', 'qw', '223', 123, '11', 15, 11, '12', '112'),
(32, '2016-04-15 14:44:06', '2016-04-15 14:44:06', 'qwaa', '223', 123, '11', 15, 11, '12', '112'),
(36, '2016-04-15 14:45:22', '2016-04-15 14:45:22', 'fdg', '223', 123, '用户管理', 14, 11, '12', '112'),
(38, '2016-04-15 14:45:42', '2016-04-15 14:45:42', 'fssdg', '223', 123, '用户管理', 14, 11, '12', '112'),
(44, '2016-04-15 14:51:34', '2016-04-15 14:51:34', '11', '11', 22, '用户管理', 14, 12, '12', '212'),
(50, '2016-04-15 14:54:54', '2016-04-15 14:54:54', 'www', 'www', 12, '11', 15, 12, 'ww', 'wwww'),
(51, '2016-04-15 14:55:08', '2016-04-15 14:55:08', 'www1', 'www', 126, '11', 15, 12, 'ww', 'wwww'),
(57, '2016-04-15 18:47:39', '2016-04-15 18:47:39', 'ssss', 'ssasd', 56, '用户管理', 14, 37, 'asdasd', 'asdasd'),
(58, '2016-04-15 18:47:57', '2016-04-15 18:47:57', 'wqewe', 'qweqwe', 12, '11', 15, 37, 'asdasda', 'sdasdasd');

-- --------------------------------------------------------

--
-- 表的结构 `adm_userauth`
--

CREATE TABLE IF NOT EXISTS `adm_userauth` (
  `id` int(10) unsigned NOT NULL,
  `ctime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `utime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user` varchar(35) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `aid` int(10) unsigned NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- 转存表中的数据 `adm_userauth`
--

INSERT INTO `adm_userauth` (`id`, `ctime`, `utime`, `user`, `uid`, `aid`) VALUES
(20, '2016-04-15 18:11:51', '2016-04-15 18:11:51', 'admin', 11, 11),
(21, '2016-04-15 18:11:51', '2016-04-15 18:11:51', 'admin', 11, 12);

-- --------------------------------------------------------

--
-- 表的结构 `adm_users`
--

CREATE TABLE IF NOT EXISTS `adm_users` (
  `id` int(10) unsigned NOT NULL,
  `name` char(10) CHARACTER SET utf8 NOT NULL,
  `ename` varchar(50) CHARACTER SET utf8 NOT NULL,
  `depart` char(10) CHARACTER SET utf8 NOT NULL,
  `position` char(10) CHARACTER SET utf8 NOT NULL,
  `role` char(10) CHARACTER SET utf8 NOT NULL,
  `status` smallint(6) NOT NULL DEFAULT '1' COMMENT '状态'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- 转存表中的数据 `adm_users`
--

INSERT INTO `adm_users` (`id`, `name`, `ename`, `depart`, `position`, `role`, `status`) VALUES
(11, '管理员', 'admin', '技术', '工程师', '超级管理员', 1),
(12, '夏雨晴空', 'dingdejing', '技术', '工程师', '管理', 2),
(14, 'test', 'test', '运营', '副总裁', '编辑', 2),
(15, '12', '231', '运营', '副总裁', '编辑', 1);

-- --------------------------------------------------------

--
-- 表的结构 `sys_dbcache`
--

CREATE TABLE IF NOT EXISTS `sys_dbcache` (
  `skey` varchar(32) CHARACTER SET utf8 NOT NULL,
  `expiry` int(11) NOT NULL,
  `value` text CHARACTER SET utf8
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- 表的结构 `sys_idgenter`
--

CREATE TABLE IF NOT EXISTS `sys_idgenter` (
  `obj` varchar(32) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `id` int(11) NOT NULL DEFAULT '1',
  `step` tinyint(4) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- 转存表中的数据 `sys_idgenter`
--

INSERT INTO `sys_idgenter` (`obj`, `id`, `step`) VALUES
('adm_authnode', 27, 1),
('adm_auths', 51, 1),
('adm_menugroup', 26, 1),
('adm_menus', 58, 1),
('adm_userauth', 21, 1),
('adm_users', 15, 1),
('users', 1000003, 1);

-- --------------------------------------------------------

--
-- 表的结构 `sys_sessions`
--

CREATE TABLE IF NOT EXISTS `sys_sessions` (
  `skey` varchar(32) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `expiry` int(11) DEFAULT NULL,
  `value` varchar(5000) CHARACTER SET utf8 DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- 转存表中的数据 `sys_sessions`
--

INSERT INTO `sys_sessions` (`skey`, `expiry`, `value`) VALUES
('rhkutjpbok61bs1ujbfmcdetd2', 1460729752, 'security_code|s:4:"CKNY";adminUser|s:5:"admin";');

-- --------------------------------------------------------

--
-- 表的结构 `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) unsigned NOT NULL,
  `ctime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `utime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `mobile` varchar(11) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `username` varchar(20) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `email` varchar(50) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `password` varchar(32) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `salt` varchar(6) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
-- Indexes for table `users`
--
ALTER TABLE `users`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `adm_actlog`
--
ALTER TABLE `adm_actlog`
MODIFY `id` int(10) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=39479;
--
-- AUTO_INCREMENT for table `adm_loginlog`
--
ALTER TABLE `adm_loginlog`
MODIFY `id` int(10) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=37552;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
