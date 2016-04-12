<?php
class IndexController extends QFrameAction
{
    public function indexAction()
    {/*{{{*/
        $testInfo = "hello! Install Succ";
        $this->assign('info',$testInfo);

        //如果需要渲染其他的模板，则
        //$this->render('tpl/blank',true);
        //表示渲染tpl目录下的blank.phtml
    }/*}}}*/
}
?>
