<?php

namespace Elephant\Base;

class Exception
{
    protected $_errorController = "error";
    protected $_errorAction     = "error";

    public function __construct()
    {
    }

    public function setException($e)
    {
        $error              = new ArrayObject(array(),ArrayObject::ARRAY_AS_PROPS);
        $exceptionType      = get_class($e);
        $error->exception   = $e;
        $error->type        = $exceptionType;
        QFrameContainer::find('QFrameHttp')->setParam('error_handle',$error);
        QFrameContainer::find('QFrameWeb')->setDispatched(false);
        QFrameContainer::find('QFrameWeb')->setControllerName($this->getErrorControllerName())
                                          ->setActionName($this->getErrorActionName())
                                          ->dispatch();
        QFrameContainer::find('QFrameView')->renderView();
    }

    public function setErrorController($name)
    {
        $this->_errorController = $name;
        return $this;
    }

    public function setErrorAction($name)
    {
        $this->_errorAction = $name;
        return $this;
    }

    public function getErrorControllerName()
    {
        return $this->_errorController;
    }

    public function getErrorActionName()
    {
        return $this->_errorAction;
    }
}