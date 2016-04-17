<?php

namespace App\Models\Svc;

class EntitySvc
{
    public static function createEntityFile($f_type,$f_name,$f_description,$f_attr,$f_default,$entity,$table_name,$id_genter_start)
    {
        $entity_file = "<?php\n\nnamespace App\Models\Entity;\n\nuse App\Support\Loader;\nuse App\Support\Entity;\n\nclass ".$entity." extends Entity\n{\n    const ID_OBJ  = '{$table_name}';\n";
        foreach ($f_default as $k => $v) {
            $oparr = explode('|', $v);
            if ($oparr[0] === '@AUTO') {
                $entity_file.= "\n    // {$f_description[$k]}";
                foreach ($oparr as $value) {
                    if ($value === '@AUTO') {
                        continue;
                    }
                    list($op_n, $op_v, $op_w) = explode(":", $value);
                    $entity_file.= "\n    const ".strtoupper($f_name[$k].'_'.$op_n)." = '{$op_v}';";
                }
                $entity_file.="\n    public static \$".strtoupper($f_name[$k])." = array(";
                foreach ($oparr as $value) {
                    if ($value === '@AUTO') {
                        continue;
                    }
                    list($op_n, $op_v, $op_w) = explode(":", $value);
                    $entity_file.="\n        self::".strtoupper($f_name[$k].'_'.$op_n)." => array('name' => '{$op_w}'),";
                }
                $entity_file.="\n    );\n";
            }
        }
        $entity_file.= "\n    public static function createByBiz(\$param)\n";
        $entity_file.="    {\n";
        $entity_file.="        \$cls = __CLASS__;\n";
        $entity_file.="        \$obj = new \$cls();";
        foreach($f_type as $k=>$type)
        {
            $name = $f_name[$k];
            switch($type)
            {
                case "id_genter":
                    $entity_file.="\n        \$obj->id = Loader::loadIdGenter()->create(self::ID_OBJ);";
                    break;
                case "ctime":
                case "utime":
                    $entity_file.="\n        \$obj->$name = date('Y-m-d H:i:s');";
                    break;
                default:
                    $default_value = "'{$f_default[$k]}'";
                    $oparr = explode('|', $f_default[$k]);
                    if ($oparr[0] === '@TABLE') {
                        $default_value = "'0'";
                    }
                    if ($oparr[0] === '@AUTO') {
                        list($op_n, $op_v, $op_w) = explode(":", $oparr[1]);
                        $default_value = "self::".strtoupper($f_name[$k].'_'.$op_n);
                    }
                    $entity_file.="\n        \$obj->$name = \$param['$name']?\$param['$name']:{$default_value};";
            }

        }
        $entity_file .= "\n        return \$obj;\n";
        $entity_file .= "\n    }\n}\n";
        return $entity_file;
    }

    public static function createSQLFile($f_type,$f_name,$f_description,$f_attr,$f_default,$entity,$table_name,$id_genter_start,$table_annotation)
    {
        $create_sql="SET NAMES UTF8;\n";
        $create_sql.="INSERT INTO sys_idgenter(obj,id,step)VALUES('".$table_name."',".$id_genter_start.",1);\n";
        $create_sql.= "DROP TABLE IF EXISTS $table_name;\ncreate table `$table_name` (\n";
        foreach($f_type as $k=>$type)
        {
            $name = $f_name[$k];
            switch($type)
            {
                case "id_genter":
                    $create_sql.= "`id` int unsigned NOT NULL,\n";
                    break;
                case "ctime":
                case "utime":
                    $create_sql.= "`$name` datetime DEFAULT CURRENT_TIMESTAMP,\n";
                    break;
                case "int unsigned":
                case "int":
                case "float":
                case "tinyint unsigned":
                    $oparr = explode('|', $f_default[$k]);
                    if ($oparr[0] === '@TABLE') {
                        $f_default[$k] = '0';
                    }
                    if ($oparr[0] === '@AUTO') {
                        list($op_n, $op_v, $op_w) = explode(":", $oparr[1]);
                        $f_default[$k] = $op_v;
                    }
                    $create_sql.= "`$name` $type NOT NULL DEFAULT '".$f_default[$k]."' COMMENT '".$f_description[$k]."',\n";
                    break;
                case "datetime":
                case "date":
                case "time":
                    $create_sql.= "`$name` $type DEFAULT CURRENT_TIMESTAMP COMMENT '".$f_description[$k]."',\n";
                    break;
                case "decimal":
                    $create_sql.= "`$name` decimal(".$f_attr[$k].") NOT NULL DEFAULT ".$f_default[$k]." COMMENT '".$f_description[$k]."',\n";
                    break;
                case "char":
                case "varchar":
                    $create_sql.= "`$name` $type(".$f_attr[$k].") NOT NULL DEFAULT '".$f_default[$k]."' COMMENT '".$f_description[$k]."',\n";
                    break;
                case "text":
                case "mediumtext":
                    $create_sql.= "`$name` $type NOT NULL COMMENT '".$f_description[$k]."',\n";
                    break;
                default:
                    break;
            }
        }
        $create_sql.="PRIMARY KEY (`id`)\n";
        $create_sql.=") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='".$table_annotation."';";

        return $create_sql;
    }

