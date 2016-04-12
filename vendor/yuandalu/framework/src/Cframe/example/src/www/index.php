<?php
require_once 'QFrame/Loader.php';
require_once 'auto_load.php';
$webApp = QFrame::createWebApp();
$webApp->throwException(QFrameConfig::getConfig('EXCEPTION'));
//自定义ControllerPath
//$webApp->setControllerPath('/home/chenchao/project/fw/example/src/application/controllers/');
//$webApp->setViewPath('/home/chenchao/project/test/src/application/testview/scripts/');

/*
 * 自定义路由规则
 *
  $userroute = new QFrameStandRoute(
       'u/:qid',
       array(
              'controller' => 'my',
              'action'     => 'index',
       )
);
QFrameContainer::find('QFrameRouter')->addRoute('user',$userroute);
 */
$webApp->run();
?>
