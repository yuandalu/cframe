SET NAMES UTF8;
INSERT INTO sys_idgenter(obj,id,step)VALUES('test_table',100000,1);
DROP TABLE IF EXISTS test_table;
create table `test_table` (
`id` int unsigned NOT NULL,
`ctime` datetime DEFAULT CURRENT_TIMESTAMP,
`utime` datetime DEFAULT CURRENT_TIMESTAMP,
`name_test` varchar(255) NOT NULL DEFAULT '' COMMENT 'name',
`userid` int unsigned NOT NULL DEFAULT '0' COMMENT '用户',
`sort` int unsigned NOT NULL DEFAULT '0' COMMENT '排序',
`status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '状态',
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='测试';