    public static function createSvcFile($f_type,$f_name,$f_description,$f_attr,$f_default,$entity,$table_name,$id_genter_start)
    {


        $svc_file = "<?php\n\nnamespace App\Models\Svc;\n\nuse App\Support\Loader;\nuse App\Models\Entity\\".$entity.";\n\n
class";

$svc_file .= " ".$entity."Svc
{
    const OBJ = '".$entity."';

    public static function add(\$param)
    {
        \$obj = ".$entity."::createByBiz(\$param);
        return self::getDao()->add(\$obj);
    }

    public static function updateById(\$id, \$param)
    {
        return self::getDao()->updateById(\$id, \$param);
    }

    public static function getById(\$id = '0')
    {
        return self::getDao()->getById(\$id);
    }

    public static function deleteById(\$id)
    {
        return self::getDao()->deleteById(\$id);
    }

    public static function getAll()
    {
        return self::getDao()->gets();
    }

    private static function getDao()
    {
        return Loader::loadDao(self::OBJ);
    }

    public static function lists(\$request=array(), \$options=array(), \$export = false)
    {
        \$request_param = array();
        \$sql_condition = array();

        if (isset(\$request['id']) && \$request['id'] > 0) {
            \$request_param[] = 'id='.\$request['id'];
            \$sql_condition[] = 'id = ?';
            \$sql_param[]     = \$request['id'];
        }

        if (\$request['startdate'] != '') {
            \$request_param[] = 'startdate='.\$request['startdate'];
            \$sql_condition[] = 'ctime >= ?';
            if ('10' >= strlen(\$request['startdate'])) {
                \$sql_param[] = \$request['startdate'].' 00:00:00';
            } else {
                \$sql_param[] = \$request['startdate'];
            }
        }
        if (\$request['enddate'] != '') {
            \$request_param[] = 'enddate='.\$request['enddate'];
            \$sql_condition[] = 'ctime <= ?';
            if ('10' >= strlen(\$request['enddate'])) {
                \$sql_param[] = \$request['enddate'].' 23:59:59';
            } else {
                \$sql_param[] = \$request['enddate'];
            }
        }

        if (\$options['orderby']) {
            \$request_param[] = 'orderby='.urlencode(\$options['orderby']);
        }
";
        foreach ($f_type as $k=>$type) {
            $name = $f_name[$k];
            switch ($type) {
                case "int unsigned":
                case "int":
                case "float":
                case "decimal":
                case "tinyint unsigned":
                case "char":
                case "varchar":
                case "datetime":
                case "date":
                case "time":
                    $svc_file.="\n        if (\$request['$name']) {\n            \$request_param[] = '$name='.\$request['$name'];\n            \$sql_condition[] = '$name = ?';\n            \$sql_param[]    = \$request['$name'];\n        }";
                    break;
                case "text":
                case "mediumtext":
                    $svc_file.="\n        if (\$request['$name']) {\n            \$request_param[] = '$name='.\$request['$name'];\n            \$sql_condition[] = '$name like ?';\n            \$sql_param[]    = '%'.\$request['$name'].'%';\n        }";
                    break;
                default:
                    break;
            }
        }
        $svc_file.="
        return self::getDao()->getPager(\$request_param, \$sql_condition,\$sql_param , \$options, \$export);
    }

}\n";
        return $svc_file;
    }

