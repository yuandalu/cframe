<?php
class QFrameRouter
{
    protected $_routers = array();
    protected $_useDefaltRoute = true;

    public function getRouter()
    {/*{{{*/
        return $this->_routers;
    }/*}}}*/

    public function addRoute($name,$router)
    {/*{{{*/
        $this->_routers[$name] = $router;
    }/*}}}*/

    public function route($requestUri)
    {/*{{{*/
        if($this->_useDefaltRoute)
        {
            $this->addDefaultRoutes();
        }
        foreach(array_reverse($this->_routers) as $name => $route)
        {
            if ($params = $route->match($requestUri))
            {
                QFrameContainer::find('QFrameHttp')->setParams($params);
                QFrameContainer::find('QFrameWeb')->set('curController',$params['controller']);
                QFrameContainer::find('QFrameWeb')->set('curAction',$params['action']);
                break;
            }
        }
    }/*}}}*/

    protected function addDefaultRoutes()
    {/*{{{*/
        $handle = QFrameContainer::find('QFrameRouterDefaultRoute');
        $this->_routers = array_merge(array('default'=>$handle),$this->_routers);
    }/*}}}*/

}

class QFrameRouterDefaultRoute
{
    protected $_controllerKey = "controller";
    protected $_actionKey     = "action";
    protected $_moduleKey     = "module";

    protected $_default       =  array('controller'=>'index','action'=>'index');

    const URI_DELIMITER = '/'; 
    
    public function match($pathInfo)
    {/*{{{*/
        $value = $params =  array();
        $pathInfo = trim($pathInfo,self::URI_DELIMITER);
        if($pathInfo != '')
        {
            $path = explode(self::URI_DELIMITER,$pathInfo);
            if(count($path) && !empty($path[0]))
            {
                $value[$this->_controllerKey]  = array_shift($path); 
            }
            if(count($path) && !empty($path[0]))
            {
                $value[$this->_actionKey]      = array_shift($path);
            }
            if ($numSegs = count($path)) 
            {
                for ($i = 0; $i < $numSegs; $i = $i + 2) 
                {
                     $key = urldecode($path[$i]); 
                     $val = isset($path[$i + 1]) ? urldecode($path[$i + 1]) : null;
                     $params[$key] = $val;
                } 
            }
        }
        return $value + $params + $this->_default;
    }/*}}}*/

}

?>
