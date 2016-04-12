<?php
class ErrorController extends QFrameAction
{
    public function errorAction()
    {
        $error = $this->getParam('error_handle');
        $errorResson = $error->exception->getMessage();
        $this->assign('errorMsg',$errorResson);
    }
}
?>
