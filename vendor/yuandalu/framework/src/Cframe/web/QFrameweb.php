<?php
class QFrameWeb
{
    protected $_defaultController = "index";
    protected $_defaultAction     = "index";
    protected $_nameSpace         = NULL;
    protected $_controllerPath    = NULL;
    protected $_viewPath          = NULL;
    protected $_basePath          = NULL;
    public static $curController  = '';
    public static $curAction      = ''; 

    protected $_throwException    = false;
    protected $_dispatched        = false;
    protected $_dispatchBuf       = '';
        
    public function __set($key,$value)
    {/*{{{*/
        self::${$key} = $value;
    }/*}}}*/

    public function set($key,$value)
    {/*{{{*/
        QFrameBizResult::ensureNotFalse(isset(self::${$key}),"$key is not an valid attr");    
        $this->__set($key,$value);
    }/*}}}*/

    public function run()
    {/*{{{*/
        try
        {
            $this->processRequest();
        }catch(QFrameRunException $e)
        {
            $this->processException($e);
        }
    }/*}}}*/

    public function throwException($flag=null)
    {/*{{{*/
        if(false === $flag)
        {
            $this->_throwException = false;
        }elseif(true === $flag)
        {
            $this->_throwException = true;
        }
        return $this->_throwException;
    }/*}}}*/
    
    public function getNameSpace()
    {/*{{{*/
        if(NULL !== $this->_nameSpace)
        {
            return $this->_nameSpace;
        }else
        {
            return '';
        }
    }/*}}}*/
    
    public function getControllerPath()
    {/*{{{*/
        if(NULL !== $this->_controllerPath)
        {
            return $this->_controllerPath;
        }else
        {
            return $this->_controllerPath=$this->getBasePath().DIRECTORY_SEPARATOR.'application/controllers';
        }
    }/*}}}*/

    public function setNameSpace($nameSpace)
    {/*{{{*/
        $this->_nameSpace = $nameSpace;
    }/*}}}*/

    public function setControllerPath($path)
    {/*{{{*/
        $this->_controllerPath = $path;
    }/*}}}*/

    public function setViewPath($path)
    {/*{{{*/
        $this->_viewPath = $path;
    }/*}}}*/

    public function getViewPath()
    {/*{{{*/
        if(NULL !== $this->_viewPath)
        {
            return $this->_viewPath;
        }else
        {
            return dirname($this->getControllerPath())."/views/scripts/";
        }
    }/*}}}*/

    public function getBasePath()
    {/*{{{*/
        if($this->_basePath == NULL)
        {
            $this->_basePath = realpath("../"); 
        }
        return $this->_basePath;
    }/*}}}*/

    public function setBasePath($path)
    {/*{{{*/
        $this->_basePath = $path;     
    }/*}}}*/

    public function setControllerName($name)
    {/*{{{*/
        self::$curController = $name;
        return $this;
    }/*}}}*/

    public function setActionName($name)
    {/*{{{*/
        self::$curAction = $name;
        return $this;
    }/*}}}*/

    public function setDefaultControllerName($name)
    {/*{{{*/
        $this->_defaultController = $name;
        return $this;
    }/*}}}*/

    public function setDefaultActionName($name)
    {/*{{{*/
        $this->_defaultAction = $name;
        return $this;
    }/*}}}*/

    protected function processRequest()
    {/*{{{*/
        $pathInfo   = QFrameContainer::find('QFrameHttp')->getPathInfo();
        $controller = $this->runController($pathInfo);
        if(!QFrameContainer::find('QFrameView')->isControllerRender())
        {
            QFrameContainer::find('QFrameView')->renderView();
        }
        echo $this->_dispatchBuf;
    }/*}}}*/

    protected function runController($pathInfo)
    {/*{{{*/
        if(trim($pathInfo,'/') === '')
        {
            if('' === self::$curController) self::$curController = $this->_defaultController;
            if('' === self::$curAction)     self::$curAction     = $this->_defaultAction;
        }else
        {
            if( empty(self::$curController) || empty(self::$curAction) )
            {
                $route = QFrameContainer::find('QFrameRouter')->route($pathInfo);
            }
        }
        $this->dispatch(); 
    }/*}}}*/

    public function setDispatched($flag)
    {/*{{{*/
        $this->_dispatched = $flag;
    }/*}}}*/

    public function isDispatched()
    {/*{{{*/
        return $this->_dispatched;
    }/*}}}*/

    public function dispatch()
    {/*{{{*/
        QFrameBizResult::ensureNotFalse(!$this->isDispatched(),"Already Dispatched!!\n");
        $className = $this->createControllerClassName(self::$curController);
        $classFile = $this->getControllerPath().DIRECTORY_SEPARATOR.$className.'.php';
        QFrameBizResult::ensureNotFalse(is_file($classFile),"Controller File('$classFile') is Not Exist!!!\n");
        //$controller= new $className(); /* avoid '_forward' method repeat exec init function in the same controller*/
        $controller = QFrameContainer::find($this->createControllerClassName(self::$curController, true));
        QFrameBizResult::ensureNotFalse($controller instanceof QFrameAction,"Controller $className is not an instance of QFrameAction");
        $action    = $this->createActionName(self::$curAction);
        QFrameBizResult::ensureNotFalse(method_exists($controller,$action),"Action `$action` is Not Exist!!!\n");
        $this->setDispatched(true);
        ob_start();
        $controller->dispatch($action);
        $this->_dispatchBuf .= ob_get_clean();
    }/*}}}*/

    private function createControllerClassName($controllID, $nameSpace = false)
    {/*{{{*/
        $controller = ucfirst($controllID)."Controller";
        if ($nameSpace) {
            return $this->getNameSpace().'\\'.$controller;
        }
        return $controller;
    }/*}}}*/

    private function createActionName($actionID)
    {/*{{{*/
        return strtolower($actionID)."Action";
    }/*}}}*/

    protected function processException($e)
    {/*{{{*/
        if($this->throwException())
            throw $e;
        QFrameContainer::find('QFrameException')->setException($e);
        echo $this->_dispatchBuf;
    }/*}}}*/

}
?>
