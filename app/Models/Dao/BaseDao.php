<?php

namespace App\Models\Dao;

use App\Support\Loader;

class BaseDao {

    private $select     = '*';
    private $where      = array();
    private $whereParam = array();
    private $orderBy    = null;
    private $groupBy    = null;

    public function add($obj = null)
    {
        if (empty($obj) || !is_object($obj)) {
            return false;
        }
        $result = $this->addImp($obj);
        if ($result) {
            return $obj;
        }
        return false;
    }

    private function addImp($obj)
    {
        $cols = array_keys($obj->toAry());
        $vals = array_values($obj->toAry());
        $hold = array_fill(0, count($cols), '?');
        $sql = 'insert '.$this->getTableName().' ';
        $sql.= '(`'.implode("`, `", $cols).'`) ';
        $sql.= 'values ';
        $sql.= '('.implode(", ", $hold).'); ';
        return $this->getExecutor()->exeNoQuery($sql, $vals);
    }

    public function adds($objs = array())
    {
        if (empty($objs) || !is_array($objs)) {
            return false;
        }
        $result = $this->addsImp($objs);
        if ($result) {
            return $objs;
        }
        return false;
    }

    private function addsImp($objs)
    {
        $cols = array_keys($objs[0]->toAry());
        $hold = array_fill(0, count($cols), '?');
        $vals = array();
        foreach ($objs as $obj) {
            $vals = array_merge($vals, array_values($obj->toAry()));
        }
        $len = count($objs);
        $sql = 'insert '.$this->getTableName().' ';
        $sql.= '('.implode(", ", $cols).') ';
        $sql.= 'values ';
        for ($i = 0; $i < $len; $i++) {
            $sql.= '('.implode(", ", $hold).'), ';
        }
        $sql = rtrim($sql, ', ').';';
        return $this->getExecutor()->exeNoQuery($sql, $vals);
    }

    public function deleteById($id)
    {
        $sql = "delete from ".$this->getTableName()."  where id=?";
        return $this->getExecutor()->exeNoQuery($sql, array($id));
    }

    public function deleteByParam($param)
    {
        $where = " where 1 ";
        $updval = array();
        foreach ($param as $k => $v) {
            $where.= " and ".$k."='".$v."'";
        }
        $sql = "delete from ".$this->getTableName();
        $sql.=$where;
        //echo $sql;exit;

        return $this->getExecutor()->exeNoQuery($sql, array());
    }

    public function updateById($id, $param)
    {
        $updkey = array();
        $updval = array();
        foreach ($param as $k => $v) {
            $updkey[] = '`'.$k.'`=?';
            $updval[] = $v;
        }
        $updval[] = $id;
        $sql = "update ".$this->getTableName()." set ";
        $sql.= implode(',', $updkey);
        $sql.= " where id=?";

        return $this->getExecutor()->exeNoQuery($sql, $updval);
    }

    public function updateByUid($uid, $param)
    {
        $updkey = array();
        $updval = array();
        foreach ($param as $k => $v) {
            $updkey[] = $k.'=?';
            $updval[] = $v;
        }
        $updval[] = $uid;
        $sql = "update ".$this->getTableName()." set ";
        $sql.= implode(',', $updkey);
        $sql.= " where uid=?";

        return $this->getExecutor()->exeNoQuery($sql, $updval);
    }

    public function updateParamByRequest($param, $request)
    {
        $where = " where 1 ";
        $updkey = array();
        $updval = array();
        foreach ($param as $k => $v) {
            $updkey[] = '`'.$k.'`=?';
            $updval[] = $v;
        }
        foreach ($request as $k => $v) {
            $where.=' and `'.$k.'`="'.$v.'"';
        }

        $sql = "update ".$this->getTableName()." set ";
        $sql.= implode(',', $updkey);
        $sql.= $where;
        return $this->getExecutor()->exeNoQuery($sql, $updval);
    }

    public function getById($id = '0')
    {
        if (empty($id)) {
            return null;
        }

        $sql = "select * ";
        $sql.= "from ".$this->getTableName()." ";
        $sql.= "where id = ? ";
        $row = $this->getSlaveExecutor()->query($sql, array($id));
        if (is_null($row)) {
            return null;
        }
        $cls = '\App\Models\Entity\\'.substr(get_class($this), 15, -3);
        $obj = new $cls($row);

        return $obj;
    }

    protected function getExecutor()
    {
        return Loader::loadExecutor();
    }

    protected function getSlaveExecutor()
    {
        return Loader::loadSlaveExecutor();
    }

    final private function getTableName()
    {
        return $this->table;
    }

    final public function select()
    {
        if (func_num_args() == 1 && is_array(func_get_arg(0))) {
            $this->select = implode(',', func_get_arg(0));
        } else {
            $this->select = implode(',', func_get_args());
        }
        return $this;
    }

