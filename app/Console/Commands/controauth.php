<?php

require_once "../header.php";

$arr = glob(BASE_DIR."/app/Controllers/Admin/*.php", GLOB_NOCHECK);//获取所有controller
$auth = array();
foreach ($arr as $value) {
    $controller = str_replace("Controller", "", basename($value, '.php'));//controller名字
    $content = file_get_contents($value);
    preg_match_all("|function?\s*(.*)Action|iu", $content, $brr);//匹配Action
    foreach ($brr[1] as $val) {
        if ($controller!='' && $val!='') {
            $auth[] = $controller."_".$val;
        }
    }
}

$data = \App\Models\Svc\AdmAuthNodeSvc::getAll();//已经有的数据

$result = array_diff($auth, $data);

print_r($data);
if (!empty($result)) {
    foreach ($result as $value) {
        $re = explode("_", $value);
        $param = array();
        $param['contr']  = $re[0];
        $param['action'] = $re[1];
        $param['aid']    = 0;
        \App\Models\Svc\AdmAuthNodeSvc::add($param);
    }
}
