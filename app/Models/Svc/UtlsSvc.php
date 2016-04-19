<?php

namespace App\Models\Svc;

use App\Support\Loader;

class UtlsSvc
{

    public static function filter_xml_string($simplexml_object)
    {
        return mb_convert_encoding(trim(urldecode($simplexml_object)), 'gbk', 'utf-8');
    }

    public static function u82gb($mix)
    {
        if (is_array($mix)) {
            foreach ($mix as $k => $v) {
                $mix[$k] = self::u82gb($v);
            }
        } else {
            $mix = mb_convert_encoding(str_replace('•', '·', $mix), 'gbk', 'utf-8');
        }

        return $mix;
    }

    public static function gb2u8($mix)
    {
        if (is_array($mix)) {
            foreach ($mix as $k => $v) {
                $mix[$k] = self::gb2u8($v);
            }
        } else {
            $mix = mb_convert_encoding($mix, 'utf-8', 'gbk');
            //$mix = iconv(  'gb2312', 'utf-8', $mix);
        }

        return $mix;
    }

    public static function getAct($ctl = 'index', $act = 'index', $params = array())
    {
        $result = '/' . $ctl . '/' . ($act ? ($act . '/') : '');
        if (empty($params)) {
            return $result;
        }

        $result .= '?';
        $result .= http_build_query($params);
        return $result;
    }

    public static function goToAct($ctl, $act, $params = array())
    {
        header('location:' . self::getAct($ctl, $act, $params));
        exit;
    }

    public static function obj2array($obj)
    {
        $out = array();
        foreach ($obj as $key => $val) {
            switch (true) {
                case is_object($val):
                    $out[$key] = self::obj2array($val);
                    break;
                case is_array($val):
                    $out[$key] = self::obj2array($val);
                    break;
                default:
                    $out[$key] = $val;
            }
        }
        return $out;
    }

    public static function fenToYuan($fen)
    {
        if (empty($fen) || !is_numeric($fen)) {
            return 0;
        }
        $result = number_format($fen / 100, 2, '.', '');
        return $result;
    }

    //不带小数的元
    public static function fenToYuanInt($fen)
    {
        if (empty($fen) || !is_numeric($fen)) {
            return 0;
        }
        if (($fen % 100) > 0) {
            $result = number_format($fen / 100, 2, '.', '');
        } else {
            $result = number_format($fen / 100, 0, '', '');
        }
        return $result;
    }

    public static function yuanToFen($yuan)
    {
        if (empty($yuan) || !is_numeric($yuan)) {
            return 0;
        }
        $result = number_format($yuan * 100, 0, '', '');
        return $result;
    }

