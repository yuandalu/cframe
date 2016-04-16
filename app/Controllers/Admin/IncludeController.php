<?php

namespace App\Controllers\Admin;

use App\Ext\Captcha;

class IncludeController extends BaseController
{
    public function __construct()
    {
        header("Content-type: text/html; charset=GB2312");
        parent::__construct();
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