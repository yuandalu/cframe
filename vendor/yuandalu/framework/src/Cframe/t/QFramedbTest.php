<?php
class QFramedbTest extends UnitTestCase {
	private $arr_master_config = array(
	"driver"=>"mysql",
	"host"=>"127.0.0.1",
	"port"=>3306,
	"username"=>"root",
	"password"=>"",
	"database"=>"test",
	"charset"=>"gbk",
	"unix_socket"=>"",
	"options"=>array()
	);

	private $arr_slave_config = array(
	"driver"=>"mysql",
	"host"=>"127.0.0.3",
	"port"=>3306,
	"username"=>"root",
	"password"=>"",
	"database"=>"test",
	"charset"=>"gbk",
	"unix_socket"=>"",
	"options"=>array()
	);

	private $db;

	public function __construct($title = false) {
		$this->UnitTestCase($title);
	}

	public function testInit() {
		try {
			$this->db = QFrameDB::getInstance($this->arr_master_config);
		} catch (QFrameDBException $e) {
			print $e->getMessage();
			exit;
		}

		$this->db->setDebug(true);
		$this->db->setOptimize(true);

		$sql = "DROP TABLE IF EXISTS `user`";
		$this->assertTrue($this->db->execute($sql));

		$sql = "CREATE TABLE IF NOT EXISTS `user` (`userid` int(10) unsigned NOT NULL auto_increment,`name` varchar(20) NOT NULL, PRIMARY KEY  (`userid`)) ENGINE=InnoDB DEFAULT CHARSET=gbk";
		$this->assertTrue($this->db->execute($sql));
	}

	public function testInstance() {
		$this->assertTrue(QFrameDB::getInstance($this->arr_master_config) === QFrameDB::getInstance($this->arr_master_config));
		$this->assertTrue(QFrameDB::getInstance() instanceof QFrameDBPDO);
		$this->assertNotEqual(QFrameDB::getInstance($this->arr_master_config), QFrameDB::getInstance($this->arr_slave_config));
	}

	public function testCRUD() {
		$sql = "CREATE INDEX `idx_name` ON `user` (`name`)";
		$this->assertTrue($this->db->execute($sql));

		$sql = "insert into user set name=?";
		$userid = $this->db->execute($sql, "liuchenguang");
		$this->assertEqual($userid, 1);

		$sql = "insert into user set name=?";
		$userid = $this->db->execute($sql, "liuchenxing");
		$this->assertEqual($userid, 2);

		$sql = "select name from user where userid=?";
		$this->assertEqual($this->db->getOne($sql, 1), "liuchenguang");

		$sql = "update user set name=? where userid=?";
		$row = $this->db->execute($sql, array("chenguangliu", 1));
		$this->assertEqual($row, 1);

		$sql = "select name from user where userid=?";
		$this->assertEqual($this->db->getOne($sql, 1), "chenguangliu");

		$sql = "delete from user where userid=?";
		$row = $this->db->execute($sql, 1);
		$this->assertEqual($row, 1);
		$this->assertEqual($this->db->getOne("select count(*) from user"), 1);

		$sql = "delete from user where userid=?";
		$this->db->execute($sql, 2);
		$this->assertEqual($this->db->getOne("select count(*) from user"), 0);
	}

	public function testQuery() {
		$sql = "insert into user set name=?";
		$userid = $this->db->execute($sql, "liuchenxing");
		$this->assertEqual($userid, 3);

		$sql = "truncate table user";
		$this->assertTrue($this->db->execute($sql));

		$users = array("liuchenguang", "chenguangliu", "lcg");
		$sql = "insert into user (name) values (?)";
		foreach($users as $user) {
			$this->db->execute($sql, $user);
		}

		$sql = "select count(*) from user";
		$this->assertTrue($this->db->getOne($sql), 3);

		$sql = "select name from user where userid=?";

		$this->assertEqual($this->db->getRow($sql, 4), array("name"=>"liuchenguang"));

		$sql = "select name from user order by userid asc";
		$this->assertEqual($this->db->getAll($sql), array(array("name"=>"liuchenguang"), array("name"=>"chenguangliu"), array("name"=>"lcg")));

		$data = array();
		$rs  = $this->db->query($sql);
		while ($record = $rs->fetch()) {
			$data[] = $record;
		}

		$this->assertEqual($this->db->getAll($sql), $data);

		$this->db->setFetchMode(PDO::FETCH_NUM);
		$this->assertEqual($this->db->getAll($sql), array(array("liuchenguang"), array("chenguangliu"), array("lcg")));

		$sql = "select count(*) from user where userid=(select userid from user limit 1)";
		$this->db->getAll($sql);
	}

	public function testSafe() {
		$sql = "select * from user where 1=1";
		try {
			$this->db->getAll($sql);
		} catch (QFrameDBException $e) {
			$this->assertPattern("/SQL注入/", $e->getMessage());
		}

		$sql = "insert into user set name='aaa'";
		try {
			$this->db->execute($sql);
		} catch (QFrameDBException $e) {
			$this->assertPattern("/SQL注入/", $e->getMessage());
		}

		$sql = "delete from user";
		try {
			$this->db->execute($sql);
		} catch (QFrameDBException $e) {
			$this->assertPattern("/SQL注入/", $e->getMessage());
		}

		$sql = "select * from user where userid=rand()";
		$this->db->query($sql);
	}

