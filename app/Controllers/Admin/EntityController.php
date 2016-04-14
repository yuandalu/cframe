<?php
class EntityController extends BaseController
{/*{{{*/
	const PER_PAGE_NUM = '20';

	public function __construct()
	{/*{{{*/
		$require_login  = true;
		parent::__construct($require_login);
	}/*}}}*/


	public function indexAction()
	{

	}

	public function indexSubmitAction()
	{

		$f_type = $this->getRequest('f_type');
		$f_name = $this->getRequest('f_name');
		$f_description = $this->getRequest('f_description');
		$f_attr = $this->getRequest('f_attr');
		$f_default = $this->getRequest('f_default');
		//建立entity
		$id_genter_start = $this->getRequest('id_genter_start',1);
		$table_annotation = $this->getRequest('table_annotation');
		$entity = strtolower($this->getRequest('entity'));
		$entity_ucfirst = ucfirst($entity);

		$admin_index_file = EntitySvc::createAdminIndexFile($f_type,$f_name,$f_description,$f_attr,$f_default,$entity,$entity_ucfirst,$id_genter_start,$table_annotation);
		$entity_file  = EntitySvc::createEntityFile($f_type,$f_name,$f_description,$f_attr,$f_default,$entity,$entity_ucfirst,$id_genter_start);

		$create_sql = EntitySvc::createSQLFile($f_type,$f_name,$f_description,$f_attr,$f_default,$entity,$entity_ucfirst,$id_genter_start,$table_annotation);

		$svc_file = EntitySvc::createSvcFile($f_type,$f_name,$f_description,$f_attr,$f_default,$entity,$entity_ucfirst,$id_genter_start);

		$dao_file = EntitySvc::createDaoFile($f_type,$f_name,$f_description,$f_attr,$f_default,$entity,$entity_ucfirst,$id_genter_start);

		$admin_controller = EntitySvc::createAdminControllerFile($f_type,$f_name,$f_description,$f_attr,$f_default,$entity,$entity_ucfirst,$id_genter_start);

		$admin_list_file = EntitySvc::createAdminListFile($f_type,$f_name,$f_description,$f_attr,$f_default,$entity,$entity_ucfirst,$id_genter_start,$table_annotation);

		// $front_controller = EntitySvc::createFrontControllerFile($f_type,$f_name,$f_description,$f_attr,$f_default,$entity,$entity_ucfirst,$id_genter_start);

		file_put_contents('/tmp/'.$entity.'_svc.php',$svc_file);
		file_put_contents('/tmp/'.$entity.'_dao.php',$dao_file);
		file_put_contents('/tmp/'.$entity.'.php',$entity_file);
		file_put_contents('/tmp/'.$entity.'.sql',$create_sql);
		file_put_contents('/tmp/admin_'.$entity_ucfirst.'Controller.php',$admin_controller);
		// file_put_contents('/tmp/front_'.$entity_ucfirst.'Controller.php',$front_controller);

		file_put_contents('/tmp/'.$entity.'_admin_list.phtml',$admin_list_file);

		file_put_contents('/tmp/'.$entity.'_admin_index.phtml',$admin_index_file);



		$move_file = 'mkdir '.$_SERVER['DOCUMENT_ROOT'].'/src/application/views/admin/'.$entity."\n";
		$move_file.= 'cp -f /tmp/'.$entity.'_admin_index.phtml  '.$_SERVER['DOCUMENT_ROOT'].'/src/application/views/admin/'.$entity.'/index.phtml'."\n";
		$move_file.= 'cp -f /tmp/'.$entity.'_admin_list.phtml  '.$_SERVER['DOCUMENT_ROOT'].'/src/application/views/admin/'.$entity.'/list.phtml'."\n";


		$move_file.= 'cp -f /tmp/admin_'.$entity_ucfirst.'Controller.php  '.$_SERVER['DOCUMENT_ROOT'].'/src/application/controllers/admin/'.$entity_ucfirst.'Controller.php'."\n";
		// $move_file.= 'cp -f /tmp/front_'.$entity_ucfirst.'Controller.php  '.$_SERVER['DOCUMENT_ROOT'].'/src/application/controllers/front/'.$entity_ucfirst.'Controller.php'."\n";
		$move_file.= 'cp -f /tmp/'.$entity.'_svc.php '.$_SERVER['DOCUMENT_ROOT'].'/src/application/models/bizservice/'.$entity.'_svc.php'."\n";
		$move_file.= 'cp -f /tmp/'.$entity.'_dao.php '.$_SERVER['DOCUMENT_ROOT'].'/src/application/models/bizdomain/dao/'.$entity.'_dao.php'."\n";
		$move_file.= 'cp -f /tmp/'.$entity.'.php '.$_SERVER['DOCUMENT_ROOT'].'/src/application/models/bizdomain/entity/'.$entity.'.php'."\n";
		$move_file.= 'cp -f /tmp/'.$entity.'.sql '.$_SERVER['DOCUMENT_ROOT'].'/src/database/'.$entity.'.sql'."\n";
		file_put_contents('/tmp/'.$entity.'.sh',$move_file);
		system('chmod 777 /tmp/'.$entity.'.sh');
		echo 'succ';
		exit;
	}

	public function ajaxQueryEntityAction()
	{
		$entity = $this->getRequest('entity');
		if(file_exists(	$_SERVER['DOCUMENT_ROOT'].'/src/application/models/bizdomain/entity/'.$entity.'.php'))
		{
			echo json_encode(array('code'=>'fail'));
			exit;
		}else
		{
			echo json_encode(array('code'=>'succ'));
			exit;
		}
	}

}/*}}}*/
?>