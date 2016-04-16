<?php

if (isset($_SERVER['REMOTE_ADDR']) && (in_array($_SERVER['REMOTE_ADDR'], array('127.0.0.1','106.2.178.238', '103.254.112.150')) || substr($_SERVER['REMOTE_ADDR'],0,8)=='192.168.')) {
    error_reporting(E_ALL & ~E_NOTICE & ~ E_STRICT);
    $EXCEPTION = true;
} else {
    error_reporting(0);
    $EXCEPTION = false;
}

$CAPTCHA_FONT_FILE = BASE_DIR.'/resources/data/verdana.ttf';