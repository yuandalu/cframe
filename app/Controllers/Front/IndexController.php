<?php

namespace App\Controllers\Front;

use App\Controllers\Front\BaseController;

class IndexController extends BaseController
{
    public function indexAction()
    {
        $this->assign('name', 'Cframe Ok');
    }
}