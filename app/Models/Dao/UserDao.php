<?php

namespace App\Models\Dao;

use App\Support\Pager;

class UserDao extends BaseDao
{
    protected $table = 'users';

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
        $options['sql']           = $sql;
        $options['sql_param']     = $sql_param;
        $options['request_param'] = $request_param;
        $options['per_page']      = $options['per_page']?$options['per_page']:20;

        if (!$export) {
            $list = Pager::render($options);
        } else {
            $list = $this->getExecutor()->querys($sql, $sql_param);
            if (empty($list)) {
                $list = array();
            }
        }

        return $list;
    }

}