    final public function where()
    {
        $args    = func_get_args();
        $numArgs = func_num_args();
        if ($numArgs == 1 && is_array($args[0])) {
            foreach ($args[0] as $k=>$v) {
                if (is_string($v) || is_numeric($v)) {
                    $this->where[]      = $k.' = ?';
                    $this->whereParam[] = $v;
                } elseif (is_array($v) && count($v) == 3) {
                    $this->where[]      = $v[0].' '.$v[1].' ?';
                    $this->whereParam[] = $v[2];
                } else {
                    throw new \Exception("where param error");
                }
            }
        } elseif ($numArgs == 2) {
            if (is_string($args[0]) && (is_string($args[1]) || is_numeric($args[1]))) {
                $this->where[]      = $args[0].' = ?';
                $this->whereParam[] = $args[1];
            } elseif (is_string($args[0]) && is_array($args[1])) {
                $this->where[] = $args[0];
                foreach ($args[1] as $v) {
                    $this->whereParam[] = $v;
                }
            } else {
                throw new \Exception("where param error");
            }
        } elseif ($numArgs == 3) {
            if (is_string($args[0]) && is_string($args[1]) && (is_string($args[2]) || is_numeric($args[2]))) {
                $this->where[]      = $args[0].' '.$args[1].' ?';
                $this->whereParam[] = $args[2];
            } else {
                throw new \Exception("where param error");
            }
        } else {
            throw new \Exception("where param error");
        }
        return $this;
    }

    final public function whereIn($field, $ids)
    {
        if (is_array($ids)) {
            $idArr = $ids;
        } elseif (is_string($ids)) {
            $idArr = explode(',', $ids);
        } else {
            throw new \Exception("where param error");
        }
        if (!empty($idArr)) {
            $idStr = implode(',',array_fill(0, count($idArr), '?'));
            $this->where[] = $field.' in('.$idStr.')';
            foreach ($idArr as $v) {
                $this->whereParam[] = $v;
            }
        } else {
            $this->where[] = '1=2';
        }
        return $this;
    }

    final public function isNull($field)
    {
        if (is_string($field)) {
            $this->where[] = $field.' is null';
        } else {
            throw new \Exception("where param error");
        }
        return $this;
    }

    final public function gets($num = 0)
    {
        $sql = 'select '.$this->select.' from '.$this->getTableName();
        if (!empty($this->where)) {
            $sql.= ' where '.implode(' and ', $this->where);
        }
        if (!is_null($this->orderBy)) {
            $sql.= ' order by '.$this->orderBy;
        }
        if (!is_null($this->groupBy)) {
            $sql.= ' group by '.$this->groupBy;
        }
        if ($num) {
            $sql.= ' limit '.intval($num);
        }
        $rows = $this->getSlaveExecutor()->querys($sql, $this->whereParam);
        $this->cleanSelect();
        if (empty($rows)) {
            return array();
        } else {
            return $rows;
        }
    }

    final public function find($isObj = null)
    {
        $sql = 'select '.$this->select.' from '.$this->getTableName();
        if (!empty($this->where)) {
            $sql.= ' where '.implode(' and ', $this->where);
        }
        if (!is_null($this->orderBy)) {
            $sql.= ' order by '.$this->orderBy;
        }
        $sql.= ' limit 1';
        $row = $this->getSlaveExecutor()->query($sql, $this->whereParam);
        $this->cleanSelect();
        if (empty($row)) {
            return null;
        } else {
            if ($isObj) {
                $cls = is_string($isObj)?$isObj:'\App\Models\Entity\\'.substr(get_class($this), 15, -3);
                return new $cls($row);
            } else {
                return $row;
            }
        }
    }

    final public function count($field = null)
    {
        $select = $field?'count('.$field.')':'count(*)';
        $sql = 'select '.$select.' as num from '.$this->getTableName();
        if (!empty($this->where)) {
            $sql .= ' where '.implode(' and ', $this->where);
        }
        $sql .= ' limit 1';
        $row = $this->getSlaveExecutor()->query($sql, $this->whereParam);
        $this->cleanSelect();
        if (empty($row)) {
            return 0;
        } else {
            return $row['num'];
        }
    }

    final public function groupBy()
    {
        if (func_num_args() == 1 && is_array(func_get_arg(0))) {
            $this->groupBy = implode(',', func_get_arg(0));
        } else {
            $this->groupBy = implode(',', func_get_args());
        }
        return $this;
    }

    final public function orderBy()
    {
        if (func_num_args() == 1 && is_array(func_get_arg(0))) {
            $this->orderBy = implode(',', func_get_arg(0));
        } else {
            $this->orderBy = implode(',', func_get_args());
        }
        return $this;
    }

    final private function cleanSelect()
    {
        $this->select     = '*';
        $this->where      = array();
        $this->whereParam = array();
        $this->orderBy    = null;
        $this->groupBy    = null;
    }
}