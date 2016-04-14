<?php
class AuthDao extends BaseDao
{/*{{{*/
	public function getPager( $request_param, $sql_condition =array(), $sql_param=array() , $options)
	{/*{{{*/
        $sql = "select * ";
		$sql.= "from ".self::getTableName()." ";
		if(!empty( $sql_condition ))
		{
			$sql.= 'where '. implode(' and ', $sql_condition);
		}
		if($options['orderby'])
		{
			$sql.= " order by ".$options['orderby']." ";
		}else
		{
			$sql.= " order by id desc ";
		}
		$options['sql']		   = $sql;
		$options['sql_param']	 = $sql_param;
		$options['request_param'] = $request_param;
		$options['per_page']	  = $options['per_page']?$options['per_page']:20;
		$list = Pager::render($options);

		if($export)
		{

		}else
		{

		}
		return $list;
	}/*}}}*/

	public function getAlladmin()
	{
		$sql = "select * from ".self::getTableName()."";
		$data = $this->getExecutor()->querys( $sql);
		return $data;
	}

	public function getById($id)
	{
		$sql = "select * from ".self::getTableName()." where id=?";
		return  $this->getExecutor()->querys($sql,array($id));

	}
	public function getUidByAuth($aid)
	{

		$sql = "select uid,user from userauth where aid=?";
		return  $this->getExecutor()->querys($sql,array($aid));
	}

	public function forbiddenAccount($id)
	{
		$sql = "update ".self::getTableName()." set status = ? where id= ?";
		return  $this->getExecutor()->exeNoquery($sql,array(2,$id));
	}
	public function addAuth($param)
	{
		//$length = sizeof($param['grade']);

		if(!empty($param['grade']))
		{
			foreach($param['grade'] as $v)
			{
				if(!empty($v))
				{
					$sql  = "insert into gradelist (id,uid,gid,gname,status) values(?,?,?,?,?)ON DUPLICATE KEY UPDATE uid=".$param['admin'].",gid=".$v[0]."";
					$id = LoaderSvc::loadIdGenter()->create('gradelist');
					echo $sql;die();
					$res = $this->getExecutor()->exeNoQuery($sql,array($id,$param['admin'],$v[0],$v[1],1));
				}
			}
		}else
		{
			return null;
		}

	}

	public function getGidByUid($uid)
	{
		$sql = "select gid from gradelist where uid=?";
		return  $this->getExecutor()->querys($sql,array($uid));
	}

	public function checkName($name)
	{
		$sql = "select ename from ".self::getTableName()." where ename=?";
		return  $this->getExecutor()->querys($sql,array($name));
	}

	public function getUidByEname($name)
	{
		$sql = "select id from ".self::getTableName()." where ename=?";
		return  $this->getExecutor()->query($sql,array($name));
	}

	public function delauth($param)
	{
		$sql = "delete from userauth where aid=? and uid=?";
		return $this->getExecutor()->exeNoquery($sql,$param);
	}
	public function getGradelistByUid($uid=0)
	{
		if($uid==0)
		{
			return array();
		}
		$sql = "select * from gradelist where uid=?";
		return $this->getExecutor()->querys($sql,array($uid));
	}

	public function getAuthByUid($uid)
	{
		$sql = "select name,uid,aid from auths left join userauth on auths.id=userauth.aid where userauth.uid=?";
		if($uid==0)
		{
			return array();
		}
		return $this->getExecutor()->querys($sql,array($uid));
	}
	public function getAdminByuid($uid=0)
	{
		if($uid==0)
		{
			return array();
		}
		$sql = "select * from  adminuser where id=?";
		return $this->getExecutor()->querys($sql,array($uid));
	}
	public function getTableName()
    {/*{{{*/
		return 'adminuser';
	}/*}}}*/

}/*{{{*/
?>