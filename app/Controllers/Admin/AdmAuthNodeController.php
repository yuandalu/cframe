<?php

namespace App\Controllers\Admin;

use App\Models\Svc\UtlsSvc;
use App\Models\Svc\AdminSvc;
use App\Models\Svc\AdmAuthSvc;
use App\Models\Svc\AdmAuthNodeSvc;
use App\Models\Svc\LogSvc;

class AdmAuthNodeController extends BaseController
{
    const PER_PAGE_NUM = 20;
    public function __construct()
    {
        parent::__construct();
    }

    public function indexAction()
    {
        $auth = AdmAuthSvc::getauth();
        $auths = AdmAuthSvc::getAllauth();
        $this->assign("auth",$auth);
        $this->assign('curr_menu', 'manage');
        $this->assign('curr_submenu', 'manage_admauthnode');

    }

    public function addAction()
    {
        $param = array();
        $param['aid'] = $this->getRequest('aid','');
        $param['contr'] = $this->getRequest('contr','');
        $param['action'] = $this->getRequest('action','');

        /*自己写判断参数检查
        if($param['ctype']==''||$param['name'])
        {
            UtlsSvc::showMsg('有为空的参数','/AdmAuthNode/create/');
        }
        */

        $obj = AdmAuthNodeSvc::add($param);
        var_dump($obj);
        exit;
        //UtlsSvc::showMsg('添加成功','/AdmAuthNode/list/');
    }

    public function addauthAction()
    {
        $param['name'] = trim($this->getRequest("name"));
        $data['contr'] = $this->getRequest("contr");
        $data['action'] = $this->getRequest("action");
        $obj = AdmAuthSvc::add($param);
        $data['aid'] = $obj->id;
        $objs = AdmAuthNodeSvc::updateByCA($data);
        UtlsSvc::showMsg('添加成功','/AdmAuthNode/list/');
        exit;
    }
    public function updateAuthsAction()
    {
        $ids = $this->getRequest('ids');
        $auths = intval($this->getRequest('auths',0));

        foreach($ids as $id=>$v)
        {
            AdmAuthNodeSvc::updateById($id, array('aid'=>$auths));
        }
        UtlsSvc::showMsg('修改成功',$_SERVER['HTTP_REFERER']);
    }

    public function editAction()
    {
        $id = $this->getRequest("id");
        $data = AdmAuthNodeSvc::getById($id);
        $auths = AdmAuthSvc::getAllauth();
        $this->assign('data', $data );
        $this->assign('auths', $auths );
        $this->assign('curr_menu', 'manage');
        $this->assign('curr_submenu', 'manage_admauthnode');
    }

    public function modifyAction()
    {
        $id = $this->getRequest("id");
        $param['contr'] = $this->getRequest('contr');
        $param['action'] = $this->getRequest('action');
        $param['aid'] = $this->getRequest('aid');
        $re = AdmAuthNodeSvc::updateById($id,$param);
        if ($re) {
            $action = '修改程序权限['.$id.']';
            LogSvc::writeLog($action,array('action'=>$action,'content'=>$param));
            UtlsSvc::showMsg('修改成功', '/AdmAuthNode/list/');
            exit;
        } else {
            UtlsSvc::showMsg('未做任何改变', '/AdmAuthNode/list/?id='.$id.'');
            exit;
        }
        exit;
    }
    public function listAction()
    {
        $request = array();
        $request['id'] = $this->getRequest('id','');
        $request['startdate'] = $this->getRequest('startdate','');
        $request['enddate'] = $this->getRequest('enddate','');
        $request['utime'] = $this->getRequest('utime','');
        $request['aid'] = $this->getRequest('aid','');
        $request['contr'] = $this->getRequest('contr','');
        $request['action'] = $this->getRequest('action','');
        $orderby  = $this->getRequest('orderby');



        $list = AdmAuthNodeSvc::lists($request,array('per_page'=>self::PER_PAGE_NUM, 'page_param'=>'cp', 'curr_page'=>$this->getRequest('cp',1),'file_name'=>'/AdmAuthNode/list/','orderby'=>$orderby));

        $this->assign('request',$request);

        $this->assign( 'list', $list );
        $this->assign( 'per_page_num', self::PER_PAGE_NUM );

        $this->assign('curr_menu', 'manage');
        $this->assign('curr_submenu', 'manage_admauthnode');

        $this->assign($request);
        $this->assign( 'authconf',  AdmAuthSvc::getConf());
        $this->assign('orderby', $orderby);
    }
}