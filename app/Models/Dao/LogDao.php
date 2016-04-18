<?php

namespace App\Models\Dao;

use App\Support\Loader;
use App\Support\Pager;
use App\Models\Svc\UtlsSvc;

class LogDao extends BaseDao
{
    protected $table = 'log';

    public function loginLog($param)
    {
        $sql = "insert into adm_loginlog (`username`,`ip`,`login_time`,`result`) values('".$param['username']."','".$param['ip']."','".$param['time']."',".$param['result'].")";
        $this->getExecutor()->exeNoQuery($sql);
    }

    public function writeLog($action,$content)
    {
        $ip = UtlsSvc::getClientIP();
        $param['ip'] = $ip;
        $param['username'] = Loader::loadSess()->get( 'admin_user')?Loader::loadSess()->get( 'admin_user'):"system";
        $param['time'] = date('Y-m-d H:i:s');
        $param['action'] = $action;
        $param['content'] = serialize($content);
        $sql = "insert into adm_actlog (username,action_time,action,content,ip) values(?,?,?,?,?)";
        return $this->getExecutor()->exeNoQuery($sql,array($param['username'],$param['time'],$param['action'],$param['content'],$param['ip']));
    }

    public function getLogs($table,$options, $request = array(), $request_param= array(), $sql_condition= array(),$sql_param= array())
    {
        $sql = "select * from ".$table;
        if(!empty( $sql_condition ))
        {
            $sql.= ' where '. implode(' and ', $sql_condition);
        }

        $options['sql']           = $sql . " order by id desc";
        $options['sql_param']     = $sql_param;
        $options['request_param'] = $request_param;
        $options['per_page']      = $options['per_page']?$options['per_page']:10;

        $list = Pager::render($options);
        return $list;
    }

    public function getDetailByid($id)
    {
        $sql = "select * from adm_actlog where id = ?";
        $data = $this->getExecutor()->querys($sql,array($id));
        return $data;
    }

}