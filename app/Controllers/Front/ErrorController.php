<?php

namespace App\Controllers\Front;

use App\Models\Svc\ErrorSvc;

class ErrorController extends BaseController
{
    // 需要排除验证登录的action名
    static $NOT_LOGIN_ACTION  = array('show');
    // 默认分页数
    const PER_PAGE_NUM = 15;

    public function __construct()
    {
        $isLogin  = true;
        if (in_array(strtolower($this->getActionName()), self::$NOT_LOGIN_ACTION)) {
            $isLogin = false;
        }
        parent::__construct($isLogin);
    }


    public function showAction()
    {
        $e = $this->getRequest('e', '');
        $t = $this->getRequest('t', '');
        $s = $this->getRequest('s', '');

        if (!$this->checkShowParam($e, $t, $s)) {
            exit;
        }
        $this->assign('err_no', $e);
        $this->assign('err_msg', ErrorSvc::getMsg($e));
        return render('show');
    }

    private function checkShowParam($e, $t, $s)
    {
        if ('' == $e || '' == $t || '' == $s) {
            return false;
        }

        if (!ErrorSvc::checkShowSign($e, $t, $s)) {
            return false;
        }

        if (ErrorSvc::SHOW_TIMEOUT < (time() - $t)) {
            return false;
        }

        return true;
    }
}