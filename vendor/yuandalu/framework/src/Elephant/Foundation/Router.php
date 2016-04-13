<?php

namespace Elephant\Foundation;

use Elephant\Container\Factory;

class Router
{
    protected $_routers = array();
    protected $_useDefaltRoute = true;

    public function getRouter()
    {
        return $this->_routers;
    }

    public function addRoute($name,$router)
    {
        $this->_routers[$name] = $router;
    }

    public function route($requestUri)
    {
        if ($this->_useDefaltRoute) {
            $this->addDefaultRoutes();
        }
        foreach (array_reverse($this->_routers) as $name => $route) {
            if ($params = $route->match($requestUri)) {
                Factory::find('Elephant\Base\Request')->setParams($params);
                Factory::find('Elephant\Foundation\Application')->set('curController',$params['controller']);
                Factory::find('Elephant\Foundation\Application')->set('curAction',$params['action']);
                break;
            }
        }
    }

    protected function addDefaultRoutes()
    {
        $handle = Container::find('Elephant\Foundation\DefaultRoute');
        $this->_routers = array_merge(array('default'=>$handle),$this->_routers);
    }

}