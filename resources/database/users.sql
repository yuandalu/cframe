SET NAMES UTF8;
INSERT INTO sys_idgenter(obj,id,step)VALUES('users',100000,1);
DROP TABLE IF EXISTS users;
create table `users` (
`id` int unsigned NOT NULL,
`ctime` datetime DEFAULT CURRENT_TIMESTAMP,
`utime` datetime DEFAULT CURRENT_TIMESTAMP,
`mobile` char(11) NOT NULL DEFAULT '' COMMENT '手机号',
`nickname` varchar(32) NOT NULL DEFAULT '' COMMENT '昵称',
`password` char(32) NOT NULL DEFAULT '' COMMENT '密码',
`salt` char(6) NOT NULL DEFAULT '' COMMENT '加盐',
`status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '状态',
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户';