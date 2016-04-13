<?php

namespace Elephant\Foundation;

use Elephant\Base\Ensure;
use Elephant\Container\Factory;
use Elephant\Foundation\Application;

class View
{
    protected $noViewRender     = false;
    protected $controllerRender = false;
    
    protected $_scriptAction    = NULL;

    protected $_viewScriptPathSpec              = ':controller/:action.:suffix';
    protected $_viewScriptPathNoControllerSpec  = ':action.:suffix';
    protected $_viewSuffix                      = 'phtml';
    protected $_noController                    = false;

    protected $_delimiters                      = array('-','.','_');

    public function __construct()
    {
    }

    public function __isset($key)
    {
        if ('_' != substr($key, 0, 1)) {
            return isset($this->$key);
        }
        return false;
    }

    public function __set($key,$val)
    {
        if ('_' != substr($key, 0, 1)) {
            $this->$key = $val;
            return;
        }
        Ensure::ensureNotFalse(false, "Setting private or protected class members is not allowed");
    }

    public function __get($key)
    {
        if (isset($this->$key)) {
            return $this->$key;
        } else {
            return NULL;
        }
    }

    public function setViewSuffix($suffix = "")
    {
        if ($suffix) {
            $this->_viewSuffix = (string) $suffix;
            return $this;
        }
    }

    public function renderView()
    {
        if ($this->noViewRender) return;
        $this->render();
    }

    public function render($action = null , $noController = NULL)
    {
        echo $this->fetch($action, $noController);
    }
    
    public function fetch($action = null, $noController = NULL)
    {
        if (NULL !== $action) $this->setScriptAction($action);
        if (NULL !== $noController) $this->setNoController($noController);
        $spec = $this->getNoController() ? $this->_viewScriptPathNoControllerSpec : $this->_viewScriptPathSpec;
        $path = $this->getViewScript($spec);
       // $scriptPath = Factory::find('Elephant\Foundation\Application')->getViewPath().$path;
        if (substr($action,0,1) == "/") {
            $scriptPath = $action;
        } else {
            $scriptPath = Factory::find('Elephant\Foundation\Application')->getViewPath().$path;      
        }


        Ensure::ensureNotFalse(is_file($scriptPath),"Script ($scriptPath) Not Exist !!!!!");
        return $this->runView($scriptPath);
    }

    public function renderScript($path)
    {
    }

    public function setNoRender($flag)
    {
        $this->noViewRender = (bool)$flag;
    }
    
    public function isControllerRender()
    {
        return $this->controllerRender;
    }

    public function setControllerRender($flag = true)
    {
        $this->controllerRender = $flag; 
        return $this;
    }
    
    private function runView()
    {
        ob_start();
        include func_get_arg(0);
        return $this->_filter(ob_get_clean());
    }

    //TODO
    private function _filter($content)
    {
        return $content;
    }

    protected function getViewScript($spec)
    {
        $action = self::getScriptAction();
        if(NULL == $action)
        {
            $action = Application::$curAction;
        }
        $controller = Application::$curController;
        $suffix     = $this->getViewSuffix();
        $viewPath   = dirname(Factory::find('Elephant\Foundation\Application')->getControllerPath());
        $replacements = array(
            ':controller' => str_replace($this->_delimiters,'-',strtolower($controller)),
            ':action'     => str_replace($this->_delimiters,'-',strtolower($action)),
            ':suffix'     => $suffix,
        );
        $value = str_replace(array_keys($replacements),array_values($replacements),$spec);
        $value = preg_replace('/-+/', '-', $value);
        return $value;
    }

    private function getScriptAction()
    {
        return $this->_scriptAction;
    }

    private function setScriptAction($name)
    {
        $this->_scriptAction = (string) $name;
    }

    private function getViewSuffix()
    {
        return $this->_viewSuffix;
    }

    
    private function setNoController($flag = true)
    {
        $this->_noController = ($flag) ? true : false;
    }

    private function getNoController()
    {
        return $this->_noController;
    }

}