<?php

namespace App\Controllers\Admin;

use App\Ext\Captcha;

class IncludeController extends BaseController
{
    const PER_PAGE_NUM = 15;// 默认分页数
    
    static $NOT_LOGIN_ACTION   = array('showimg', 'showhtmlimage');// 排除登录验证

    public function __construct()
    {
        $isLogin  = true;
        if (in_array(strtolower($this->getActionName()), self::$NOT_LOGIN_ACTION)) {
            $isLogin = false;
        }
        parent::__construct($isLogin);
    }

    public function showimgAction()
    {
         header('Cache-control: private');
         header('Last-Modified: ' . gmdate("D, d M Y H:i:s") . ' GMT');
         header('Cache-Control: no-store, no-cache, must-revalidate');
         header('Cache-Control: post-check=0, pre-check=0', false);
         header('Pragma: no-cache');
         $captcha = new Captcha(4,2);
         $captcha->show_image(80, 30);
    }

    public function showHtmlImageAction()
    {
         header('Cache-control: private');
         header('Last-Modified: ' . gmdate("D, d M Y H:i:s") . ' GMT');
         header('Cache-Control: no-store, no-cache, must-revalidate');
         header('Cache-Control: post-check=0, pre-check=0', false);
         header('Pragma: no-cache');
         $captcha = new Captcha(4,2);
         $captcha->showHtmlImage(80, 34);
         exit;
    }
}