    public static function createDaoFile($f_type,$f_name,$f_description,$f_attr,$f_default,$entity,$table_name,$id_genter_start)
    {
        $dao_file = "<?php\n\nnamespace App\Models\Dao;\n\nuse App\Support\Pager;\n\nclass";

         $dao_file .=" ".$entity."Dao extends BaseDao
{
    protected \$table = '".$table_name."';

    public function getByUid(\$uid)
    {
        \$sql = \"select * \";
        \$sql.= \"from \".\$this->table.\" \";
        \$sql.= \"where uid = ? \";
        \$row = \$this->getExecutor()->query(\$sql, array(\$uid));
        if (empty(\$row)) {
            return null;
        }
        return \$row;
    }

    public function getPager(\$request_param, \$sql_condition = array(), \$sql_param = array(), \$options, \$export = false)
    {
        \$sql = \"select * \";
        \$sql.= \"from \".\$this->table.\" \";
        if (!empty(\$sql_condition)) {
            \$sql.= 'where '. implode(' and ', \$sql_condition);
        }
        if (\$options['orderby']) {
            \$sql.= \" order by \".\$options['orderby'].\" \";
        } else {
            \$sql.= \" order by id desc \";
        }
        \$options['sql']           = \$sql;
        \$options['sql_param']     = \$sql_param;
        \$options['request_param'] = \$request_param;
        \$options['per_page']      = \$options['per_page']?\$options['per_page']:20;

        if (!\$export) {
            \$list = Pager::render(\$options);
        } else {
            \$list = \$this->getExecutor()->querys(\$sql, \$sql_param);
            if (empty(\$list)) {
                \$list = array();
            }
        }

        return \$list;
    }

}\n";
        return $dao_file;
    }

