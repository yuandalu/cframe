<?php

namespace App\Controllers\Front;

use Elephant\Foundation\Controller;

class BaseController extends Controller
{
    public function __construct($require_login = true)
    {
    //     //#mark用户信息变更需要更新用户的cookie，或者让用户重新登录
    //     $user = UserSvc::info(true);
    //     //用户登录认证
        if ($require_login) {
    //         if (!$user) {
    //             if (UtlsSvc::isMobile()) {
    //                 ErrorSvc::showJson(ErrorSvc::ERR_NO_LOGIN);
    //             }
    //             header('Location: /login?go='.urlencode('http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']));
    //             exit;
    //         }
    //         #mark 如果同时恰好在同一秒登陆，此方式就会不生效，可以考虑精确到毫秒或者其他方式
    //         $logintime = LoaderSvc::loadDBCache()->get('logintime_'.$user->uid);
    //         if ($user->logintime != $logintime) {
    //             setcookie('T', '', -1, '/', 'b.youmi.cn');
    //             setcookie('B', '', -1, '/', 'b.youmi.cn');
    //             if (UtlsSvc::isMobile()) {
    //                 ErrorSvc::showJson(ErrorSvc::ERR_NO_LOGIN, '', '用户信息被更新，请重新登陆');
    //             }
    //             UtlsSvc::showMsg('用户信息被更新，请重新登陆', '/login?go='.urlencode('http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']));
    //         }
    //         $company = CompanySvc::getById($user->companyid);
    //         if (!$company) {
    //             if (UtlsSvc::isMobile()) {
    //                 ErrorSvc::showJson(ErrorSvc::ERR_NO_LOGIN, '', '公司不存在');
    //             }
    //             UtlsSvc::showMsg('公司不存在', '/login/logout');
    //         }
    //         if ($company->endtime < date('Y-m-d')) {
    //             if (UtlsSvc::isMobile()) {
    //                 ErrorSvc::showJson(ErrorSvc::ERR_NO_LOGIN, '', '合同已到期，请联系客服');
    //             }
    //             UtlsSvc::showMsg('合同已到期，请联系客服', '/login/logout');
    //         }
    //         if ($company->status != Company::STATUS_Y) {
    //             if (UtlsSvc::isMobile()) {
    //                 ErrorSvc::showJson(ErrorSvc::ERR_NO_LOGIN, '', '公司被禁用，请联系客服');
    //             }
    //             UtlsSvc::showMsg('公司被禁用，请联系客服', '/login/logout');
    //         }
    //         $this->assign('loginUser', $user);
    //         $this->assign('currentCompany', $company);
        }
    //     if(!isset($_COOKIE['L']))
    //     {
    //         setcookie("L", time(),time()+7200, "/", "b.youmi.cn");
    //         $param_uaction['uid']=$user->uid;
    //         $param_uaction['companyid']=$user->companyid;
    //         $param_uaction['departmentid']=$user->departmentid;
    //         $param_uaction['roleid']=$user->roleid;
    //         UactionSvc::add($param_uaction);
    //     }
    }
}