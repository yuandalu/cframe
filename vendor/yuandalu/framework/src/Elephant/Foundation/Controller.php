<?php

namespace Elephant\Foundation;

use Elephant\Base\Ensure;
use Elephant\Container\Factory;
use Elephant\Foundation\Application;

class Controller
{
    public function __construct()
    {
        $this->init();
    }

    /**
     * Dispatch the requested action
     *
     * @param string $action : Method name of action
     * @return void
     */

    public function dispatch($action)
    {
        $this->preDispatch();
        $this->$action();
        $this->postDispatch();
    }

    public function init()
    {
    }

    public function preDispatch()
    {
    }

    public function postDispatch()
    {
    }

    public static function htmlspecialcharsRecursive($value)
    {
        if (is_numeric($value)) {
            return $value;
        }
        if (is_string($value)) {
            return htmlspecialchars($value);
        }
        if (is_array($value)) {
            foreach ($value as $k=>$v) {
                $value[$k] = self::htmlspecialcharsRecursive($v);
            }
            return $value;
        }
        if (is_object($value)) {
            foreach ($value as $k=>$v) {
                $value->$k = self::htmlspecialcharsRecursive($v);
            }
            return $value;
        }
        return $value;
    }

    /**
     * Assigns variables to the view script
     *
     * assign('name',$value) assigns a variable called 'name' with the corresponding $value.
     *
     * assign($array) assigns the array keys as variable names (with the corresponding array values)
     *
     * @see __set()
     * @param string|array
     * @param if assigning a named variable , use this as the value
     */

    public function assign($spec, $value = null, $dohtmlspecialchars = true)
    {
        if (is_string($spec)) {
            Ensure::ensureFalse('_' == substr($spec, 0, 1),"Setting private or protected class members is not allowed");
            if($dohtmlspecialchars)
            {
                $value = self::htmlspecialcharsRecursive($value);
            }
            Factory::find('Elephant\Foundation\View')->$spec = $value;
        } elseif (is_array($spec)) {
            //TODO if(is_array($val))
            foreach ($spec as $key=>$val) {
                Ensure::ensureFalse('_' == substr($key, 0, 1),"Setting private or protected class members is not allowed");
                if (is_string($val)) {
                    Factory::find('Elephant\Foundation\View')->$key = $dohtmlspecialchars ? htmlspecialchars($val) : $val;
                } else {
                    if ($dohtmlspecialchars) {
                        $val = self::htmlspecialcharsRecursive($val);
                    }
                    Factory::find('Elephant\Foundation\View')->$key = $val;
                }
            }
        }
    }

    /**
     * Set the noRender flag , if true, will not autorender views
     * @param boolean $flag
     */

    public function setNoViewRender($flag)
    {
        return Factory::find('Elephant\Foundation\View')->setNoRender($flag);
    }

    /**
     * Get cur Controller Name
     * @return string
     */

    public function getControllerName()
    {
        return Application::$curController;
    }

    /**
     * Gets cur Action Name
     * @return string
     */

    public function getActionName()
    {
        return Application::$curAction;
    }

    /**
     * Gets a parameter from {@link $_request Request object}. If the
     * parameter does not exist , NULL will be returned .
     *
     * @param string $key
     * @param mixed  $default
     * @return mixed
     */

    public function getParam($key, $default = null)
    {
        $value = Factory::find('Elephant\Base\Request')->get($key);

        return (null==$value && null !== $default) ? $default : $value;
    }

    public function getRequest($key = null, $default = null)
    {
        $value = Factory::find('Elephant\Base\Request')->getRequest($key , $default);

        return $value;
    }

    public function getPost($key = null, $default = null)
    {
        $value = Factory::find('Elephant\Base\Request')->getPost($key , $default);

        return $value;
    }

    /**
     * Processes a view script
     *
     * @param string $name: The script script name to process
     *
     */

    public function render($name = null, $noController = false)
    {
        if (is_null($name)) return;
        Factory::find('Elephant\Foundation\View')->setControllerRender(true);
        return Factory::find('Elephant\Foundation\View')->render($name,$noController);
    }

    /**
     * Return a view script
     *
     * @param string $name: The script script name to process
     *
     */

    public function fetch($name = null, $noController = false)
    {
        if (is_null($name)) return;
        Factory::find('Elephant\Foundation\View')->setControllerRender(true);
        return Factory::find('Elephant\Foundation\View')->fetch($name,$noController);
    }
    /**
     * modify the default suffix of view script
     *
     * @param string $suffix: The script suffix name
     *
     */

    public function setViewSuffix($suffix)
    {
        if (empty($suffix)) return false;
        Factory::find('Elephant\Foundation\View')->setViewSuffix($suffix);
    }

    public function _forward($action, $controller = null)
    {
        if (null !== $controller) {
            Factory::find('Elephant\Foundation\Application')->setControllerName($controller);
        }
        Factory::find('Elephant\Foundation\Application')->setActionName($action);
        Factory::find('Elephant\Foundation\Application')->setDispatched(false);
        Factory::find('Elephant\Foundation\Application')->dispatch();
    }

    public function initView()
    {
        $this->view = Factory::find('Elephant\Foundation\View');
    }

}