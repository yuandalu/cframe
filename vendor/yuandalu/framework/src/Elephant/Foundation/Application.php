<?php

namespace Elephant\Foundation;

use Elephant\Base\Ensure;
use Elephant\Container\Factory;
use Elephant\Base\RunException;

class Application
{
    const VERSION = '0.1.0';

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

    public function __construct($basePath = null)
    {
        if ($basePath) {
            $this->setBasePath($basePath);
        }
    }

    public function __set($key,$value)
    {
        self::${$key} = $value;
    }

    public function set($key,$value)
    {
        Ensure::ensureNotFalse(isset(self::${$key}),"$key is not an valid attr");
        $this->__set($key,$value);
    }

    public function run()
    {
        try {
            $this->processRequest();
        } catch(RunException $e) {
            $this->processException($e);
        }
    }

    public function throwException($flag = null)
    {
        if (false === $flag) {
            $this->_throwException = false;
        } elseif (true === $flag) {
            $this->_throwException = true;
        }
        return $this->_throwException;
    }
    
    public function getNameSpace()
    {
        if (NULL !== $this->_nameSpace) {
            return $this->_nameSpace;
        } else {
            return '';
        }
    }

    public function getControllerPath()
    {
        if (NULL !== $this->_controllerPath) {
            return $this->_controllerPath;
        } else {
            return $this->_controllerPath=$this->getBasePath().DIRECTORY_SEPARATOR.'app/controllers';
        }
    }

    public function setNameSpace($nameSpace)
    {
        $this->_nameSpace = $nameSpace;
    }

    public function setControllerPath($path)
    {
        $this->_controllerPath = $path;
    }

    public function setViewPath($path)
    {
        $this->_viewPath = $path;
    }

    public function getViewPath()
    {
        if(NULL !== $this->_viewPath)
        {
            return $this->_viewPath;
        }else
        {
            return dirname($this->getControllerPath())."/views/scripts/";
        }
    }

    public function getBasePath()
    {
        return $this->_basePath;
    }

    public function setBasePath($path)
    {
        $this->_basePath = rtrim($path, '\/');
    }

    public function setControllerName($name)
    {
        self::$curController = $name;
        return $this;
    }

    public function setActionName($name)
    {
        self::$curAction = $name;
        return $this;
    }

    protected function processRequest()
    {
        $pathInfo   = Factory::find('Elephant\Base\Request')->getPathInfo();
        $controller = $this->runController($pathInfo);
        if(!Factory::find('Elephant\Foundation\View')->isControllerRender())
        {
            Factory::find('Elephant\Foundation\View')->renderView();
        }
        echo $this->_dispatchBuf;
    }

    protected function runController($pathInfo)
    {
        if (trim($pathInfo,'/') === '') {
            if ('' === self::$curController) self::$curController = $this->_defaultController;
            if ('' === self::$curAction)     self::$curAction     = $this->_defaultAction;
        } else {
            if( empty(self::$curController) || empty(self::$curAction) )
            {
                $route = Factory::find('Elephant\Foundation\Router')->route($pathInfo);
            }
        }
        $this->dispatch();
    }

    public function setDispatched($flag)
    {
        $this->_dispatched = $flag;
    }

    public function isDispatched()
    {
        return $this->_dispatched;
    }

    public function dispatch()
    {
        Ensure::ensureNotFalse(!$this->isDispatched(),"Already Dispatched!!\n");
        $className = $this->createControllerClassName(self::$curController);
        $classFile = $this->getControllerPath().DIRECTORY_SEPARATOR.$className.'.php';
        Ensure::ensureNotFalse(is_file($classFile),"Controller File('$classFile') is Not Exist!!!\n");
        //$controller= new $className(); /* avoid '_forward' method repeat exec init function in the same controller*/
        $controller = Factory::find($this->createControllerClassName(self::$curController, true));
        Ensure::ensureNotFalse($controller instanceof Controller,"Controller $className is not an instance of Controller");
        $action = $this->createActionName(self::$curAction);
        Ensure::ensureNotFalse(method_exists($controller,$action),"Action `$action` is Not Exist!!!\n");
        $this->setDispatched(true);
        ob_start();
        $controller->dispatch($action);
        $this->_dispatchBuf .= ob_get_clean();
    }

    private function createControllerClassName($controllID, $nameSpace = false)
    {
        $controller = ucfirst($controllID)."Controller";
        if ($nameSpace) {
            return $this->getNameSpace().'\\'.$controller;
        }
        return $controller;
    }

    private function createActionName($actionID)
    {
        return strtolower($actionID)."Action";
    }

    protected function processException($e)
    {
        if($this->throwException())
            throw $e;
        Factory::find('Elephant\Base\Exception')->setException($e);
    }

}