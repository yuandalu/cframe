<?php

namespace App\Controllers\Admin;

use App\Models\Svc\ErrorSvc;

class ErrorController extends BaseController
{
    public function errorAction()
    {
        echo '<pre>';
        print_r($this->getParam('error_handle'));
        echo '</pre>';
        exit;
    }
}