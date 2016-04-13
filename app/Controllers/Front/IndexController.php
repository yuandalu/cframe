<?php

namespace App\Controllers\Front;

use App\Controllers\Front\BaseController;
use App\Models\Svc\LoaderSvc;

class IndexController extends BaseController
{
    public function indexAction()
    {
        $cache = LoaderSvc::loadDBCache()->get('a');
        $this->assign('name', $cache);
    }
}