<?php

namespace App\Controllers\Admin;

use App\Controllers\Admin\BaseController;

class IndexController extends BaseController
{
    public function indexAction()
    {
        $this->assign('name', 'Cframe Ok');
    }
}