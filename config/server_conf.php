<?php

if (isset($_SERVER['REMOTE_ADDR']) && (in_array($_SERVER['REMOTE_ADDR'], array('127.0.0.1')) || substr($_SERVER['REMOTE_ADDR'], 0, 8) == '192.168.')) {
    // error_reporting(E_ALL & ~E_NOTICE & ~ E_STRICT);
    error_reporting(E_ALL | E_STRICT);
    $EXCEPTION = true;
} else {
    error_reporting(0);
    $EXCEPTION = false;
}

$READONLY_MODE     = '0';
$DOMAIN_NAME       = 'yuandalu.com';

// 后台配置
$ADMIN_VERIFY_NUM    = '5';
$ADMIN_EMAIL_POSTFIX = '@guixue.com';
$ADMIN_POP_ADDRESS   = 'pop.ym.163.com';
$ADMIN_POP_PORT      = '110';
$CAPTCHA_FONT_FILE   = BASE_DIR.'/resources/data/verdana.ttf';