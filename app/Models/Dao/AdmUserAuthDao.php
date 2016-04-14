<?php

namespace App\Models\Dao;

class AdmUserAuthDao extends BaseDao
{
    protected $table = 'adm_userauth';

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

    public function getAid()
    {
        $sql = "select id ";
        $sql.= "from ".$this->table." order by id desc";
        $data = $this->getExecutor()->querys($sql, array());
        if (empty($data)) {
            return array();
        }
        foreach ($data as $value) {
            $auths[] = $value['id'];
        }
        return $auths;
    }
}