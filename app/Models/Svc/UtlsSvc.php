<?php

namespace App\Models\Svc;

use App\Support\Loader;

class UtlsSvc
{

    public static function filter_xml_string($simplexml_object)
    {
        return mb_convert_encoding(trim(urldecode($simplexml_object)), 'gbk', 'utf-8');
    }

    public static function is_id($id)
    {
        return is_numeric($id) && strlen($id) >= 8;
    }

    public static function is_numerics($id)
    {
        if (is_numeric($id)) {
            return true;
        } else {
            return false;
        }
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

    

    public static function getTotalPage($total_cnt = '0', $row_num = '0')
    {
        if ('0' == $total_cnt || '0' == $row_num) {
            return '1';
        }
        return ceil($total_cnt / $row_num);
    }

    

    public static function getTime($unixtime = -1)
    {
        if ($unixtime == -1) {
            return date('Y-m-d H:i:s');
        } else {
            return date('Y-m-d H:i:s', $unixtime);
        }
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

    

    public static function get_ip_address($ip)
    {
        if ($ip == '127.0.0.1')
            return 'IP：' . $ip . ' 来自：本地';
        $content = self::simpleRequest("http://ip.taobao.com/service/getIpInfo.php?ip=" . $ip);
        $ipdata = json_decode($content, true);
        $ipaddress = " " . $ipdata['data']['country'] . "-" . $ipdata['data']['area'] . "-" . $ipdata['data']['region'] . "-" . $ipdata['data']['city'] . "-" . $ipdata['data']['county'] . $ipdata['data']['isp'] . "";
        return $ipaddress;
    }

    /*
     * 返回地址数组
     */

    public static function get_ip_address2($ip)
    {
        if ($ip == '127.0.0.1' || empty($ip)) {
            return array();
        }

        $content = self::simpleRequest("http://ip.taobao.com/service/getIpInfo.php?ip=" . $ip);
        $ipdata = json_decode($content, true);

        if ($ipdata['data']) {
            $address['country'] = $ipdata['data']['country'];
            $address['area'] = $ipdata['data']['area'];
            $address['region'] = $ipdata['data']['region'];
            $address['city'] = $ipdata['data']['city'];
            $address['county'] = $ipdata['data']['county'];
            $address['isp'] = $ipdata['data']['isp'];
        } else {
            $address = array();
        }
        return $address;
    }

    public static function get_ip_province($ip)
    {
        if ($ip == '127.0.0.1')
            return 'IP：' . $ip . ' 来自：本地';
        $content = self::simpleRequest("http://ip.taobao.com/service/getIpInfo.php?ip=" . $ip);
        $ipdata = json_decode($content, true);
        $ipaddress = $ipdata['data']['region'];
        return $ipaddress;
    }

    public static function getRemoteIP()
    {
        return $_SERVER['REMOTE_ADDR'];
    }

    

    public static function checkSourceIP($mid)
    {
        $merchant_allow_ip = QFrameConfig::getConfig('MERCHANT_ALLOW_IP');
        if (!isset($merchant_allow_ip[$mid])) {
            return false;
        }
        //self::getClientIP()里，HTTP_X_FORWARDED太容易被伪造了
        return in_array($_SERVER['REMOTE_ADDR'], $merchant_allow_ip[$mid]);
    }

    

    public static function checkNotifyIP($gateway)
    {
        $gateway_allow_ip = QFrameConfig::getConfig('GATEWAY_ALLOW_IP');
        if (!isset($gateway_allow_ip[$gateway])) {

            return false;
        }
        $ip = self::getRemoteIP();
        if (in_array($ip, $gateway_allow_ip[$gateway])) {
            return true;
        }
        foreach ($gateway_allow_ip[$gateway] as $ip_C) {
            if (strpos($ip, $ip_C) === 0) {
                return true;
            }
        }
        return false;
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

    

    public static function needLogin($ajax = false)
    {
        $info = UserSdk::getUserInfo();
        if (!$info) {
            if ($ajax) {
                return '';
                exit;
            } else {
                UtlsSvc::goToAct('login', 'index', array('go' => 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']));
            }
            return false;
        }
        return true;
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

    

    public static function json_encode($var)
    {
        switch (gettype($var)) {
            case 'boolean':
                return $var ? 'true' : 'false'; // Lowercase necessary!
            case 'integer':
            case 'double':
                return $var;
            case 'resource':
            case 'string':
                return '"' . str_replace(array("\r", "\n", "<", ">", "&"), array('\r', '\n', '\x3c', '\x3e', '\x26'), addslashes($var)) . '"';
            case 'array':
                // Arrays in JSON can't be associative. If the array is empty or if it
                // has sequential whole number keys starting with 0, it's not associative
                // so we can go ahead and convert it as an array.
                if (empty($var) || array_keys($var) === range(0, sizeof($var) - 1)) {
                    $output = array();
                    foreach ($var as $v) {
                        $output[] = self::json_encode($v);
                    }
                    return '[' . implode(', ', $output) . ']';
                }
            // Otherwise, fall through to convert the array as an object.
            case 'object':
                $output = array();
                foreach ($var as $k => $v) {
                    $output[] = '"' . strval($k) . '":' . self::json_encode($v);
                }
                return '{' . implode(',', $output) . '}';
            default:
                return 'null';
        }
    }

    public static function staffAuth($uid, $pwd)
    {

        $ds = ldap_connect($_SERVER['ENV_LDAP_SERVER'], 389);
        if ($ds) {
            if (@ldap_bind($ds, $uid, $pwd)) {
                $r = 1;
            } else {
                $r = 0;
            }
            ldap_close($ds);
        } else {
            $r = 0;
        }
        return $r;
    }

    public static function uploadDocFile($file, $param, $k = "")
    {
        if (empty($param['size'])) {
            return 'param size is not exist';
        }
        if (empty($param['path'])) {
            return 'param path is not exist';
        }
        if (empty($file)) {
            return 'param file is not exist';
        }
        if ($k) {
            if (is_uploaded_file($_FILES[$file]['tmp_name'][$k])) {
                $result = array();
                if ($_FILES[$file]['size'][$k] > $param['size']) {
                    return 'size to large';
                }
                if (!file_exists($_SERVER['ENV_DATA_DIR'] . '/' . $param['path'])) {
                    self::makeDir($_SERVER['ENV_DATA_DIR'] . '/' . $param['path']);
                }
                $name = strstr($_FILES[$file]['name'][$k], $_FILES[$file]['name'][$k]);
//                $pathinfo = pathinfo($name);
//                $newName = md5(uniqid() . $param['size'] . $_SERVER['REMOTE_ADDR']) . "." . $pathinfo['extension'];
                $targetFile = $_SERVER['ENV_DATA_DIR'] . '/' . $param['path'] . '/' . $name;
                move_uploaded_file($_FILES[$file]['tmp_name'][$k], $targetFile);
                $result['name'] = $param['path'] . '/' . $name;
                $result['size'] = $_FILES[$file]['size'][$k];


                return $result;
            } else {
                return " file is not exist";
            }
        } else {
            if (is_uploaded_file($_FILES[$file]['tmp_name'])) {
                $result = array();
                if ($_FILES[$file]['size'] > $param['size']) {
                    return 'size to large';
                }
                if (!file_exists($_SERVER['ENV_DATA_DIR'] . '/' . $param['path'])) {
                    self::makeDir($_SERVER['ENV_DATA_DIR'] . '/' . $param['path']);
                }
                $name = strstr($_FILES[$file]['name'], $_FILES[$file]['name']);
//                $pathinfo = pathinfo($name);
//                $newName = md5(uniqid() . $param['size'] . $_SERVER['REMOTE_ADDR']) . "." . $pathinfo['extension'];
//                $newName = date("dhis")."_"  . $_FILES[$file]['name'];
                $targetFile = $_SERVER['ENV_DATA_DIR'] . '/' . $param['path'] . '/' . $name;
                move_uploaded_file($_FILES[$file]['tmp_name'], $targetFile);
                $result['name'] = $param['path'] . '/' . $name;
                $result['size'] = $_FILES[$file]['size'];
                //print_r($result);exit;

                return $result;
            } else {
                return " file is not exist";
            }
        }
    }

    public static function showMsg($alert, $url, $time = 1.2)
    {
        $time = $time * 1000;
        echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><script src="/static/js/jquery.js"></script><script src="/static/js/msg_util.js"></script><link rel="stylesheet" href="/static/admin/css/public.css" /></head><body>';
        echo "<script>MsgUtil.show('" . addslashes($alert) . "',function(){window.location.href='" . $url . "';},$time);</script>";
        echo "</body></html>";
        exit;
    }

    

    public static function simpleShowMsg($alert, $url)
    {
        $time = $time * 1000;
        echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><body>';
        echo "<script>alert('" . addslashes($alert) . "');window.location.href='" . $url . "';</script>";
        echo "</body></html>";
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

    public static function showstar($average_star)
    {
        $start = '1.5';
        $b1 = "<img src='/static/images1/a1.gif' />";
        $b2 = "<img src='/static/images1/a2.gif' />";
        if ($average_star < $start) {
            $start = $b1 . $b2 . $b2 . $b2 . $b2;
        } elseif ($start <= $average_star && $average_star < ($start + 1)) {
            $start = $b1 . $b1 . $b2 . $b2 . $b2;
        } elseif (($start + 1) <= $average_star && $average_star < ($start + 2)) {
            $start = $b1 . $b1 . $b1 . $b2 . $b2;
        } elseif (($start + 2) <= $average_star && $average_star < ($start + 3)) {
            $start = $b1 . $b1 . $b1 . $b1 . $b2;
        } elseif (($start + 3) <= $average_star && $average_star < ($start + 4)) {
            $start = $b1 . $b1 . $b1 . $b1 . $b1;
        } else {
            $start = $b1 . $b1 . $b1 . $b1 . $b1;
        }
        return $start;
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

    public static function getWebBaseUrl()
    {
        return 'http://' . str_replace('admin.', '', $_SERVER['SERVER_NAME']);
    }

    /*
     * 获得parse_str的指定变量
     */

    public static function get_parse_str($str, $keys)
    {
        parse_str($str);
        $result = array();
        foreach ($keys as $k => $v) {
            $result[$v] = $$v;
        }

        return $result;
    }

    /*
     * 分页函数
     */

    public static function cutpage($number, $page, $pnum, $pname, $file, $params = array(), $maxp = 0, $rewrite = false, $xianshi = false)
    {

        if (!$maxpage = ceil($number / abs($pnum)))
            $maxpage = 1;
        if ($maxp)
            $maxpage = min($maxp, $maxpage);
        if (!is_numeric($page) || $page < 1 || $page > $maxpage)
            return false;
        if ($maxpage < 2)
            return '';
        $cutpage = "<p class='paging mar_t10'>";
        if ($xianshi == false) {
            $cutpage .= "<a>总记录：" . $number . "条</a> <a>当前页" . $page . "总页数" . $maxpage . "</a>";
        }
        if ($page != 1)
            $cutpage .= ' <a href="' . $file . '" target="_self">首页</a>';
        if ($page > 1)
            $cutpage .= ' <a href="' . self::urlme($file, array_merge($params, array($pname => $page - 1)), $rewrite) . '" target="_self">上一页</a>';
        $lnum = $page > 3 ? 2 : $page - 1;
        $maxsp = $page + 10 - $lnum > $maxpage ? $maxpage + 1 : $page + 10 - $lnum;
        $minsp = $maxsp - 10 < 1 ? 1 : $maxsp - 10;
        for ($i = $minsp; $i < $maxsp; $i++) {
            $cutpage .= $page == $i ? ' <a class=on>' . $i . '</a>' : ' <a href="' . self::urlme($file, array_merge($params, array($pname => $i)), $rewrite) . '" target="_self">' . $i . '</a>';
        }
        if ($page < $maxpage)
            $cutpage .= ' <a href="' . self::urlme($file, array_merge($params, array($pname => $page + 1)), $rewrite) . '" target="_self">下一页</a>';
        if ($page != $maxpage)
            $cutpage .= ' <a href="' . self::urlme($file, array_merge($params, array($pname => $maxpage)), $rewrite) . '" target="_self">尾页</a>';
        $cutpage = $cutpage . '</p>';

        return $cutpage;
    }

    private function urlme($path = null, $params = null, $rewrite = false)
    {
        $url = '/';
        if (is_string($path) || is_numeric($path))
            $path = explode('/', $path);
        if (is_array($path)) {
            foreach ($path as $v)
                if ($v && (is_string($v) || is_numeric($v)))
                    $ptmp[] = rawurlencode($v);
            if ($ptmp)
                $url .= implode('/', $ptmp);
        }
        if (!preg_match('/.*\.[a-z]{2,4}$/', $url))
            $url .= '/';
        if (is_array($params)) {
            $url .= '?';
            foreach ($params as $k => $v) {
                if (is_string($v) || is_numeric($v))
                    $url .= rawurlencode($k) . '=' . rawurlencode($v) . '&';
            }
        }
        return $rewrite ? rtrim(str_replace(array('=', '&', '?', '//'), '/', $url)) . '.html' : rtrim($url, '&?');
    }

    public static function checkUrl($strurl)
    {
        $str = substr($strurl, 0, 7);
        if (stripos($strurl, '?') !== false) {
            $ref = "&ref=221";
        } else {
            $ref = "?ref=221";
        }
        if ($str !== 'http://') {
            return "http://" . $strurl . $ref;
        }
        return $strurl . $ref;
    }

    public static function StrLenW($str)
    {
        $count = 0;
        $len = strlen($str);
        for ($i = 0; $i < $len; $i++, $count++)
            if (ord($str[$i]) >= 128)
                $i++;
        return $count;
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

    /*
    *获取ios的设备id
    */
    public static function getIosDeviceid()
    {

        $deviceid = "";
        $version = self::getAppVersion();

        if (self::isApp() && self::isIOS() && (  ($version['app'] == 'GUIXUE' && $version['version'] > 1.4 )  || ($version['app'] == 'GUIXUETOEFL' && $version['version'] > 0) ) ) {
            LogSvc::get("deviceid")->log(print_r($version, true));


            $useragent = $_SERVER['HTTP_USER_AGENT'];

            preg_match("/DeviceId:(.*?)\)/i", $useragent, $match);
            LogSvc::get("deviceid")->log(print_r($match, true));

            $deviceid = $match[1];
        }

        return $deviceid;

    }

    /*
     * 判断 ios6 及以下版本
     */

    public static function isIOS5()
    {

        if (self::isIOS() && strpos($_SERVER['HTTP_USER_AGENT'], 'OS 5_') || strpos($_SERVER['HTTP_USER_AGENT'], 'OS 4_') || strpos($_SERVER['HTTP_USER_AGENT'], 'OS 6_')) {
            //网页访问会带上版本信息
            return true;
        } else {
            //应用内访问只能通过cookie传递版本信息
            $appverid = $_COOKIE['appverid'];
            $appverobj = AppverSvc::getById($appverid);
            $sysver = intval(100 * $appverobj->sysver);

            if ($sysver > 0 && $sysver < 700) {
                return true;
            }
        }

        return false;
    }

    /*
    * 是否越狱
    */
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
        if (in_array($ip, array('127.0.0.1', '58.132.172.36', '182.92.104.65', '182.92.103.136','106.2.178.238', '103.254.112.150', '123.57.39.20'))) {
            return true;
        }
        return false;
    }

    public static function hexdec_big($hex)
    {
        $ret = 0;
        $c = 0;
        while (strlen($hex) > 0) {
            $h = substr($hex, -1);
            $d = base_convert($h, 16, 10);
            $ret = bcadd($ret, bcmul(bcpow(16, $c, 0), $d, 0), 0);
            $hex = substr($hex, 0, -1);
            $c++;
        }
        return $ret;
    }

    /*
     * 只读模式
     */

    public static function isReadonly()
    {
        return $_SERVER['READONLY_MODE'] == "1";
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

    /*
     * 根据手机号获取手机归属地等信息
     */

    public static function getMobileInfo($mobile)
    {
        $page = self::simpleRequest('http://opendata.baidu.com/api.php?query=' . $mobile . '&co=&resource_id=6004&t=' . time() . '&ie=utf8&oe=gbk&cb=bd__cbs__854nlx&format=json&tn=baidu');

        $page = iconv('gbk', 'utf-8', substr(str_replace('bd__cbs__854nlx(', '', $page), 0, -2));

        $json = json_decode($page);
        $city = $json->data[0]->city; //城市
        $operators = $json->data[0]->type; //归属地
        $prov = $json->data[0]->prov; //省份
        return array('city' => $city, 'operators' => $operators, 'prov' => $prov);
    }

    //QQ和彩贝联合登录自动注册的系统邮箱地址
    public static function autoregEmail($username)
    {
        $uid = intval(str_replace('#', '', $username));
        if ($uid > 0) {
            return $username . '@guixue.net';
        } else {
            $userinfo = UserSdk::getInfoByUsername($username);
            $uid = $userinfo['uid'];
            return '#' . $uid . '@guixue.net';
        }
    }

    //是否自动注册用户
    public static function isAutoRegUser($uid, $username)
    {
        $uid = intval($uid);
        if (strpos($username, '#') === 0 || $username == '#' . $uid) {
            return true;
        } else {
            return false;
        }
    }

    //获取用户推广链接
    public static function getUserRef($uid)
    {
        if (intval($uid)) {
            return 'u-' . $uid;
        }
        return 0;
    }

    //根据用户推广链接获取uid
    public static function getUidByRef($ref)
    {
        if (strpos($ref, 'u-') !== false) {
            return intval(str_replace('u-', '', $ref));
        }
        return 0;
    }

    /**
     * 二维数组排序
     *
     * @param $arr :数据
     * @param $keys :排序的健值
     * @param $type :升序/降序
     *
     * @return array
     */
    public static function arraySort($arr, $keys, $type = "asc")
    {
        if (!is_array($arr)) {
            return false;
        }
        $keysvalue = array();
        foreach ($arr as $key => $val) {
            $keysvalue[$key] = $val[$keys];
        }
        if ($type == "asc") {
            asort($keysvalue);
        } else {
            arsort($keysvalue);
        }
        reset($keysvalue);
        foreach ($keysvalue as $key => $vals) {
            $keysort[$key] = $key;
        }
        $new_array = array();
        foreach ($keysort as $key => $val) {
            $new_array[$key] = $arr[$val];
        }
        return $new_array;
    }

//end function

    public static function arraySort2($arr, $keys, $type = "asc")
    {

        if (!is_array($arr) || empty($arr)) {
            return array();
        }
        $keysvalue = array();
        foreach ($arr as $key => $val) {
            $keysvalue[$key] = $val[$keys];
        }
        if ($type == "asc") {
            asort($keysvalue);
        } else {
            arsort($keysvalue);
        }
        reset($keysvalue);
        foreach ($keysvalue as $key => $vals) {
            $keysort[$key] = $key;
        }
        $new_array = array();
        foreach ($keysort as $key => $val) {
            $new_array[] = $arr[$val];
        }
        return $new_array;
    }

//end function

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

    /**
     *
     * cms 区块输出为json格式的转换为二维数组(仅考虑多行1列的情况)
     * @param int $sectionid
     */
    public static function json2arrayBySectionId($sectionid)
    {
        $url = 'http://www.guixue.com/section/' . intval($sectionid) . '.json';
        $data = self::remote_get_contents($url);

        $output = array();
        if ($data) {
            $data = json_decode($data, true);
            foreach ($data as $k => $r) {
                foreach ($r as $i => $c) {
                    if (strpos($c['thumb'], 'http://') === false) {
                        $c['thumb'] = 'http://i1.umivi.net/' . $c['thumb'];
                    }

                    $output[] = $c;
                }
            }
        }
        return $output;
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

    /**
     * 获取某个时间段的日期数组
     */
    public static function intervalDate($begDate, $endDate)
    {
        $date = array();
        $begTime = strtotime($begDate);
        $endTime = strtotime($endDate);
        while ($begTime - 86400 < $endTime) {
            $date[] = date("Y-m-d", $begTime);
            $begTime += 86400;
        }
        return $date;
    }

    public static function uploadFile($file, $param, $k = "")
    {
        if (empty($param['size'])) {
            return 'param size is not exist';
        }
        if (empty($param['image_type'])) {
            return 'param image_type is not exist';
        }
        if (empty($param['path'])) {
            return 'param path is not exist';
        }
        if (empty($file)) {
            return 'param file is not exist';
        }
        if ($k) {
            if (is_uploaded_file($_FILES[$file]['tmp_name'][$k])) {
                $result = array();
                if ($_FILES[$file]['size'][$k] > $param['size']) {
                    return 'size to large';
                }
                if (!in_array($_FILES[$file]['type'][$k], $param['image_type'])) {
                    return 'image_type is wrong ';
                }
                if (!file_exists($_SERVER['ENV_DATA_DIR'] . '/' . $param['path'])) {
                    self::makeDir($_SERVER['ENV_DATA_DIR'] . '/' . $param['path']);
                }
                $name = strstr($_FILES[$file]['name'][$k], $_FILES[$file]['name'][$k]);
                $pathinfo = pathinfo($name);
                $newName = md5(uniqid() . $param['size'] . $_SERVER['REMOTE_ADDR']) . "." . $pathinfo['extension'];

                $targetFile = $_SERVER['ENV_DATA_DIR'] . '/' . $param['path'] . '/' . $newName;
                move_uploaded_file($_FILES[$file]['tmp_name'][$k], $targetFile);
                $result['image'] = $param['path'] . '/' . $newName;
                $result['size'] = $_FILES[$file]['size'][$k];


                return $result;
            } else {
                return " file is not exist";
            }
        } else {
            if (is_uploaded_file($_FILES[$file]['tmp_name'])) {
                $result = array();
                if ($_FILES[$file]['size'] > $param['size']) {
                    return 'size to large';
                }
                if (!in_array($_FILES[$file]['type'], $param['image_type'])) {
                    return 'image_type is wrong ';
                }
                if (!file_exists($_SERVER['ENV_DATA_DIR'] . '/' . $param['path'])) {
                    self::makeDir($_SERVER['ENV_DATA_DIR'] . '/' . $param['path']);
                }
                $name = strstr($_FILES[$file]['name'], $_FILES[$file]['name']);
                $pathinfo = pathinfo($name);
                $newName = md5(uniqid() . $param['size'] . $_SERVER['REMOTE_ADDR']) . "." . $pathinfo['extension'];

                $targetFile = $_SERVER['ENV_DATA_DIR'] . '/' . $param['path'] . '/' . $newName;
                move_uploaded_file($_FILES[$file]['tmp_name'], $targetFile);
                $result['image'] = $param['path'] . '/' . $newName;
                $result['size'] = $_FILES[$file]['size'];
                //print_r($result);exit;

                return $result;
            } else {
                return " file is not exist";
            }
        }
    }

    public static function uploadCorpusFile($file, $param, $k = "", $id)
    {
        if (empty($param['size'])) {
            return 'param size is not exist';
        }
        if (empty($param['type'])) {
            return 'param type is not exist';
        }
        if (empty($param['path'])) {
            return 'param path is not exist';
        }
        if (empty($file)) {
            return 'param file is not exist';
        }
        if ($k) {
            if (is_uploaded_file($_FILES[$file]['tmp_name'][$k])) {
                $result = array();
                if ($_FILES[$file]['size'][$k] > $param['size']) {
                    return 'size to large';
                }
                if (!in_array($_FILES[$file]['type'][$k], $param['type'])) {
                    return 'image_type is wrong ';
                }
                if (!file_exists($_SERVER['ENV_DATA_DIR'] . '/' . $param['path'])) {
                    self::makeDir($_SERVER['ENV_DATA_DIR'] . '/' . $param['path']);
                }
                $name = strstr($_FILES[$file]['name'][$k], $_FILES[$file]['name'][$k]);
                $pathinfo = pathinfo($name);
                $newName = $id[$k] . "." . $pathinfo['extension'];

                $targetFile = $_SERVER['ENV_DATA_DIR'] . '/' . $param['path'] . '/' . $newName;
                move_uploaded_file($_FILES[$file]['tmp_name'][$k], $targetFile);
                $result['sound'] = $param['path'] . '/' . $newName;
                $result['size'] = $_FILES[$file]['size'][$k];


                return $result;
            } else {
                return " file is not exist";
            }
        } else {
            if (is_uploaded_file($_FILES[$file]['tmp_name'])) {
                $result = array();
                if ($_FILES[$file]['size'] > $param['size']) {
                    return 'size to large';
                }
                if (!in_array($_FILES[$file]['type'], $param['type'])) {
                    return 'type is wrong ';
                }
                if (!file_exists($_SERVER['ENV_DATA_DIR'] . '/' . $param['path'])) {
                   echo  self::makeDir($_SERVER['ENV_DATA_DIR'] . '/' . $param['path']);
                }
                $name = strstr($_FILES[$file]['name'], $_FILES[$file]['name']);
                $pathinfo = pathinfo($name);
                $newName = $id . "." . $pathinfo['extension'];
                $targetFile = $_SERVER['ENV_DATA_DIR'] . '/' . $param['path']  . '/' . $newName;
                move_uploaded_file($_FILES[$file]['tmp_name'], $targetFile);
                $result['sound'] = str_replace('corpus','',$param['path']) . '/' . $newName;
                $result['size'] = $_FILES[$file]['size'];
                //print_r($result);exit;

                return $result;
            } else {
                return " file is not exist";
            }
        }
    }


    public static function plat()
    {
        $plat = 'web';
        if (UtlsSvc::isIpad()) {
            $plat = 'ipad';
        }
        if (UtlsSvc::isIphone() || UtlsSvc::isIpod()) {
            $plat = 'iphone';
        }
        if (UtlsSvc::isAndroid()) {
            $plat = 'android';
        }
        return $plat;
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
        )
            ;
        return $d;
    }

    public static function qrcode($content, $width = 200)
    {
        return UtlsSvc::remote_get_contents("http://qr.liantu.com/api.php?&bg=ffffff&fg=000000&w=" . $width . "&m=0&text=" . urlencode($content));
    }

    public static function rootDomain()
    {
        $domain = $_SERVER['SERVER_NAME'];
        return 'guixue.com';
    }

    public static function getMicrotime()
    {
        list($usec, $sec) = explode(" ", microtime());
        return intval(($usec * 1000 + (float)$sec * 1000));
    }

    public static function getMid()
    {

        $mid = (int)$_REQUEST['mid'];


        if (in_array($mid, array(Merchant::MERCHANT_DEFAULT, Merchant::MERCHANT_VOD_ANDROID, Merchant::MERCHANT_VOD_IOS))) {
            return $mid;
        }


        if (self::isApp() && self::isAndroid()) {
            $mid = Merchant::MERCHANT_VOD_ANDROID;

        } else if (self::isApp() && self::isIOS()) {
            $mid = Merchant::MERCHANT_VOD_IOS;
        } else if (self::isMobile('excludepad')) {
            $mid = Merchant::MERCHANT_VOD_WAP;
        } else {

            $mid = Merchant::MERCHANT_DEFAULT;
        }

        return $mid;

    }


    public static function getAppVersion()
    {

        $useragent = $_SERVER['HTTP_USER_AGENT'];
        $result = array();

        

        preg_match("/GUIXUETOEFL:(\d+\.\d+)/", $useragent, $match);

        if (isset($match[1])) {
            $result['version'] = $match[1];
            $result['app'] = "GUIXUETOEFL";
            return $result;
        }


        preg_match("/GUIXUECET:(\d+\.\d+)/", $useragent, $match);

        if (isset($match[1])) {
            $result['version'] = $match[1];
            $result['app'] = "GUIXUECET";
            return $result;
        }

        preg_match("/GUIXUE:(\d+\.\d+)/", $useragent, $match);
        if (isset($match[1])) {
            $result['version'] = $match[1];
            $result['app'] = "GUIXUE";
            return $result;
        }

        return $result;

        
    }

    public static function WaitingForReview($app = "GUIXUE")
    {
        $version = UtlsSvc::getAppVersion();
        
        if(self::isToeflApp())
        {
            if (UtlsSvc::isIOS() && $version['version'] == 1.1) {
                return true;
            }
        }

        if(self::isCetApp())
        {
            if (UtlsSvc::isIOS() && $version['version'] == 1.0) {
                return true;
            }
        }
        

        if(self::isIeltsApp())
        {
            if (UtlsSvc::isIOS() && $version['version'] == 2.65) {
                return true;
            }
        }
        
        return false;
    }

    /*
    *按照功能模块 区分
    */
    public static function waitingForReviewBySub($sub, $app = "GUIXUE")
    {   


        if(self::isToeflApp())
        {
            $version_android = self::getToeflAndroidAppVersion();
            $version_ios = self::getToeflIosAppVersion();

            if($sub == 'iosdeposit' && ($version_ios >=1.0 )  ) {
                return true;
            } 

            if($sub == 'useiap' && ($version_ios <=0.9 ) && !self::inApple() && !self::isAbroad() ) {
                //如果为true 则不使用iap 适用支付宝等sdk
                return true;
            }

            if($sub == 'couponcode' && $version_ios<1.1) {
                //显示优惠码
                return true;
            }
        } else if(self::isCetApp()){
            $version_android = self::getCetAndroidAppVersion();
            $version_ios = self::getCetIosAppVersion();
             
            if($sub == 'useiap' && ($version_ios <1.0 ) && !self::inApple() && !self::isAbroad() ) {
                //如果为true 则不使用iap 适用支付宝等sdk
                //return false;
                return true;

            }

        } else  {
            $version_android = self::getGuixueAndroidAppVersion();
            $version_ios = self::getGuixueIosAppVersion();
            if($sub == 'synonyms' &&  ($version_android == 2.2  || $version_ios ==2.2 ) )
            {
                return true;
            }

            if($sub == 'memberielts' && ($version_android>=2.60 || $version_ios>=2.60) ) {
                return true;
            }   
             if($sub == 'membercet' && ($version_android >= 2.5  || $version_ios >=2.5 )   ) {
                return true;
            }

            if($sub == 'changemobile' && ($version_android >= 2.61  || $version_ios >=2.61 )  ) {
                return true;
            }   

            if($sub == 'iosdeposit' && ($version_ios >=2.61 )  ) {
                return true;
            }   

            if($sub == 'useiap' && ($version_ios <=2.64 ) && !self::inApple() && !self::isAbroad() ) {
                //如果为true 则不使用iap 适用支付宝等sdk
                //return false;
                return true;

            }

            if($sub  == 'ieltsaddon'  && ($version_android > 2.61  || $version_ios >2.61 )  ) {
                //雅思终身会员 2.62的时候跳转到终身会员页
                return true;
            }

            if($sub == 'version263' && ($version_android >=2.63  || $version_ios >=2.63 ) ) {
                //针对版本2.63 做的设置
                return true;
            }

             if($sub == 'couponcode' && $version_ios<=2.63) {
                //显示优惠码
                return true;
            }
        }
        

        

        return false;
    }

    public static function isApp()
    {
        $appversion = self::getAppVersion();
        return !empty($appversion);
    }

    public static function isGuixeApp()
    {
        $appversion = self::getAppVersion();
        return $appversion['app'] == 'GUIXUE';
    }

    public static function isIeltsApp()
    {
        $appversion = self::getAppVersion();
        return $appversion['app'] == 'GUIXUE';
    }

    public static function isPengciApp()
    {
        $appversion = self::getAppVersion();
        return $appversion['app'] == "PENGCI";
    }

    public static function isToeflApp()
    {
        $appversion = self::getAppVersion();
        return $appversion['app'] == "GUIXUETOEFL";
    }
    public static function isCetApp()
    {
        $appversion = self::getAppVersion();
        return $appversion['app'] == "GUIXUECET";
    }

    public static function getGuixueVersion()
    {
        $appversion = self::getAppVersion();
        return ($appversion['app'] == 'GUIXUE' ? $appversion['version'] : 0);
    }


    public static function getGuixueIosAppVersion()
    {
        $appversion = self::getAppVersion();
        return (($appversion['app'] == 'GUIXUE' && UtlsSvc::isIOS()) ? $appversion['version'] : 0);
    }

    public static function getGuixueAndroidAppVersion()
    {
        $appversion = self::getAppVersion();

        return (($appversion['app'] == 'GUIXUE' && UtlsSvc::isAndroid()) ? $appversion['version'] : 0);

    }

    public static function getToeflIosAppVersion()
    {
        $appversion = self::getAppVersion();
        return (($appversion['app'] == 'GUIXUETOEFL' && UtlsSvc::isIOS()) ? $appversion['version'] : 0);
    }

    public static function getToeflAndroidAppVersion()
    {
        $appversion = self::getAppVersion();

        return (($appversion['app'] == 'GUIXUETOEFL' && UtlsSvc::isAndroid()) ? $appversion['version'] : 0);

    }

    public static function getCetIosAppVersion()
    {
        $appversion = self::getAppVersion();
        return (($appversion['app'] == 'GUIXUECET' && UtlsSvc::isIOS()) ? $appversion['version'] : 0);
    }

    public static function getCetAndroidAppVersion()
    {
        $appversion = self::getAppVersion();

        return (($appversion['app'] == 'GUIXUECET' && UtlsSvc::isAndroid()) ? $appversion['version'] : 0);

    }

    public static function getPengciVersion()
    {
        $appversion = self::getAppVersion();
        return ($appversion['app'] == 'PENGCI' ? $appversion['version'] : 0);
    }

    public static function getGeTuiField()
    {
        if (self::isIeltsApp()) {
            return 'getuiid';
        } elseif (self::isToeflApp()) {
            return 'toeflid';
        } elseif (self::isCetApp()) {
            return 'cetid';
        } else {
            return 'getuiid';
        }
    }

    public static function getGeTuiFieldByType($productType = '0')
    {
        if ($productType >= '0' && $productType <= '1999') {
            return 'getuiid';
        } elseif ($productType >= '2000' && $productType <= '2999') {
            return 'toeflid';
        } elseif ($productType >= '3000' && $productType <= '3999') {
            return 'cetid';
        } else {
            return 'getuiid';
        }
    }

    public static function createAudioRecorderLicence()
    {

        $str = strtolower($_SERVER['HTTP_HOST']) . '?localhost';
        $i = 0;
        $str1 = '';
        for ($i = 0; $i < strlen($str); $i++) {

            $str1 .= dechex(ord($str[$i]));
        }
        $str1_len = strlen($str1);
        $a = substr($str1, 0, intval($str1_len / 2));
        $b = substr($str1, intval($str1_len / 2));

        $str2 = $a . 'aurc8be' . $b . 'aurc0c2c4baceba';

        $i = 0;
        $vali = 0;
        while ($i < strlen($str2)) {
            $curr = intval($str2[$i]);
            if ($curr > 0) {
                $vali = $vali + $curr;
            }

            $i++;
        }


        $vali = dechex($vali);
        return $str2 . $vali;
    }

    public static function validateAudioRecorderLicence($str)
    {
        $i = 0;
        $pos2 = strpos($str, "aur", 0);
        $str3 = substr($str, 0, $pos2);
        $str4 = substr($str, ($pos2 + 3), 4);
        $pos5 = strpos($str, "aur", $pos2 + 3);
        $str6 = substr($str, $pos2 + 7, $pos5 - $pos2 - 7);
        $str7 = substr($str, $pos5 + 3, 4);
        $str8 = substr($str, $pos5 + 7, 8);
        $str9 = substr($str, $pos5 + 15);
        $str10 = $str3 . $str6;

        $str11 = "";
        $pos12 = 0;

        $str15 = "";

        $i = 0;
        while ($i < $pos5 + 15) {
            $curr = intval($str[$i]);
            if ($curr > 0) {
                $pos12 = $pos12 + $curr;
            }

            $i++;
        }


        $pos12 = dechex($pos12);

        if ($str9 != $pos12) {
            exit;
        }

        $i = 0;


        while ($i < strlen($str10)) {
            $str15 = chr(hexdec(substr($str10, $i, 2)));
            $str11 = $str11 . $str15;
            $i++;
            $i++;

        }

        return $str11 == (strtolower($_SERVER['HTTP_HOST']) . '?localhost');

    }


    public static function responseJSON($result)
    {

        $result = json_encode($result);

        $expires = 1500;
        $etag = md5($result);


        if (isset($_SERVER['HTTP_IF_NONE_MATCH']) && $_SERVER['HTTP_IF_NONE_MATCH'] == $etag) {
            header("HTTP/1.1 304 Not Modified");
            exit;
        }

        header("Etag: " . $etag);
        header("Pragma: public");
        header("Cache-Control: max-age=" . $expires);
        header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $expires) . ' GMT');

        echo $result;
        return;
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

    //判断是否是国外
    public static function isAbroad($ip='') {
        if($ip == '') {
           $ip = self::getClientIP();
        }

        if(!$ip) {
            return false;
        }


        require_once(__dir__.'/../integration/GeoIP2/geoip2.php');
        $isoCode = getGeoIP2Contry($ip);
        
        return (strtolower($isoCode) != 'cn' && $isoCode !='');
    }

}