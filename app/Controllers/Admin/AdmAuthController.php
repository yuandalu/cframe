<?php

namespace App\Controllers\Admin;

use App\Models\Svc\UtlsSvc;
use App\Models\Svc\AdminSvc;
use App\Models\Svc\AdmAuthSvc;
use App\Models\Svc\AdmUserAuthSvc;

class AdmAuthController extends BaseController
{
    const PER_PAGE_NUM = 20;
    public function __construct()
    {
        parent::__construct();
    }

    public function indexAction()
    {
        $this->assign('curr_menu', 'manage');
        $this->assign('curr_submenu', 'auths_add');

    }


    public function addAction()
    {
        $param = array();
        $param['name'] = $this->getRequest('name','');

        //自己写判断参数检查
        if (AdmAuthSvc::getByName($param['name'])) {
            UtlsSvc::showMsg('已存在','/AdmAuth/index/');
        }

        $obj = AdmAuthSvc::add($param);
        UtlsSvc::showMsg('添加成功','/AdmAuth/list/');
        exit;

    }

    public function addauthAction()
    {
        $admin = $this->getRequest("admin");
        $auths = $this->getRequest("auths");
        $uid = $this->getRequest("uid");
        if(empty($auths)){UtlsSvc::showMsg('操作失败','/AdmUser/index/');}
        $param['uid'] = $uid;
        $param['user'] = $admin;
        foreach($auths as $v)
        {
            $param['aid'] = $v;
            AdmUserAuthSvc::add($param);
        }
        UtlsSvc::showMsg('添加成功','/AdmUser/index/');
        exit;
    }
    public function listAction()
    {
        $request = array();
        $request['id'] = $this->getRequest('id','');
        $request['startdate'] = $this->getRequest('startdate','');
        $request['enddate'] = $this->getRequest('enddate','');
        $request['utime'] = $this->getRequest('utime','');
        $request['name'] = $this->getRequest('name','');
        $orderby  = $this->getRequest('orderby');

        $list = AdmAuthSvc::lists($request,array('per_page'=>self::PER_PAGE_NUM, 'page_param'=>'cp', 'curr_page'=>$this->getRequest('cp',1),'file_name'=>'/AdmAuth/list/','orderby'=>$orderby));

        $this->assign('request',$request);

        $this->assign( 'list', $list );
        $this->assign( 'per_page_num', self::PER_PAGE_NUM );

        $this->assign('curr_menu', 'manage');
        $this->assign('curr_submenu', 'auths_add');

        $this->assign($request);
        $this->assign('orderby', $orderby);
    }
}