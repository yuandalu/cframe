<?php

namespace App\Controllers\Admin;

use App\Models\Svc\LogSvc;

class LogController extends BaseController
{
    const PER_PAGE_NUM = 15;
    public function __construct()
    {
        parent::__construct();
    }

    public function indexAction()
    {
        $list = LogSvc::getLogs('adm_loginlog',array('per_page'=>self::PER_PAGE_NUM, 'page_param'=>'cp', 'curr_page'=>$this->getRequest('cp',1),'file_name'=>'/log/index/'));
        $this->assign('list', $list);
        $this->assign('curr_menu', 'manage');
        $this->assign('curr_submenu', 'manage_log');
        return view('index');
    }

    public function operateAction()
    {
        $username = $this->getRequest('username');
        $kw = $this->getRequest('kw');
        $start = $this->getRequest('start');
        $end = $this->getRequest('end');

        $request  = array('username'=>$username, 'kw'=>$kw, 'start'=>$start, 'end'=>$end);

        $action = LogSvc::getLogs('adm_actlog',array('per_page'=>self::PER_PAGE_NUM, 'page_param'=>'cp', 'curr_page'=>$this->getRequest('cp',1),'file_name'=>'/log/operate/'), $request);
        $this->assign('operate', $action);
        $this->assign('list', $action);
        $this->assign('curr_menu', 'log');

        $this->assign('kw', $kw);
        $this->assign('username', $username);
        $this->assign('start', $start);
        $this->assign('end', $end);

        $this->assign('curr_menu', 'manage');
        $this->assign('curr_submenu', 'manage_operate');
        return view('operate');
    }

    public function getLogDetailAction()
    {
        $logid = $this->getRequest('id');
        $LogDetails = LogSvc::getDetailByid($logid);
        echo "<pre>";
        $content = unserialize($LogDetails[0]['content']);
        foreach($LogDetails as $v)
        {
            echo '<div><b>'.$v['username'].'</b> 于 '.$v['action_time'].' 在IP为：<b>'.$v['ip'].'</b> 的主机上 '.$v['action'].' 具体操作内容是：</div>';
        }
        print_r($content);
        exit;
    }
}