    public static function call($url, $time_out = 30)
    {
        if ('' == $url) {
            return false;
        }

        $url_ary = parse_url($url);
        if (!isset($url_ary['host'])) {
            return false;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_NOPROGRESS, 1);
        curl_setopt($ch, CURLOPT_NOBODY, 0);
        curl_setopt($ch, CURLOPT_HTTPGET, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1)');

        $http_header = array();
        $http_header[] = 'Connection: close';
        $http_header[] = 'Pragma: no-cache';
        $http_header[] = 'Cache-Control: no-cache';
        $http_header[] = 'Accept: */*';
        $http_header[] = 'Host: ' . $url_ary['host'];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $http_header);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $time_out);

        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    public static function remote_get_contents($url, $expire = 600)
    {
        if ($expire < 0) {
            return UtlsSvc::simpleRequest($url);
        }
        $filename = md5($url);
        if (!is_dir($_SERVER['ENV_CACHE_DIR'] . '/remote/')) {
            mkdir($_SERVER['ENV_CACHE_DIR'] . '/remote/', 0755);
        }
        $file = $_SERVER['ENV_CACHE_DIR'] . '/remote/' . $filename;
        if (is_file($file) && (filemtime($file) + $expire > time())) {
            return file_get_contents($file);
        }
        $content = UtlsSvc::simpleRequest($url);
        if (strlen($content) > 0) {
            file_put_contents($file, $content);
            $filesize = filesize($file);
            if ($filesize < strlen($content)) {
                unlink($file);
            }
        } else {
            if (is_file($file)) {
                return file_get_contents($file);
            } else {
                return '';
            }
        }
        return $content;
    }

    public static function simpleRequest($url, $post_data = array(), $option = array())
    {
        //使用http_build_query拼接post
        if ('' == $url) {
            return false;
        }
        $url_ary = parse_url($url);
        if (!isset($url_ary['host'])) {
            return false;
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        curl_setopt($ch, CURLOPT_HEADER, ($option['CURLOPT_HEADER'] === true));
        if ($option['referer'] != '') {
            curl_setopt($ch, CURLOPT_REFERER, $option['referer']);
        }
        if (!empty($post_data)) {
            curl_setopt($ch, CURLOPT_POST, true);
            if (is_array($post_data)) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
            } else {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
            }
        }
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');

        $http_header = array();
        $http_header[] = 'Connection: close';
        $http_header[] = 'Pragma: no-cache';
        $http_header[] = 'Cache-Control: no-cache';
        $http_header[] = 'Accept: */*';
        if (isset($option['header'])) {
            foreach ($option['header'] as $header) {
                $http_header[] = $header;
            }
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, $http_header);

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if (!isset($option['timeout'])) {
            $option['timeout'] = 15;
        }

        curl_setopt($ch, CURLOPT_TIMEOUT, $option['timeout']);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    public static function array2json($arr)
    {
        return json_encode(self::gb2u8($arr));
    }

    public static function getClientIP()
    {
        if (getenv('HTTP_CLIENT_IP')) {
            $ip = getenv('HTTP_CLIENT_IP');
        } elseif (getenv('HTTP_X_FORWARDED_FOR')) {
            $ip = getenv('HTTP_X_FORWARDED_FOR');
        } elseif (getenv('HTTP_X_FORWARDED')) {
            $ip = getenv('HTTP_X_FORWARDED');
        } elseif (getenv('HTTP_FORWARDED_FOR')) {
            $ip = getenv('HTTP_FORWARDED_FOR');
        } elseif (getenv('HTTP_FORWARDED')) {
            $ip = getenv('HTTP_FORWARDED');
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    public static function getRemoteIP()
    {
        return $_SERVER['REMOTE_ADDR'];
    }

    public static function array2xml($array)
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?><root>';
        foreach ($array as $k => $v) {
            $xml .= "<" . $k . ">";
            if (is_array($v)) {
                $xml .= self::array2xml_node($v);
            } else {
                $xml .= htmlspecialchars(mb_convert_encoding($v, 'utf-8', 'gbk'));
            }
            $xml .= "</" . $k . ">";
        }
        $xml .= '</root>';
        return $xml;
    }

    private static function array2xml_node($array)
    {
        $xml = '';
        foreach ($array as $k => $v) {
            if (is_numeric($k)) {
                $k = 'item';
            }
            $xml .= "<" . $k . ">";
            if (is_array($v)) {
                $xml .= self::array2xml_node($v);
            } else {
                $xml .= htmlspecialchars(mb_convert_encoding($v, 'utf-8', 'gbk'));
            }
            $xml .= "</" . $k . ">";
        }
        return $xml;
    }

    public static function xml2array($xml)
    {
        libxml_use_internal_errors(true);
        if (empty($xml))
            return false;
        if (is_string($xml))
            $xml = simplexml_load_string($xml, null, LIBXML_NOCDATA);
        if ($xml === false)
            return false;

        $children = $xml->children();
        if (!$children)
            return (string)$xml;
        $arr = array();
        foreach ($children as $key => $node) {
            $node = self::xml2array($node);

            if ($key == 'item')
                $key = count($arr);

            // if the node is already set, put it into an array
            if (isset($arr[$key])) {
                if (!is_array($arr[$key]) || $arr[$key][0] == null)
                    $arr[$key] = array($arr[$key]);
                $arr[$key][] = $node;
            } else {
                $arr[$key] = $node;
            }
        }
        return $arr;
    }

    public static function htmlspecialcharsRecursive($value)
    {
        if (is_numeric($value)) {
            return $value;
        }
        if (is_string($value)) {
            return htmlspecialchars($value);
        }
        if (is_array($value)) {
            foreach ($value as $k => $v) {
                $value[$k] = self::htmlspecialcharsRecursive($v);
            }
            return $value;
        }
        if (is_object($value)) {

            foreach ($value as $k => $v) {
                $value->$k = self::htmlspecialcharsRecursive($v);
            }
            return $value;
        }
        return $value;
    }

    public static function tmplog($msg)
    {
        error_log("\n[" . date('H:i:s') . " " . $_SERVER['REMOTE_ADDR'] . "]" . $msg, 3, "/tmp/fawn" . date('Ymd') . ".log");
    }

    public static function validPass($password)
    {
        return preg_match('/[0-9a-f]{32}/', $password);
    }

    public static function encode_uri_json($arr)
    {
        $out = array();
        foreach ($arr as $k => $v) {
            if (is_array($v)) {
                $out[] = $k . ":'" . self::encode_uri_json($v) . "'";
            } else {
                $out[] = $k . ":'" . addslashes(self::gb2u8($v)) . "'";
            }
        }
        $output = '{' . implode(',', $out) . '}';
        return $output;
    }

    public static function showMsg($alert, $url, $time = 1.2)
    {
        $time = $time * 1000;
        echo '<!DOCTYPE html><html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><script src="/static/admin-lte/plugins/jQuery/jQuery-2.2.0.min.js"></script><script src="/static/js/msg_util.js"></script><link rel="stylesheet" href="/static/css/public.css"/></head><body><script>MsgUtil.show(\''.addslashes($alert).'\', function(){window.location.href=\''.$url.'\';}, '.$time.');</script></body></html>';
        exit;
    }

    //截取字符串
    function cutstr($string, $length, $dot = '')
    {
        $str = $string;
        $cutlen = 0;
        $cutstr = '';

        $wordLen = mb_strlen($string, 'utf-8');

        if ($length > $wordLen) {
            return $str;
        }

        for ($i = 0; $i < $length * 2; $i++) {
            $one = mb_substr($str, 0, 1, 'utf-8');
            if (strlen($one) > 1) {
                $cutlen = $cutlen + 2;
            } else {
                $cutlen = $cutlen + 1;
            }
            $cutstr .= $one;
            $str = mb_substr($str, 1, mb_strlen($str), 'utf-8');
            if ($cutlen >= $length * 2) {
                break;
            }
        }
        return $cutstr . $dot;
    }

    //截取字符串 等宽
    function cutstr1($string, $length, $dot = '')
    {
        $str = $string;
        $cutlen = 0;
        $cutstr = '';

        $wordLen = mb_strlen($string, 'utf-8');

        if ($length > $wordLen) {
            return $str;
        }

        for ($i = 0; $i < $length; $i++) {
            $one = mb_substr($str, 0, 1, 'utf-8');
            if (strlen($one) > 1) {
                $cutlen = $cutlen + 2;
            } else {
                $cutlen = $cutlen + 1;
            }
            $cutstr .= $one;
            $str = mb_substr($str, 1, mb_strlen($str), 'utf-8');
            if ($cutlen >= $length) {
                break;
            }
        }
        return $cutstr . $dot;
    }

    public static function object_array($object)
    {
        if (is_object($object)) {
            foreach ($object as $key => $value) {
                $array[$key] = self::object_array($value);
            }
        } else {
            $array = $object;
        }
        return $array;
    }

    public static function checkMobile($str)
    {
        $pattern = "/^(13|15|18|14)\d{9}$/";
        if (preg_match($pattern, $str)) {
            Return true;
        } else {
            Return false;
        }
    }

    // 获得parse_str的指定变量
    public static function get_parse_str($str, $keys)
    {
        parse_str($str);
        $result = array();
        foreach ($keys as $k => $v) {
            $result[$v] = $$v;
        }

        return $result;
    }

    public static function isIpad()
    {
        return strpos($_SERVER['HTTP_USER_AGENT'], 'iPad') !== false;
    }

    public static function isIpod()
    {
        return strpos($_SERVER['HTTP_USER_AGENT'], 'iPod') !== false;
    }

    public static function isIphone()
    {
        return strpos($_SERVER['HTTP_USER_AGENT'], 'iPhone') !== false;
    }

    public static function isIOS()
    {
        return (strpos($_SERVER['HTTP_USER_AGENT'], 'iPad') !== false) || (strpos($_SERVER['HTTP_USER_AGENT'], 'iPhone') !== false) || (strpos($_SERVER['HTTP_USER_AGENT'], 'iPod') !== false);
    }

    // 是否越狱
    public static function isJailBreak()
    {
        return true;
        if (self::isIOS() && strpos($_SERVER['HTTP_USER_AGENT'], 'JailBreak')) {
            return true;
        }

        return false;

    }

    public static function isAndroid()
    {
        return (strpos( strtolower($_SERVER['HTTP_USER_AGENT']), 'android') !== false) || (strpos($_SERVER['HTTP_USER_AGENT'], 'HttpClient') !== false);
    }

    public static function isWeixin()
    {
        $ua = strtolower($_SERVER['HTTP_USER_AGENT']);
        return strpos($ua , 'micromessenger') !== false;
    }

    public static function isAlipay()
    {
        $agent = $_SERVER['HTTP_USER_AGENT'];
        return (strpos($agent, 'Alipay') !== false || strpos($agent, 'AlipayClient') !== false || strpos($agent, 'AliApp') !== false);
    }

    public static function isMobile($pad = 1)
    {
        if ($pad != 1) {
            return self::isAndroid() || self::isIphone() || self::isIpod();
        }
        return self::isAndroid() || self::isIOS() || self::isIphone() || self::isIpod() || self::isIpad();
    }

    public static function inApple()
    {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        if (!$ip) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        if (substr($ip, 0, 3) == '17.') {
            return true;
        }
        return false;
    }

    public static function inCompany($level = 0)
    {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        if (!$ip) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        if (substr($ip, 0, 8) == '192.168.') {
            return true;
        }
        if (in_array($ip, array('127.0.0.1'))) {
            return true;
        }
        return false;
    }

    // 只读模式
    public static function isReadonly()
    {
        return env('READONLY_MODE', 'local') == '1';
    }

    public static function numToCny($num)
    {
        $capUnit = array('万', '亿', '万', '圆', '');  //单元
        $capDigit = array(2 => array('角', '分', ''), 4 => array('仟', '佰', '拾', ''));
        $capNum = array('零', '壹', '贰', '叁', '肆', '伍', '陆', '柒', '捌', '玖');
        if ((strpos(strval($num), '.') > 16) || (!is_numeric($num)))
            return '';
        $num = sprintf("%019.2f", $num);
        $CurChr = array('', '');
        for ($i = 0, $ret = '', $j = 0; $i < 5; $i++, $j = $i * 4 + floor($i / 4)) {
            $nodeNum = substr($num, $j, 4);
            for ($k = 0, $subret = '', $len = strlen($nodeNum); (($k < $len) && (intval(substr($nodeNum, $k)) != 0)); $k++) {
                $CurChr[$k % 2] = $capNum[$nodeNum{$k}] . (($nodeNum{$k} == 0) ? '' : $capDigit[$len][$k]);
                if (!(($CurChr[0] == $CurChr[1]) && ($CurChr[$k % 2] == $capNum[0])))
                    if (!(($CurChr[$k % 2] == $capNum[0]) && ($subret == '') && ($ret == '')))
                        $subret .= $CurChr[$k % 2];
            }
            $subChr = $subret . (($subret == '') ? '' : $capUnit[$i]);
            if (!(($subChr == $capNum[0]) && ($ret == ''))) {
                $ret .= $subChr;
            }
        }
        $ret = ($ret == "") ? $capNum[0] . $capUnit[3] : $ret;
        return $ret;
    }

    public static function seconds2Hms($seconds)
    {
        if ($seconds >= 3600) {
            $h = floor($seconds / 3600);
            $seconds -= $h * 3600;
        } else {
            $h = '00';
        }

        if ($seconds >= 60) {
            $m = floor($seconds / 60);
            $seconds -= $m * 60;
        } else {
            $m = '00';
            $seconds = '00';
        }
        $h = strlen($h) == 1 ? ('0' . $h) : $h;
        $m = strlen($m) == 1 ? ('0' . $m) : $m;
        $seconds = strlen($seconds) == 1 ? ('0' . $seconds) : $seconds;
        return $h . ':' . $m . ':' . $seconds;
    }

    public static function makeDir($path)
    {
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
    }

    public static function checknull($a)
    {
        if (is_array($a)) {
            foreach ($a as $k => $v) {
                if (is_array($v)) {
                    $a[$k] = self::checknull($v);
                } else {
                    if (empty($v) && $v != 0 && $v != "0") {
                        $a[$k] = '';
                    }
                }
            }
        }

        return $a;
    }

    //16进制的8位唯一码 ：oPF2aa1e
    public static function unique32($a)
    {
        for
        (
            $a = md5($a, true),
            $s = '0123456789acegikmoqsuBDFHJLNPRTV',
            $d = '',
            $f = 0; $f < 8; $g = ord($a[$f]),
            $d .= $s[($g ^ ord($a[$f + 8])) - $g & 0x1F],
            $f++
        );
        return $d;
    }

    public static function getMicrotime()
    {
        list($usec, $sec) = explode(" ", microtime());
        return intval(($usec * 1000 + (float)$sec * 1000));
    }

    public static function formatTime($time)
    {
        $time = strtotime($time);
        $timeinterval = time() - $time;
        $timeinterval = floor($timeinterval / 60);
        if ($timeinterval < 1) {
            return "刚刚";
        } else {
            if ($timeinterval < 60) {
                return $timeinterval . "分钟前";
            } else {
                $timeinterval = floor($timeinterval / 60);
                if ($timeinterval < 24) {
                    return $timeinterval . "小时前";
                } else {
                    $hour = date("H");
                    $timeinterval = floor(($timeinterval - $hour) / 24) + 1;
                    if ($timeinterval < 2) {
                        return "昨天";
                    } elseif ($timeinterval < 3) {
                        return "前天";
                    } else {
                        return date("Y/m/d", $time);
                    }

                }
            }
        }
    }

    /*
    *生成随机密码
    */
    public static function createPwd() {
        $char_array = array('1','2','3','4','5','6','7','8','9','0','a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z');

        $mess_array = self::randArray($char_array);
        ksort($mess_array);

        return implode('', array_slice($mess_array, 0, 6));
    }

    //打乱数组
    public static function randArray($arr)
    {
        $arr_size=sizeof($arr);
        $tmp_arr=array();

        //开始乱序排列
        for($i=0;$i<$arr_size;$i++) {

            mt_srand((double) microtime()*1000000);

            $rd = mt_rand(0,$arr_size-1);

            if($tmp_arr[$rd]=="")
            {
                $tmp_arr[$rd]=$arr[$i];
            }
            else
            {
                $i=$i-1;
            }
        }

        return $tmp_arr;
    }

}