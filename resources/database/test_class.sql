SET NAMES UTF8;
INSERT INTO sys_idgenter(obj,id,step)VALUES('test_class',100000,1);
DROP TABLE IF EXISTS test_class;
create table `test_class` (
`id` int unsigned NOT NULL,
`ctime` datetime NOT NULL,
`utime` datetime NOT NULL,
`testdatetime` datetime NOT NULL COMMENT 'datetime',
`testdata` date NOT NULL COMMENT 'data',
`testtime` time NOT NULL COMMENT 'time',
`testint` int unsigned NOT NULL DEFAULT '0' COMMENT 'int',
`testtinyint` tinyint unsigned NOT NULL DEFAULT '1' COMMENT 'tinyint',
`testvarchar` varchar(32) NOT NULL DEFAULT 'default' COMMENT 'varchar',
`testint_table` int unsigned NOT NULL DEFAULT '0' COMMENT 'int_table',
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='测试';