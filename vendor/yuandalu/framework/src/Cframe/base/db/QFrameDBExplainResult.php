<?php
class QFrameDBExplainResult
{
	private static $result;

	public static function draw($result)
	{/*{{{*/
		self::$result = $result;

		if(php_sapi_name() != 'cli') {
			self::drawHTML();
		} else {
			self::drawConsole();
		}
	}/*}}}*/

	public static function drawHTML()
	{/*{{{*/
		print "<pre>\n";
		self::drawConsole();
		print "</pre>\n";
	}/*}}}*/

	public static function drawConsole()
	{/*{{{*/
		$arr_max_length = array();
		foreach(array_keys(self::$result[0]) as $value) {
			$arr_max_length[] = strlen($value);
		}

		foreach (self::$result as $record) {
			$i = 0;
			foreach ($record as $value) {
				$arr_max_length[$i] = (isset($arr_max_length[$i]) ? max($arr_max_length[$i], strlen($value)) : strlen($value)) + 2;
				$i++;
			}
		}

		//draw title
		self::drawLine($arr_max_length);
		self::drawData(array_keys(self::$result[0]), $arr_max_length);
		//draw data
		foreach(self::$result as $record) {
			self::drawLine($arr_max_length);
			self::drawData(array_values($record), $arr_max_length);
		}

		self::drawLine($arr_max_length);
	}/*}}}*/

	public static function drawLine($arr_length_list)
	{/*{{{*/
		print "+";
		foreach($arr_length_list as $length) {
			print str_repeat("-", $length)."+";
		}
		print "\n";
	}/*}}}*/

	public static function drawData($arr_record_list, $arr_length_list)
	{/*{{{*/
		print "|";
		$left = 0;
		foreach ($arr_record_list as $i=>$value) {
			$space  = floor(($arr_length_list[$i] - strlen($value)) / 2);
			$left  += $space;
			$right  = $arr_length_list[$i] - $space;
			$format = '%'.$space.'s%-'.$right.'s|';

			printf($format, "", $value);
			$left  -= $space;
			$left  += $arr_length_list[$i];
		}
		print "\n";
	}/*}}}*/
}
?>