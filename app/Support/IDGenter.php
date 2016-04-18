<?php

namespace App\Support;

class IDGenter
{
    const DEF_OBJ    = 'other';
    const TABLE_NAME = 'sys_idgenter';
    
    private $executor;
    private $container;
    
    public function __construct($executor)
    {
        $this->executor  = $executor;
        $this->container = array();
    }

    public function create($obj = self::DEF_OBJ)
    {
        if (false === $this->hasReg($obj)) {
            $this->reg($obj);
        }
        
        if ($this->full($obj)) {
            $this->renew($obj);
        }
        
        $this->container[$obj]['id']++;
        return (string) $this->container[$obj]['id'];
    }
    
    private function hasReg($obj)
    {
        return array_key_exists($obj, $this->container);
    }
    
    private function reg($obj)
    {
        $sql = 'select id, step ';
        $sql.= 'from '.self::TABLE_NAME.' ';
        $sql.= "where obj = '".$obj."' ";
        $row = $this->executor->query($sql);
        
        if (is_null($row)) {
            return false;
        }
        
        $info = array('id' => $row['id'], 'step' => $row['step'], 'max_id' => $row['id']);
        $this->container[$obj] = $info;
        return true;
    }
    
    private function full($obj)
    {
        if ($this->container[$obj]['id'] == $this->container[$obj]['max_id']) {
            return true;
        }
        return false;
    }

    private function renew($obj)
    {
        $sql = 'update '.self::TABLE_NAME.' set ';
        $sql.= 'id = last_insert_id(id + '.$this->container[$obj]['step'].') ';
        $sql.= "where obj = '".$obj."'";
        if (false === $this->executor->exeNoQuery($sql)) {
            return false;
        }
        
        $sql = 'select last_insert_id() as id ';
        $row = $this->executor->query($sql);
        if (is_null($row)) {
            return false;
        }
        
        $this->container[$obj]['max_id'] = $row['id'];
        $this->container[$obj]['id'] = $row['id'] - $this->container[$obj]['step'];
        return true;
    }
}