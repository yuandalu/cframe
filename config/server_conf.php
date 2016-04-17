<?php

if (isset($_SERVER['REMOTE_ADDR']) && (in_array($_SERVER['REMOTE_ADDR'], array('127.0.0.1')) || substr($_SERVER['REMOTE_ADDR'], 0, 8) == '192.168.')) {
    error_reporting(E_ALL & ~E_NOTICE & ~ E_STRICT);
    $EXCEPTION = true;
} else {
    error_reporting(0);
    $EXCEPTION = false;
}

$READONLY_MODE = '0';
$CAPTCHA_FONT_FILE = BASE_DIR.'/resources/data/verdana.ttf';