    public static function createAdminControllerFile($f_type,$f_name,$f_description,$f_attr,$f_default,$entity,$table_name,$id_genter_start)
    {
        $admin_controller = "<?php\n\nnamespace App\Controllers\Admin;\n\nuse App\Models\Svc\ErrorSvc;\nuse App\Models\Svc\UtlsSvc;\nuse App\Models\Svc\\".$entity."Svc;\n\n
class";
$admin_controller .=" ".$entity."Controller extends BaseController
{
    // 默认分页数
    const PER_PAGE_NUM = 15;

    public function __construct()
    {
        parent::__construct();
    }

    public function indexAction()
    {
        \$id = \$this->getRequest('id','');
        if (\$id > 0) {
            \$".$entity." = ".$entity."Svc::getById(\$id);
            if (is_null(\$".$entity.")) {
                UtlsSvc::showMsg('没有这个ID', '/".$entity."/list');
            }
            \$this->assign(\$".$entity."->toAry());
        }
        \$this->assign('curr_menu', '".$entity."');
        \$this->assign('curr_submenu', '".$entity."_add');
        return view('index');
    }


    public function addAction()
    {
        \$param = array();
        \$id    = \$this->getRequest('id','');
";
        foreach ($f_name as $name) {
            if (in_array($name,array('id','ctime','utime'))) {
                continue;
            }
            $admin_controller.="        \$param['$name'] = \$this->getRequest('$name','');\n";
        }

        $admin_controller.="
        // 参数校验，有时这是必须的
        // if (empty(\$param['name'])) {
        //     return ErrorSvc::format(ErrorSvc::ERR_PARAM_EMPTY, null, '姓名不能为空');
        // }

        if (\$id != '') {
            \$param['utime'] = date('Y-m-d H:i:s');
            \$obj = ".$entity."Svc::updateById(\$id, \$param);
            return ErrorSvc::format(ErrorSvc::ERR_OK, null, '保存成功');
        } else {
            \$obj = ".$entity."Svc::add(\$param);
            return ErrorSvc::format(ErrorSvc::ERR_OK, null, '新增成功');
        }
    }

    public function deleteAction()
    {
        return ErrorSvc::format(
            ErrorSvc::ERR_OK,
            null,
            '请考虑清楚数据是否真的需要删除，是否可以使用状态标识来进行软删除'
        );
    }

    public function listAction()
    {
        \$request = array();
";
        foreach ($f_name as $name) {
            switch ($name) {
                case "ctime":
                    $admin_controller.="        \$request['startdate'] = \$this->getRequest('startdate','');\n";
                    $admin_controller.="        \$request['enddate'] = \$this->getRequest('enddate','');\n";
                    break;

                default:
                    $admin_controller.="        \$request['$name'] = \$this->getRequest('$name','');\n";

            }
        }

        $admin_controller.="        \$orderby  = \$this->getRequest('orderby');
        // 必须校验 orderby 此处没有做预处理

        \$list = ".$entity."Svc::lists(\$request, array(
            'per_page'=>self::PER_PAGE_NUM,
            'page_param'=>'p',
            'curr_page'=>\$this->getRequest('p',1),
            'file_name'=>'/".$entity."/list/',
            'orderby'=>\$orderby
        ));

        \$this->assign(\$request);
        \$this->assign('orderby', \$orderby);
        \$this->assign('list', \$list);
        \$this->assign('curr_menu', '".$entity."');
        \$this->assign('curr_submenu', '".$entity."_list');
        return view('list');
    }

    public function exportAction()
    {
        \$request = array();
";
        foreach ($f_name as $name) {
            switch ($name) {
                case "ctime":
                    $admin_controller.="        \$request['startdate'] = \$this->getRequest('startdate','');\n";
                    $admin_controller.="        \$request['enddate'] = \$this->getRequest('enddate','');\n";
                    break;

                default:
                    $admin_controller.="        \$request['$name'] = \$this->getRequest('$name','');\n";

            }
        }

        $admin_controller.="        \$orderby  = \$this->getRequest('orderby');
        // 必须校验 orderby 此处没有做预处理

        \$list = ".$entity."Svc::lists(\$request, array(
            'per_page'=>self::PER_PAGE_NUM,
            'page_param'=>'p',
            'curr_page'=>\$this->getRequest('p',1),
            'file_name'=>'/".$entity."/list/',
            'orderby'=>\$orderby
        ), true);

        // 表格导出
        \$table = '<table border=\"1\"><tr>
";
        $admin_controller.= "        <th>ID</th><th>创建时间</th><th>修改时间</th>";
        foreach ($f_name as $k => $v) {
            if (in_array($v,array('id','ctime','utime'))) {
                continue;
            }
            $admin_controller.= "<th>{$f_description[$k]}</th>";
        }
        $admin_controller.="
        </tr>';
        foreach (\$list as \$k => \$v) {
            \$table .= '<tr>';
";
        $admin_controller.= "            \$table .= '<th>'.\$v['id'].'</th><th>'.\$v['ctime'].'</th><th>'.\$v['utime'].'</th>";
        foreach ($f_name as $k => $v) {
            if (in_array($v,array('id','ctime','utime'))) {
                continue;
            }
            $admin_controller.= "<th>'.\$v['{$v}'].'</th>";
        }
        $admin_controller.= "';";
        $admin_controller.="
            \$table .= '</tr>';
        }
        \$table .= '</table>';
        echo \$table;
        // CSV导出
";
        $admin_controller.= "        // \$str = \"ID,创建时间,修改时间";
        foreach ($f_name as $k => $v) {
            if (in_array($v,array('id','ctime','utime'))) {
                continue;
            }
            $admin_controller.= ",{$f_description[$k]}";
        }
        $admin_controller.= "\\n\";";
        $admin_controller.="
        // foreach (\$list as \$k => \$v) {
            // \$id = \$v['id'];
            // \$ctime = \$v['ctime'];
            // \$utime = \$v['utime'];
";
        $admin_controller_tmp.= "            // \$str .= \$id.','.\$ctime.','.\$utime";
        foreach ($f_name as $k => $v) {
            if (in_array($v,array('id','ctime','utime'))) {
                continue;
            }
            $admin_controller.= "            // \${$v} = \$v['{$v}'];\n";
            $admin_controller_tmp.= ".','.\${$v}";
        }
        $admin_controller.= $admin_controller_tmp.".\"\\n\";";
        $admin_controller.="
        // }
        // header(\"Content-type:text/csv\");   
        // header(\"Content-Disposition:attachment;filename=\".date('Ymd').'.csv');   
        // header('Cache-Control:must-revalidate,post-check=0,pre-check=0');   
        // header('Expires:0');   
        // header('Pragma:public');  
        // echo \$str;
        exit;
    }

}\n";
        return $admin_controller;
    }
    public static function createAdminListFile($f_type,$f_name,$f_description,$f_attr,$f_default,$entity,$table_name,$id_genter_start,$table_annotation)
    {
        $admin_list_file = '<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>后台管理-'.$table_annotation.'</title>
        <?php $this->render(\'include/headersource\', true); ?>
        <!-- 此处引入资源文件或自定义样式及脚本 -->
        <style>
            #list.table th, #list.table td, #search th, #search td { 
                text-align: center;
                vertical-align: middle; 
            }
        </style>
    </head>
    <body class="hold-transition skin-blue sidebar-mini">
        <div class="wrapper">
            <?php $this->render(\'include/header\', true); ?>
            <?php $this->render(\'include/leftbar\', true); ?>
            <div class="content-wrapper">
                <section class="content-header">
                    <h1>'.$table_annotation.'<small>List</small></h1>
                    <ol class="breadcrumb">
                        <li>
                            <a href="/">
                            <i class="fa fa-dashboard"></i>
                                后台管理
                            </a>
                        </li>
                        <li class="active">
                            '.$table_annotation.'列表
                        </li>
                    </ol>
                </section>
                <section class="content">
                <!-- 此处是主内容区域 Start -->
                <!-- search -->
                <div class="row">
                <div class="col-md-12">
                <div class="box box-warning">
                <div class="box-header with-border">
                    <h3 class="box-title">Search</h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <div class="box-body table-responsive" style="display: block;">
                <form id="searchform" method="post" action="/'.$entity.'/list/">
                <table id="search" class="table">
                    <tr>
                        <td width="80px">ID</td>
                        <td width="150px"><input type="text" name="id" value="<?php echo $this->id; ?>" class="form-control" placeholder="ID"></td>
                        <td width="80px">起始时间</td>
                        <td>
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input type="date" name="startdate" value="<?php echo $this->startdate; ?>" class="form-control">
                            </div>
                        </td>
                        <td width="80px">结束时间</td>
                        <td>
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input type="date" name="enddate" value="<?php echo $this->enddate; ?>" class="form-control">
                            </div>
                        </td>
                        <td><input type="submit" class="btn btn-block btn-default" value="查询"></td>
                        <td><a class="btn btn-block btn-info" onclick="exportTable();return false;">导出</a></td>
                    </tr>
                    <!-- 请自己按照数量调整好 colspan 使表格看起来更协调 -->';
                foreach ($f_name as $k=>$name) {
                    switch ($name) {
                        case "id":
                        case "ctime":
                        case "utime":
                            break;
                        default:
                        if (in_array($k, array(3,6,9,12,15,18,21,24,27,30,33,36,39,42,45,48,51,54,57,60,63,69,72,75,78,81,84,87,90))) {
                            $admin_list_file .= '
                    <tr>';
                        }

                            $oparr = explode('|', $f_default[$k]);
                            if ($oparr[0] === '@AUTO') {
                                $admin_list_file.= '
                        <td>'.$f_description[$k].'</td>
                        <td>
                            <select class="form-control" name="'.$name.'">
                            <option value="">All</option>
                            <?php foreach (\App\Models\Entity\\'.$entity.'::$'.strtoupper($f_name[$k]).' as $k => $v): ?>
                                <?php if ($k == $this->'.$f_name[$k].') {
                                    echo \'<option value="\'.$k.\'" selected>\'.$v[\'name\'].\'-\'.$k.\'</option>\';
                                } else {
                                    echo \'<option value="\'.$k.\'">\'.$v[\'name\'].\'-\'.$k.\'</option>\';
                                } ?>
                            <?php endforeach ?>
                            </select>
                        </td>';
                            } else if ($oparr[0] === '@TABLE') {
                                $admin_list_file.= '
                        <td>'.$f_description[$k].'</td>
                        <td>
                            <select class="form-control" name="'.$name.'">
                            <option value="">All</option>
                            <?php foreach (\App\Models\Svc\\'.$oparr[1].'Svc::getAll() as $k => $v): ?>
                                <?php if ($v[\''.$oparr[2].'\'] == $this->'.$f_name[$k].') {
                                    echo \'<option value="\'.$v[\''.$oparr[2].'\'].\'" selected>\'.$v[\''.$oparr[2].'\'].\'-\'.$v[\''.$oparr[3].'\'].\'</option>\';
                                } else {
                                    echo \'<option value="\'.$v[\''.$oparr[2].'\'].\'">\'.$v[\''.$oparr[2].'\'].\'-\'.$v[\''.$oparr[3].'\'].\'</option>\';
                                } ?>
                            <?php endforeach ?>
                            </select>
                        </td>';
                            } else {
                                $admin_list_file .= '
                        <td>'.$f_description[$k].'</td>
                        <td><input type="text" name="'.$name.'" value="<?php echo $this->'.$name.'; ?>" class="form-control" placeholder="查询的'.$f_description[$k].'"></td>';
                            }

                        if (($k + 1) === count($f_name) || in_array($k, array(5,8,11,14,17,20,23,26,29,32,35,38,41,44,47,50,53,56,59,62,65,68,71,74,77,80,83,86,89,92))) {
                            $admin_list_file .= '
                    </tr>';
                        }
                    }
                }
                $admin_list_file.='
                </table>
                </form>
                </div>
                </div>
                </div>
                </div>
                <!-- list -->
                <div class="row">
                <div class="col-md-12">
                <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Table List</h3>
                    <div class="pull-right">
                        <div class="btn-group">
                            <a href="#" class="btn btn-sm btn-default">可</a>
                            <a href="#" class="btn btn-sm btn-default">隐</a>
                            <span class="btn btn-sm btn-default active">藏</span>
                        </div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <span class="glyphicon glyphicon-th-list"></span>
                        <a class="glyphicon glyphicon-plus" href="/'.$entity.'/index"></a>
                    </div>
                </div>
                <div class="box-body table-responsive">
                <table id="list" class="table table-bordered table-condensed table-striped table-hover">
                <tbody>
                    <tr>
                        <th width="10px">#</th>
                        <th>创建时间</th>
                        <th>修改时间</th>';
                foreach ($f_name as $k=>$name) {
                    if (in_array($k, array(0,1,2))) {
                        continue;
                    }
                    $admin_list_file.="
                        <th>".$f_description[$k]."</th>";
                }
                $admin_list_file.='
                        <th>操作</th>
                    </tr>
                    <?php foreach ($this->list[\'records\'] as $v) {
                    echo "<tr>";';
                    foreach ($f_name as $name) {
                        $admin_list_file.="
                        echo '<td>'.\$v['".$name."'].'</td>';";
                    }
                    $admin_list_file.='
                        echo \'<td><a class="glyphicon glyphicon-edit" href="/'.$entity.'/index?id=\'.$v[\'id\'].\'"></a>&nbsp;<a  class="glyphicon glyphicon-trash" href="javascript:void(0);" onclick="del(\'.$v[\'id\'].\');return false;"></a></td>\';
                    echo \'</tr>\';
                    } ?>
                </tbody>
                </table>
                </div>
                <div class="box-footer clearfix">
                    <ul class="pagination pagination-sm no-margin pull-right">
                        <?php $this->render(\'include/pager\', true); ?>
                    </ul>
                </div>
                </div>
                </div>
                </div>
                <!-- 此处是主内容区域 End -->
                </section>
            </div>
            <?php $this->render(\'include/footer\', true); ?>
            <?php $this->render(\'include/rightbar\', true); ?>
        </div>
        <?php $this->render(\'include/footersource\', true); ?>
        <!-- JavaScript引用和代码请集中写入此处 Start -->
        <script type="text/javascript">
        function del(id) {
            $.confirm({
                title: \'警告!\',
                content: \'是否删除\',
                confirm: function() {
                    window.location.href = \'/'.$entity.'/delete?id=\'+id;
                }
            });
        }
        function exportTable()
        {
            var act = $("#searchform").attr(\'action\');
            $("#searchform").attr(\'action\', act.replace(\'list\', \'export\'));
            $("#searchform").submit();
        }
        </script>
        <!-- JavaScript引用和代码请集中写入此处 End -->
    </body>
</html>
';
        return $admin_list_file;
    }
 
    public static function createAdminIndexFile($f_type,$f_name,$f_description,$f_attr,$f_default,$entity,$table_name,$id_genter_start,$table_annotation)
    {
        $admin_index_file = '<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>后台管理-'.$table_annotation.'</title>
        <?php $this->render(\'include/headersource\', true); ?>
        <!-- 此处引入资源文件或自定义样式及脚本 -->
    </head>
    <body class="hold-transition skin-blue sidebar-mini">
        <div class="wrapper">
            <?php $this->render(\'include/header\', true); ?>
            <?php $this->render(\'include/leftbar\', true); ?>
            <div class="content-wrapper">
                <section class="content-header">
                    <h1>'.$table_annotation.'<small><?php echo $this->id?\'Edit\':\'Add\';?></small></h1>
                    <ol class="breadcrumb">
                        <li>
                            <a href="/">
                            <i class="fa fa-dashboard"></i>
                                后台管理
                            </a>
                        </li>
                        <li class="active">
                            '.$table_annotation.'
                        </li>
                    </ol>
                </section>
                <section class="content">
                <!-- 此处是主内容区域 Start -->
                <div class="row">
                <div class="col-md-12">
                <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"></h3>
                    <div class="pull-right">
                        <?php if ($this->id): ?>
                        <span class="glyphicon glyphicon-edit"></span>
                        <a class="glyphicon glyphicon-plus" href="/'.$entity.'/index"></a>
                        <a class="glyphicon glyphicon-th-list" href="/'.$entity.'/list"></a>
                        <?php else: ?>
                        <span class="glyphicon glyphicon-plus"></span>
                        <a class="glyphicon glyphicon-th-list" href="/'.$entity.'/list"></a>
                        <?php endif ?>
                    </div>
                </div>
                <form class="form-horizontal" method="post" action="/'.$entity.'/add/" name="myform" id="myform">
                    <div class="box-body">';
                foreach ($f_type as $k=>$type) {
                    switch ($type) {
                        case "id_genter":
                        case "ctime":
                        case "utime":
                            break;
                        default:
                            $oparr = explode('|', $f_default[$k]);
                            if ($oparr[0] === '@AUTO') {
                                $admin_index_file.= '
                        <div class="form-group">
                            <label class="col-sm-2 control-label">'.$f_description[$k].'</label>
                            <div class="col-sm-10">
                                <select class="form-control" style="width:200px;"  name="'.$f_name[$k].'">
                                <?php foreach (\App\Models\Entity\\'.$entity.'::$'.strtoupper($f_name[$k]).' as $k => $v): ?>
                                    <?php if ($k == $this->'.$f_name[$k].') {
                                        echo \'<option value="\'.$k.\'" selected>\'.$v[\'name\'].\'-\'.$k.\'</option>\';
                                    } else {
                                        echo \'<option value="\'.$k.\'">\'.$v[\'name\'].\'-\'.$k.\'</option>\';
                                    } ?>
                                <?php endforeach ?>
                                </select>
                            </div>
                        </div>';
                            } else if ($oparr[0] === '@TABLE') {
                                $admin_index_file.= '
                        <div class="form-group">
                            <label class="col-sm-2 control-label">'.$f_description[$k].'</label>
                            <div class="col-sm-10">
                                <select class="form-control" style="width:200px;"  name="'.$f_name[$k].'">
                                <?php foreach (\App\Models\Svc\\'.$oparr[1].'Svc::getAll() as $k => $v): ?>
                                    <?php if ($v[\''.$oparr[2].'\'] == $this->'.$f_name[$k].') {
                                        echo \'<option value="\'.$v[\''.$oparr[2].'\'].\'" selected>\'.$v[\''.$oparr[2].'\'].\'-\'.$v[\''.$oparr[3].'\'].\'</option>\';
                                    } else {
                                        echo \'<option value="\'.$v[\''.$oparr[2].'\'].\'">\'.$v[\''.$oparr[2].'\'].\'-\'.$v[\''.$oparr[3].'\'].\'</option>\';
                                    } ?>
                                <?php endforeach ?>
                                </select>
                            </div>
                        </div>';
                            } else {
                            $admin_index_file.= '
                        <div class="form-group">
                            <label class="col-sm-2 control-label">'.$f_description[$k].'</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" style="width:200px;" name="'.$f_name[$k].'" value="<?php echo $this->'.$f_name[$k].'; ?>" placeholder="请输入'.$f_description[$k].'">
                            </div>
                        </div>';
                            }
                            break;
                    }
                }
                $admin_index_file.='
                    </div>
                    <div class="box-footer">
                        <input type="hidden" name="id" value="<?php echo $this->id; ?>">
                        <button type="submit" id="myButton" data-loading-text="Loading..." class="btn btn-primary">确 定</button>
                    </div>
                </form>
                </div>
                </div>
                </div>
                <!-- 此处是主内容区域 End -->
                </section>
            </div>
            <?php $this->render(\'include/footer\', true); ?>
            <?php $this->render(\'include/rightbar\', true); ?>
        </div>
        <?php $this->render(\'include/footersource\', true); ?>
        <!-- JavaScript引用和代码请集中写入此处 Start -->
        <script src="/static/plugins/jquery-form/jquery.form.min.js"></script>
        <script type="text/javascript">
        $(function(){
            var btnSubmit;
            $("#myform").ajaxForm({
                dataType: "json",
                beforeSubmit: function() {
                    btnSubmit = $("#myButton").button(\'loading\');
                },
                success: processJson
            });
            function processJson(data){
                $(\'.alert.alert-warning\').remove();
                if (data.e == "9999") {
                    setTimeout(function(){
                        btnSubmit.button(\'reset\');
                        window.location.href = "<?php echo $_SERVER[\'HTTP_REFERER\'];?>";
                    }, 500);
                } else {
                    $.confirm({
                        title: false,
                        content: data.m,
                        cancelButton: false,
                        confirmButton: false,
                        closeIcon: false,
                    });
                    btnSubmit.button(\'reset\');
                }
            }
        });
        </script>
        <!-- JavaScript引用和代码请集中写入此处 End -->
    </body>
</html>
';
        return $admin_index_file;
    }
    public static function createFrontControllerFile($f_type,$f_name,$f_description,$f_attr,$f_default,$entity,$table_name,$id_genter_start)
    {
        $front_controller = "<?php\n\nnamespace App\Controllers\Front;\n\n
class ".$entity."Controller extends BaseController
{
    // 需要排除验证登录的action名
    static \$NOT_LOGIN_ACTION  = array('');
    // 默认分页数
    const PER_PAGE_NUM = 15;

    public function __construct()
    {
        \$isLogin  = true;

        if (in_array(strtolower(\$this->getActionName()), self::\$NOT_LOGIN_ACTION)) {
            \$isLogin = false;
        }

        parent::__construct(\$isLogin);
    }

    public function addAction()
    {
        \$param = array();
";
        foreach($f_name as $name)
        {
            if (in_array($name,array('id','ctime','utime'))) {
                continue;
            }
            $front_controller.="        \$param['$name'] = \$this->getRequest('$name','');\n";
        }
        $front_controller.="

        // 验证逻辑
        GUMP::set_field_name('type', '类型');// 对应错误提示文字，可不写，默认为字段名
        GUMP::set_field_name('email', 'Email');
        \$is_valid = GUMP::is_valid(\$param, array(
            'type' => 'required|max_len,5|min_len,2',
            'email' => 'required|valid_email'
        ));
        if (\$is_valid !== true) {
            // var_dump(\$is_valid);exit;// 包含详细错误信息
            return ErrorSvc::format(ErrorSvc::ERR_PARAM_INVALID, null, array_shift(\$is_valid));
        }

        // \$obj = ".$entity."Svc::add(\$param);
        if (is_object(\$obj)) {
            return ErrorSvc::format(ErrorSvc::ERR_OK, \$obj->toAry());
        } else {
            return ErrorSvc::format(ErrorSvc::ERR_SYSTEM_ERROR);
        }
    }

    public function updateAction()
    {
        \$param = array();
        \$id = \$this->getRequest('id','');
";
        foreach($f_name as $name)
        {
            if (in_array($name,array('id','ctime','utime'))) {
                continue;
            }
            $front_controller.="        \$param['$name'] = \$this->getRequest('$name','');\n";
        }
        $front_controller.="

        // 验证逻辑
        GUMP::set_field_name('id', 'XXXID');
        GUMP::set_field_name('email', 'Email');
        \$is_valid = GUMP::is_valid(\$param, array(
            'id' => 'required',
            'email' => 'required|valid_email'
        ));
        if (\$is_valid !== true) {
            // var_dump(\$is_valid);exit;
            return ErrorSvc::format(ErrorSvc::ERR_PARAM_INVALID, null, array_shift(\$is_valid));
        }

        \$param['utime'] = date('Y-m-d H:i:s');
        // \$obj = ".$entity."Svc::updateById(\$id, \$param);
        if (\$obj) {
            return ErrorSvc::format(ErrorSvc::ERR_OK);
        } else {
            return ErrorSvc::format(ErrorSvc::ERR_SYSTEM_ERROR);
        }
    }

    public function getAction()
    {
        \$id = \$this->getRequest('id','');

        // 验证逻辑
        GUMP::set_field_name('id', 'XXXID');
        \$is_valid = GUMP::is_valid(\$param, array(
            'id' => 'required'
        ));
        if (\$is_valid !== true) {
            // var_dump(\$is_valid);exit;
            return ErrorSvc::format(ErrorSvc::ERR_PARAM_INVALID, null, array_shift(\$is_valid));
        }

        // \$obj = ".$entity."Svc::getById(\$id);
        if (\$obj) {
            return ErrorSvc::format(ErrorSvc::ERR_OK, \$obj->toAry());
        } else {
            return ErrorSvc::format(ErrorSvc::ERR_NOT_EXISTX);
        }
    }

    public function listAction()
    {
        \$request               = array();
        \$p                     = \$this->getRequest('p',1);
        \$pagenum               = \$this->getRequest('pagenum',self::PER_PAGE_NUM);
        \$orderby               = \$this->getRequest('orderby');
        \$request['id']         = \$this->getRequest('id','');
        \$request['startdate']  = \$this->getRequest('startdate','');
        \$request['enddate']    = \$this->getRequest('enddate','');
        \$request['utime']      = \$this->getRequest('utime','');
";
        foreach($f_name as $name)
        {
            if (in_array($name,array('id','ctime','utime'))) {
                continue;
            }
            $front_controller.="        \$request['$name'] = \$this->getRequest('$name','');\n";
        }
        $front_controller.="

        // 此处必须验证order排序白名单
        if (!empty(\$orderby) && !in_array(\$orderby, array('id desc'))) {
            return ErrorSvc::format(ErrorSvc::ERR_PARAM_EMPTY);
        }

        // \$list = ".$entity."Svc::lists(\$request,array('per_page'=>\$pagenum, 'page_param'=>'p', 'curr_page'=>\$p,'file_name'=>'/".$entity."/list/','orderby'=>\$orderby));
        
        return ErrorSvc::format(ErrorSvc::ERR_OK, \$list);

    }

    public function deleteAction()
    {

    }

}";
        return $front_controller;
    }
}