	public function testTrans() {
		try {
			$this->db->startTrans();
			$this->db->startTrans();
		} catch (QFrameDBException $e) {
			$this->assertPattern("/之前开启的事务尚未结束，事务处理不能嵌套操作/", $e->getMessage());
			$this->db->rollback();
		}

		try {
			$this->db->commit();
		} catch (QFrameDBException $e) {
			$this->assertPattern("/之前开启的事务已经被提交或没有开启，请仔细查看事务处理过程中的操作语句/", $e->getMessage());
		}

		try {
			$this->db->rollback();
		} catch (QFrameDBException $e) {
			$this->assertPattern("/之前开启的事务已经被提交或没有开启，请仔细查看事务处理过程中的操作语句/", $e->getMessage());
		}

		try {
			$this->db->execute("set autocommit=1");
		} catch (QFrameDBException $e) {
			$this->assertPattern("/为避免操作异常，请使用包装后的事务处理接口/", $e->getMessage());
		}

		try {
			$this->db->execute("set                     autocommit=1");
		} catch (QFrameDBException $e) {
			$this->assertPattern("/为避免操作异常，请使用包装后的事务处理接口/", $e->getMessage());
		}

		$sql = "DROP TABLE `userinfo`";
		$this->db->execute($sql);
		$sql = "CREATE TABLE IF NOT EXISTS `userinfo` (`userid` int(10) unsigned NOT NULL,`birth` int(11) default NULL,PRIMARY KEY  (`userid`)) ENGINE=InnoDB DEFAULT CHARSET=gbk";
		$this->db->execute($sql);

		//成功事务
		try {
			$this->db->startTrans();

			$userid = $this->db->execute("insert into user set name=?", "xiaoliu");
			$this->db->execute("insert into userinfo set userid=?, birth=?", array($userid, 198132));

			$this->db->commit();
		} catch (QFrameDBException $e) {
			print $e->getMessage()."\n";
			$this->db->rollback();
		}

		$this->assertEqual($this->db->getOne("select name from user where userid=?", $userid), "xiaoliu");
		$this->assertEqual($this->db->getOne("select birth from userinfo where userid=?", $userid), 198132);

		//失败事务
		try {
			$this->db->startTrans();

			$userid = $this->db->execute("insert into user set name=?", "xiaoliu");

			$this->db->execute("insert into userinfo set userid=?, birth=?", array(7, 198132));

			$this->db->commit();
		} catch (QFrameDBException $e) {
			$this->assertPattern("/Duplicate entry/", $e->getMessage());
			$this->db->rollback();
		}

		$this->assertEqual($this->db->getOne("select name from user where userid=?", $userid), 0);
		$this->assertEqual($this->db->getOne("select birth from userinfo where userid=?", $userid), 0);

		//连接超时
		//事务正常写法测试
		$this->db->setWaitTimeOut(1);
		try {
			$this->db->startTrans();

			$userid = $this->db->execute("insert into user set name=?", "xiaoliu");

			$this->db->execute("insert into userinfo set userid=?, birth=?", array(7, 198132));

			$this->db->commit();
		} catch (QFrameDBException $e) {
			$this->assertPattern("/Duplicate entry/", $e->getMessage());
			$this->db->rollback();
		}

		$this->assertEqual($this->db->getOne("select name from user where userid=?", $userid), 0);
		$this->assertEqual($this->db->getOne("select birth from userinfo where userid=?", $userid), 0);

		//事务错误写法测试
		try {
			$this->db->startTrans();

			$userid = $this->db->execute("insert into user set name=?", "xiaoliu");

			$this->db->execute("insert into userinfo set userid=?, birth=?", array(7, 198132));

			sleep(2);

			$this->db->execute("insert into user set name=?", "_cuowu1");
		} catch (QFrameDBException $e) {
			$this->assertPattern("/Duplicate entry/", $e->getMessage());
		}

		sleep(2);
		try {
			$this->db->execute("insert into user set name=?", "_cuowu2");
		} catch (QFrameDBException $e) {
			print $e->getMessage()."\n";
		}

		try {
			$this->db->rollback();
		} catch (QFrameDBException $e) {
			print $e->getMessage();
		}

		$this->assertEqual($this->db->getOne("select name from user where userid=?", $userid), 0);
		$this->assertEqual($this->db->getOne("select birth from userinfo where userid=?", $userid), 0);

		$this->assertEqual($this->db->getOne("select count(*) from user where name=?", "_cuowu1"), 0);
		$this->assertEqual($this->db->getOne("select count(*) from user where name=?", "_cuowu2"), 0);
		
		$this->db->setWaitTimeOut(10);
	}

	public function testQuick() {
		$userid = $this->db->insert("user", array("name"=>"haha"));
		$this->assertTrue($userid);

		$this->assertTrue($this->db->update("user", array("name"=>"kaka"), "userid=?", $userid));
		$this->assertTrue($this->db->delete("user", "userid=?", $userid));
	}

	public function testOther() {
		$this->assertEqual($this->db->quote(true), "'1'");
		$this->assertEqual($this->db->quote("'s"), "'\'s'");

		$userid = $this->db->execute("insert into user set name='aaa'", null, false);
		$this->assertEqual($userid, $this->db->getInsertId());

		$this->db->close();

		$this->assertTrue($this->db->getOne("select 1"));

		//timeout
		//普通模式，关闭自动重连
		$this->db->setWaitTimeOut(1);
		$this->db->setAutoReconnect(false);
		try {
			sleep(2);

			$this->db->getOne("select 1");
		} catch (QFrameDBException $e) {
			print $e->getMessage()."\n";
			$this->assertPattern("/Lost connection/", $e->getMessage());
		}

		$this->assertTrue($this->db->getOne("select 1"));

		//普通模式，开启自动重连
		$this->db->setAutoReconnect(true);
		$this->assertEqual($this->db->getOne("select 1"), 1);
		sleep(2);
		$this->assertEqual($this->db->getOne("select 1"), 1);
	}



	public function testClean() {
		$sql = "DROP TABLE IF EXISTS `user`";
		$this->assertTrue($this->db->execute($sql));
	}
}
?>