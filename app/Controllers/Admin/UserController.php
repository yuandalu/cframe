<?php

namespace App\Controllers\Admin;

use App\Models\Svc\ErrorSvc;
use App\Models\Svc\UtlsSvc;
use App\Models\Svc\UserSvc;


class UserController extends BaseController
{
    const PER_PAGE_NUM = 15;// 默认分页数
    
    static $NOT_LOGIN_ACTION  = array();// 排除登录验证
    static $SUPER_MUST_VERIFY = array();// 必须具有权限包括超级管理员

    public function __construct()
    {
        $isLogin  = true;
        if (in_array(strtolower($this->getActionName()), self::$NOT_LOGIN_ACTION)) {
            $isLogin = false;
        }
        parent::__construct($isLogin, self::$SUPER_MUST_VERIFY);
    }

    public function indexAction()
    {
        $id = $this->getRequest('id','');
        if ($id > 0) {
            $User = UserSvc::getById($id);
            if (is_null($User)) {
                UtlsSvc::showMsg('没有这个ID', '/User/list');
            }
            $this->assign($User->toAry());
        }
        $this->assign('curr_menu', 'User');
        $this->assign('curr_submenu', 'User_add');
        return view('index');
    }


    public function addAction()
    {
        $param = array();
        $id    = $this->getRequest('id','');
        $param['mobile'] = $this->getRequest('mobile','');
        $param['nickname'] = $this->getRequest('nickname','');
        $param['password'] = $this->getRequest('password','');
        $param['salt'] = $this->getRequest('salt','');
        $param['status'] = $this->getRequest('status','');

        // 参数校验，有时这是必须的
        // if (empty($param['name'])) {
        //     return ErrorSvc::format(ErrorSvc::ERR_PARAM_EMPTY, null, '姓名不能为空');
        // }

        if ($id != '') {
            $param['utime'] = date('Y-m-d H:i:s');
            $obj = UserSvc::updateById($id, $param);
            return ErrorSvc::format(ErrorSvc::ERR_OK, null, '保存成功');
        } else {
            $obj = UserSvc::add($param);
            return ErrorSvc::format(ErrorSvc::ERR_OK, null, '新增成功');
        }
    }

    public function deleteAction()
    {
        return ErrorSvc::format(
            ErrorSvc::ERR_OK,
            null,
            '请考虑清楚数据是否真的需要删除，是否可以使用状态标识来进行软删除'
        );
    }

    public function listAction()
    {
        $request = array();
        $request['id'] = $this->getRequest('id','');
        $request['startdate'] = $this->getRequest('startdate','');
        $request['enddate'] = $this->getRequest('enddate','');
        $request['utime'] = $this->getRequest('utime','');
        $request['mobile'] = $this->getRequest('mobile','');
        $request['nickname'] = $this->getRequest('nickname','');
        $request['password'] = $this->getRequest('password','');
        $request['salt'] = $this->getRequest('salt','');
        $request['status'] = $this->getRequest('status','');
        $orderby  = $this->getRequest('orderby');
        // 必须校验 orderby 此处没有做预处理

        $list = UserSvc::lists($request, array(
            'per_page'=>self::PER_PAGE_NUM,
            'page_param'=>'p',
            'curr_page'=>$this->getRequest('p',1),
            'file_name'=>'/User/list/',
            'orderby'=>$orderby
        ));

        $this->assign($request);
        $this->assign('orderby', $orderby);
        $this->assign('list', $list);
        $this->assign('curr_menu', 'User');
        $this->assign('curr_submenu', 'User_list');
        return view('list');
    }

    public function exportAction()
    {
        $request = array();
        $request['id'] = $this->getRequest('id','');
        $request['startdate'] = $this->getRequest('startdate','');
        $request['enddate'] = $this->getRequest('enddate','');
        $request['utime'] = $this->getRequest('utime','');
        $request['mobile'] = $this->getRequest('mobile','');
        $request['nickname'] = $this->getRequest('nickname','');
        $request['password'] = $this->getRequest('password','');
        $request['salt'] = $this->getRequest('salt','');
        $request['status'] = $this->getRequest('status','');
        $orderby  = $this->getRequest('orderby');
        // 必须校验 orderby 此处没有做预处理

        $list = UserSvc::lists($request, array(
            'per_page'=>self::PER_PAGE_NUM,
            'page_param'=>'p',
            'curr_page'=>$this->getRequest('p',1),
            'file_name'=>'/User/list/',
            'orderby'=>$orderby
        ), true);

        // 表格导出
        $table = '<table border="1"><tr>
        <th>ID</th><th>创建时间</th><th>修改时间</th><th>手机号</th><th>昵称</th><th>密码</th><th>加盐</th><th>状态</th>
        </tr>';
        foreach ($list as $k => $v) {
            $table .= '<tr>';
            $table .= '<th>'.$v['id'].'</th><th>'.$v['ctime'].'</th><th>'.$v['utime'].'</th><th>'.$v['mobile'].'</th><th>'.$v['nickname'].'</th><th>'.$v['password'].'</th><th>'.$v['salt'].'</th><th>'.$v['status'].'</th>';
            $table .= '</tr>';
        }
        $table .= '</table>';
        echo $table;
        // CSV导出
        // $str = "ID,创建时间,修改时间,手机号,昵称,密码,加盐,状态\n";
        // foreach ($list as $k => $v) {
            // $id = $v['id'];
            // $ctime = $v['ctime'];
            // $utime = $v['utime'];
            // $mobile = $v['mobile'];
            // $nickname = $v['nickname'];
            // $password = $v['password'];
            // $salt = $v['salt'];
            // $status = $v['status'];
            // $str .= $id.','.$ctime.','.$utime.','.$mobile.','.$nickname.','.$password.','.$salt.','.$status."\n";
        // }
        // header("Content-type:text/csv");   
        // header("Content-Disposition:attachment;filename=".date('Ymd').'.csv');   
        // header('Cache-Control:must-revalidate,post-check=0,pre-check=0');   
        // header('Expires:0');   
        // header('Pragma:public');  
        // echo $str;
        exit;
    }

}
