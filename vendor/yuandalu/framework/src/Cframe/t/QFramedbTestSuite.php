<?php
set_time_limit(0);

ini_set("include_path", ini_get("include_path").":/usr/home/liuchenguang:../base/");

function __autoload($class) {
	include_once("db/{$class}.php");
}

include_once("simpletest/autorun.php");
//include_once("QFramedb.php");

class AllTests extends TestSuite {
	function AllTests() {
		$files = array("");
		$this->TestSuite('QFrameDB');
		$dir = opendir(dirname(__FILE__));
		while(false != ($entry = readdir($dir))) {
			if(is_file(dirname(__FILE__)."/".$entry) && !in_array($entry, array(".", "..")) && $entry != basename(__FILE__) && end(explode(".", $entry)) != "swp" && !preg_match("/mock/i", $entry)) {
				$this->addTestFile(dirname(__FILE__)."/".$entry);
			}
		}
	}
}
?>