<?php

namespace App\Models\Dao;

class AdmAuthNodeDao extends BaseDao
{
    protected $table = 'adm_authnode';

    public function getByUid($uid)
    {
        $sql = "select * ";
        $sql.= "from ".$this->table." ";
        $sql.= "where uid = ? ";
        $row = $this->getExecutor()->query($sql, array($uid));
        if (empty($row)) {
            return null;
        }
        return $row;
    }

    public function getPager($request_param, $sql_condition = array(), $sql_param = array(), $options, $export = false)
    {
        $sql = "select * ";
        $sql.= "from ".$this->table." ";
        if (!empty($sql_condition)) {
            $sql.= 'where '. implode(' and ', $sql_condition);
        }
        if ($options['orderby']) {
            $sql.= " order by ".$options['orderby']." ";
        } else {
            $sql.= " order by id desc ";
        }
        $options['sql']        = $sql;
        $options['sql_param']    = $sql_param;
        $options['request_param'] = $request_param;
        $options['per_page']      = $options['per_page']?$options['per_page']:20;
        $list = Pager::render($options);

        if ($export) {

        } else {

        }

        return $list;
    }

    public function getAll()
    {
        $sql = "select concat(contr,'_',action) as a from ".$this->table." ";
        $data = $this->getExecutor()->querys($sql);
        foreach ($data as $v) {
            $res[]=$v['a'];
        }
        $result = array_unique($res);
        return $result;
    }

    public function getControauth()
    {
        $sql = "select contr from " .$this->table." where aid=0 group by contr";
        $re = $this->getExecutor()->querys($sql);
        return $re;
    }
    public function getActionauth()
    {
        $sql = "select action from ".$this->table." where aid=0";
        $re = $this->getExecutor()->querys($sql);
        return $re;
    }
    public function verify($c, $a)
    {
        $sql = "select aid from ".$this->table." where contr= ? and action= ?";
        $data = $this->getExecutor()->querys($sql, array($c, $a));
        if (empty($data)) {
            return array();
        }
        foreach ($data as $value) {
            $datas[] = $value['aid'];
        }
        return $datas;
    }
    public function updateByCA($param)
    {
        $sql = "update ".$this->table." set aid=".$param['aid']." where contr='".$param['contr']."' and action= '".$param['action']."'";
        return $this->getExecutor()->exeNoQuery($sql);
    }
}