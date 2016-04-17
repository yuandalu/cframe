<?php

namespace App\Controllers\Admin;

use App\Models\Svc\UtlsSvc;
use App\Models\Svc\AdminSvc;
use App\Models\Svc\AdmMenuSvc;
use App\Models\Svc\AdmAuthSvc;
use App\Models\Svc\AdmAuthNodeSvc;
use App\Models\Svc\LogSvc;

class AdmMenuController extends BaseController
{
    const PER_PAGE_NUM = 20;
    public function __construct()
    {
        parent::__construct();
    }

    public function indexAction()
    {
        $oneclass = AdmMenuSvc::getAll();
        $auths = AdmAuthSvc::getAllauth();
        $this->assign("oneclass",$oneclass);
        $this->assign("auths",$auths);
        $this->assign('curr_menu', 'manage');
        $this->assign('curr_submenu', 'manage_admmenu');
        return view('index');
    }


    public function addAction()
    {
        $param = array();
        $param['name'] = $this->getRequest('name','');
        $param['url'] = $this->getRequest('url','');
        $param['sort'] = $this->getRequest('sort','');
        $class = $this->getRequest('groupid');
        $crr = explode(',',$class);
        $param['oneclass'] = $crr[1]?$crr[1]:$this->getRequest('oneclass');
        $param['groupid'] = $crr[0];
        $param['aid'] = $this->getRequest('aid','');
        $param['curr_menu'] = $this->getRequest('curr_menu','');
        $param['curr_submenu'] = $this->getRequest('curr_submenu','');

        // 自己写判断参数检查
        if (AdmMenuSvc::getByName($param['name'])) {
            echo '名称已存在';
            exit;
        }
        
        $obj = AdmMenuSvc::add($param);
        var_dump($obj);
        exit;
        //UtlsSvc::showMsg('添加成功','/AdmMenu/list/');


    }

    public function editAction()
    {
        $id = $this->getRequest("id");
        $data = AdmMenuSvc::getById($id);
        $auths = AdmAuthSvc::getAllauth();
        $this->assign("auths",$auths);
        $this->assign("data",$data);
        $this->assign('curr_menu', 'manage');
        $this->assign('curr_submenu', 'manage_admmenu');
        return view('edit');
    }

    public function modifyAction()
    {
        $id = $this->getRequest("id");
        $request['oneclass'] = $this->getRequest("oneclass");
        $request['name'] = $this->getRequest("name");
        $request['aid'] = $this->getRequest("aid");
        $request['sort'] = $this->getRequest("sort");
        $request['url'] = $this->getRequest("url");
        $request['curr_menu'] = $this->getRequest("curr_menu");
        $request['curr_submenu'] = $this->getRequest("curr_submenu");
        $data = explode("/",$request['url']);
        $param['contr'] = $data[1]?$data[1]:$data[0];
        $param['action'] = $data[2]?$data[2]:$data[1];
        $param['aid'] = $request['aid'];
        if(empty($request['name']) || empty($request['aid'])  || empty($request['oneclass'])  || empty($request['url']) || empty($request['curr_menu']) || empty($request['curr_submenu']) )
        {
            UtlsSvc::showMsg('信息不全', "/AdmMenu/list/?id={$id}");
            exit();
        }
        $re  = AdmMenuSvc::updateById($id,$request);
        $res = AdmAuthNodeSvc::updateByCA($param);
        if($re)
        {
            $action = '修改栏目['.$column.']';
            LogSvc::writeLog($action,array('action'=>$action,'content'=>$request));
            UtlsSvc::showMsg('修改成功', "/AdmMenu/list/?id={$id}");
            exit;
        }else
        {
            UtlsSvc::showMsg('未做任何改变', "/AdmMenu/list/?id={$id}");
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
        $request['name'] = $this->getRequest('name','');
        $request['oneclass'] = $this->getRequest('oneclass','');
        $request['url'] = $this->getRequest('url','');
        $request['sort'] = $this->getRequest('sort','');
        $request['groupid'] = $this->getRequest('groupid','');
        $request['aid'] = $this->getRequest('aid','');
        $request['curr_menu'] = $this->getRequest('curr_menu','');
        $request['curr_submenu'] = $this->getRequest('curr_submenu','');
        $orderby  = $this->getRequest('orderby');

        $list = AdmMenuSvc::lists($request,array('per_page'=>self::PER_PAGE_NUM, 'page_param'=>'cp', 'curr_page'=>$this->getRequest('cp',1),'file_name'=>'/AdmMenu/list/','orderby'=>$orderby));

        $this->assign('request',$request);

        $this->assign( 'list', $list );
        $this->assign( 'per_page_num', self::PER_PAGE_NUM );

        $this->assign('curr_menu', 'manage');
        $this->assign('curr_submenu', 'manage_admmenu');
        $this->assign('orderby', $orderby);
        return view('list');
    }


}
