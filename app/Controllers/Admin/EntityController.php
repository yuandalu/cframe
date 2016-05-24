<?php

namespace App\Controllers\Admin;

use App\Models\Svc\EntitySvc;

class EntityController extends BaseController
{
    const PER_PAGE_NUM = 15;// 默认分页数

    static $NOT_LOGIN_ACTION  = array();// 排除登录验证

    public function __construct()
    {
        $isLogin  = true;
        if (in_array(strtolower($this->getActionName()), self::$NOT_LOGIN_ACTION)) {
            $isLogin = false;
        }
        parent::__construct($isLogin);
    }

    public function indexAction()
    {
        return render('index');
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
        $entity = $this->getRequest('entity');
        $lower_entity = strtolower($entity);
        $table_name = strtolower($this->getRequest('table_name'));

        $admin_index_file = EntitySvc::createAdminIndexFile($f_type,$f_name,$f_description,$f_attr,$f_default,$entity,$table_name,$id_genter_start,$table_annotation);
        $entity_file  = EntitySvc::createEntityFile($f_type,$f_name,$f_description,$f_attr,$f_default,$entity,$table_name,$id_genter_start);

        $create_sql = EntitySvc::createSQLFile($f_type,$f_name,$f_description,$f_attr,$f_default,$entity,$table_name,$id_genter_start,$table_annotation);

        $svc_file = EntitySvc::createSvcFile($f_type,$f_name,$f_description,$f_attr,$f_default,$entity,$table_name,$id_genter_start);

        $dao_file = EntitySvc::createDaoFile($f_type,$f_name,$f_description,$f_attr,$f_default,$entity,$table_name,$id_genter_start);

        $admin_controller = EntitySvc::createAdminControllerFile($f_type,$f_name,$f_description,$f_attr,$f_default,$entity,$table_name,$id_genter_start);

        $admin_list_file = EntitySvc::createAdminListFile($f_type,$f_name,$f_description,$f_attr,$f_default,$entity,$table_name,$id_genter_start,$table_annotation);

        // $front_controller = EntitySvc::createFrontControllerFile($f_type,$f_name,$f_description,$f_attr,$f_default,$entity,$table_name,$id_genter_start);

        file_put_contents('/tmp/'.$entity.'Svc.php',$svc_file);
        file_put_contents('/tmp/'.$entity.'Dao.php',$dao_file);
        file_put_contents('/tmp/'.$entity.'.php',$entity_file);
        file_put_contents('/tmp/'.$table_name.'.sql',$create_sql);
        file_put_contents('/tmp/admin_'.$entity.'Controller.php',$admin_controller);
        // file_put_contents('/tmp/front_'.$entity.'Controller.php',$front_controller);

        file_put_contents('/tmp/'.$lower_entity.'_admin_list.phtml',$admin_list_file);

        file_put_contents('/tmp/'.$lower_entity.'_admin_index.phtml',$admin_index_file);



        $move_file = 'mkdir '.BASE_DIR.'/resources/views/admin/'.$lower_entity."\n";
        $move_file.= 'cp -f /tmp/'.$lower_entity.'_admin_index.phtml  '.BASE_DIR.'/resources/views/admin/'.$lower_entity.'/index.phtml'."\n";
        $move_file.= 'cp -f /tmp/'.$lower_entity.'_admin_list.phtml  '.BASE_DIR.'/resources/views/admin/'.$lower_entity.'/list.phtml'."\n";


        $move_file.= 'cp -f /tmp/admin_'.$entity.'Controller.php  '.BASE_DIR.'/app/Controllers/Admin/'.$entity.'Controller.php'."\n";
        // $move_file.= 'cp -f /tmp/front_'.$entity.'Controller.php  '.BASE_DIR.'/app/Controllers/Front/'.$entity.'Controller.php'."\n";
        $move_file.= 'cp -f /tmp/'.$entity.'Svc.php '.BASE_DIR.'/app/Models/Svc/'.$entity.'Svc.php'."\n";
        $move_file.= 'cp -f /tmp/'.$entity.'Dao.php '.BASE_DIR.'/app/Models/Dao/'.$entity.'Dao.php'."\n";
        $move_file.= 'cp -f /tmp/'.$entity.'.php '.BASE_DIR.'/app/Models/Entity/'.$entity.'.php'."\n";
        $move_file.= 'cp -f /tmp/'.$table_name.'.sql '.BASE_DIR.'/resources/database/'.$table_name.'.sql'."\n";
        file_put_contents('/tmp/'.$entity.'.sh',$move_file);
        system('chmod 777 /tmp/'.$entity.'.sh');
        echo 'succ';
        exit;
    }

    public function ajaxQueryEntityAction()
    {
        $entity = $this->getRequest('entity');
        if(file_exists(BASE_DIR.'/app/Models/Entity/'.$entity.'.php'))
        {
            return json_encode(array('code'=>'fail'));
        }else
        {
            return json_encode(array('code'=>'succ'));
        }
    }

    public function ajaxQueryTableNameAction()
    {
        $table_name = $this->getRequest('table_name');
        if(file_exists(BASE_DIR.'/resources/database/'.$table_name.'.sql'))
        {
            return json_encode(array('code'=>'fail'));
        }else
        {
            return json_encode(array('code'=>'succ'));
        }
    }

}