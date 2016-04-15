SET NAMES UTF8;
INSERT INTO sys_idgenter(obj,id,step)VALUES('test_table',100,1);
DROP TABLE IF EXISTS test_table;
create table `test_table` (
`id` int unsigned NOT NULL,
`ctime` datetime DEFAULT CURRENT_TIMESTAMP,
`utime` datetime DEFAULT CURRENT_TIMESTAMP,
`tdata` datetime DEFAULT CURRENT_TIMESTAMP COMMENT '时间',
`status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT 'status',
`tableidname` int unsigned NOT NULL DEFAULT '0' COMMENT 'idname',
`name` varchar(20) NOT NULL DEFAULT '' COMMENT 'test',
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='测试';