<?php

namespace App\Models\Dao;

use App\Support\Pager;
use App\Models\Entity\AdmUser;

class AdmUserDao extends BaseDao
{
    protected $table = 'adm_users';

    public function getPager( $request_param, $sql_condition =array(), $sql_param=array() , $options, $export = false)
    {
        $sql = "select * ";
        $sql.= "from ".$this->table." ";
        if (!empty( $sql_condition )) {
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

    public function getByEname($ename)
    {
        $sql = "select * from ".$this->table." where ename= ? and status=".AdmUser::ACTIVE_Y;
        $data = $this->getExecutor()->query($sql, array($ename));
        if (empty($data)) {
            return null;
        }
        return new AdmUser($data);
    }

    public function getById($id = '0')
    {
        $sql = "select * from ".$this->table." where id=?";
        return $this->getExecutor()->querys($sql,array($id));
    }

    public function forbiddenAccount($id)
    {
        $sql = "update ".$this->table." set status = ? where id= ?";
        return $this->getExecutor()->exeNoquery($sql,array(2,$id));
    }

    public function checkName($name)
    {
        $sql = "select ename from ".$this->table." where ename=?";
        return  $this->getExecutor()->querys($sql,array($name));
    }

    public function getUidByEname($name)
    {
        $sql = "select id from ".$this->table." where ename=?";
        return $this->getExecutor()->query($sql,array($name));
    }

    public function delauth($param)
    {
        $sql = "delete from adm_userauth where aid=? and uid=?";
        return $this->getExecutor()->exeNoquery($sql,$param);
    }
    public function getAuthByUid($uid)
    {
        $sql = "select name,uid,aid from adm_auths left join adm_userauth on adm_auths.id=adm_userauth.aid where adm_userauth.uid=?";
        if ($uid==0) {
            return array();
        }
        return $this->getExecutor()->querys($sql,array($uid));
    }
    public function getUidByAuth($aid)
    {
        $sql = "select uid,user from adm_userauth where aid=?";
        return  $this->getExecutor()->querys($sql,array($aid));
    }
    public function getAdminByuid($uid=0)
    {
        if ($uid==0) {
            return array();
        }
        $sql = "select * from ".$this->table." where id=?";
        return $this->getExecutor()->querys($sql,array($uid));
    }

}