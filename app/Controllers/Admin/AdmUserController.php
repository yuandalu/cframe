<?php

namespace App\Controllers\Admin;

use App\Models\Svc\UtlsSvc;
use App\Models\Svc\AdmUserSvc;
use App\Models\Svc\AdmAuthSvc;

class AdmUserController extends BaseController
{
    const PER_PAGE_NUM = 15;// 默认分页数

    static $NOT_LOGIN_ACTION  = array();// 排除登录验证

    public function __construct()
    {
        $isLogin  = true;
        if (in_array(strtolower($this->getActionName()), self::$NOT_LOGIN_ACTION)) {
            $isLogin = false;
        }
        parent::__construct($isLogin);
    }

    public function indexAction()
    {
        $request = array();
        $request['name'] = $this->getRequest('name','');
        $orderby  = $this->getRequest('orderby');
        $list = AdmUserSvc::lists($request, array('per_page'=>self::PER_PAGE_NUM, 'page_param'=>'cp', 'curr_page'=>$this->getRequest('cp',1),'file_name'=>'/AdmUser/index/'));
        $this->assign('list', $list);
        $this->assign('name', $request['name']);
        $this->assign('curr_menu', 'manage');
        $this->assign('curr_submenu', 'manage_admuser');
        return view('index');
    }

    public function addAction()
    {
        $request = array();
        $request['name'] = $this->getRequest("name");
        $request['ename'] = $this->getRequest("ename");
        $request['depart'] = $this->getRequest("depart");
        $request['position'] = $this->getRequest("position");
        $request['role'] = $this->getRequest("role");
        if (AdmUserSvc::getByEname($request['ename'])) {
            $data = array("code"=>"fail","msg"=>"用户已存在");
            echo json_encode($data);
            exit;
        }
        $re = AdmUserSvc::add($request);
        $res = $re->toAry();
        if(!empty($res))
        {
            $data = array("code"=>"succ","data"=>$res,"msg"=>"添加成功");
            echo json_encode($data);
            exit;
        }else
        {
            $data = array("code"=>"fail","msg"=>"提交失败，或数据重复");
            echo json_encode($data);
            exit;
        }

    }

    public function addGradeAction()
    {
        $id = $this->getRequest("id");//获取当前管理员的UID
        if (!is_numeric($id)) {
            return false;
        }
        $name = $this->getRequest("admin");//当前管理员的名字
        $adminuser = AdmUserSvc::getById($id);//当前管理员
        $haveauth = AdmAuthSvc::getAuthByUid($id);

        $auths = AdmAuthSvc::getAll();
        $str = '<div class="row">
                <div class="col-md-12">
                <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">权限设置 - '.$name.'</h3>
                    <div class="pull-right">
                    </div>
                </div>
                <div class="box-body">
                <form action="/AdmAuth/addauth/" method="post" id="authForm">';
        $str.="<input type='hidden' value='".$name."' name='admin'><input type='hidden' value='".$id."' name='uid'>";

        $tempArr = array();
        foreach ($auths as $key=>$value) {
            if (!empty($haveauth)) {
                if (!in_array($value['id'],$haveauth)) {
                    $tempArr[] = $value;
                }
            } else {
                $tempArr[] = $value;
            }
        }

        $str .= '<table id="list" class="table table-bordered table-condensed table-striped table-hover">';
        if (!empty($tempArr)) {
            foreach ($tempArr as $k=>$v) {
                if ($k == 0 || ($k != 2 && $k % 4 == 0)) {
                    $str .= '<tr>';
                }
                $str.= '<td><input type="checkbox" name="auths[]"" value="'.$v['id'].'"> '.$v['name'].'</td>';
                if ($k != 1 && ($k + 1) % 4 == 0) {
                    $str .= '</tr>';
                }
            }
            for ($i = 0; $i < (4 - (count($tempArr) % 4)); $i++) {
                $str .= '<td></td>';
            }
            if (count($tempArr) % 4 != 0) {
                $str .= '</tr>';
            }
        } else {
            $str .= ' <tr><td>该账户已拥有所有权限！</td></tr>';
        }
        $str .= "</table>";
        $str.='</form>
                </div>
                <!--<div class="box-footer clearfix">
                    <div class="pull-right">
                    </div>-->
                </div>
                </div>
                </div>
                </div>';
        return $str;
    }

    // 获取某个人所有权限
    public function modifyauthAction(){
        $id = $this->getRequest("uid",0);
        $auth = AdmUserSvc::getAuthByUid($id);
        $admin = AdmUserSvc::getAdminByuid($id);
        $this->assign('admin',$admin);
        $this->assign('data',$auth);
        $this->assign('curr_menu', 'manage');
        $this->assign('curr_submenu', 'manage_admuser');
        return view('modifyauth');
    }

    // 账号禁用
    public function forbiddenAction()
    {
        $id = $this->getRequest('id');
        AdmUserSvc::forbiddenAccount($id);
        echo json_encode(array('msg'=>"SUCC"));
        exit;
    }

    public function checknameAction()
    {
        $name = $this->getRequest("name");
        $res = AdmUserSvc::checkName($name);
        if (!empty($res)) {
            echo json_encode(array("code"=>"yes","msg"=>"该人已存在！"));
        } else {
            echo json_encode(array("code"=>"no","msg"=>"和邮箱前缀保持一致！"));
        }
        exit;
    }

    // 删除某条权限
    public function delauthAction()
    {
        $request['id'] = $this->getRequest('id');
        $request['uid'] = $this->getRequest('uid');
        $re = AdmUserSvc::delauth($request);
        if ($re) {
            echo json_encode(array('code'=>"succ"));
        } else {
            echo json_encode(array('code'=>"fail"));
        }

        exit;
    }

    public function saveroleAction()
    {
        $id = $this->getRequest("id");
        $name = trim($this->getRequest("name"));
        $obj = AdmUserSvc::updateByid($id,array('role'=>$name));
        echo json_encode(array('code'=>"yes"));
        exit;
    }

    public function deleteuserauthAction()
    {
        $id_auth = $this->getRequest('id_auth',array());
        foreach ($id_auth as $v) {
            list($aid, $uid) =  explode('_',$v);
            $request['id'] =$aid;
            $request['uid'] = $uid;
            AdmUserSvc::delauth($request);
        }
        UtlsSvc::showMsg('移除完成',$_SERVER['HTTP_REFERER']);
    }
}