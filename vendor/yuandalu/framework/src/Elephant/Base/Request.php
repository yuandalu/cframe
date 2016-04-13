<?php

namespace Elephant\Base;

class Request
{
    protected $_params = array();

    protected $_requestUri = NULL;

    protected $_baseUrl   = NULL;

    public function __construct()
    {
       $this->setRequestUri(); 
    }

    public function __get($key)
    {
        switch (true) {
            case isset($this->_params[$key]):
                 return $this->_params[$key];
            case isset($_GET[$key]):
                 return $_GET[$key];
            case isset($_POST[$key]):
                 return $_POST[$key];
            case isset($_COOKIE[$key]):
                 return $_COOKIE[$key];
            case ($key == 'REQUEST_URI'):
                 return $this->getRequestUri();
            case ($key == 'PATH_INFO'):
                 return $this->getPathInfo();
            case isset($_SERVER[$key]):
                 return $_SERVER[$key];
            case isset($_ENV[$key]):
                 return $_ENV[$key];
            default:
                 return null;
        }
    }

    public function get($key)
    {
        return $this->__get($key);
    }

    public function getRequest($key = null , $default = null)
    {
        if (null === $key) {
            $res = array_merge($this->getPost(null, null) , $this->getQuery(null, null));
            return $res;
        }
        if (null !== ($res = $this->getQuery($key, null))) {
            return $res;
        }
        if (null !== ($res = $this->getPost($key, null))) {
            return $res;
        }

        return $default;
    }

    public function getQuery($key = null, $default = null)
    {
        if (null === $key) {
            return $_GET;
        }

        return (isset($_GET[$key])) ? $_GET[$key] : $default;
    }

    public function getPost($key = null, $default = null)
    {
        if (null === $key) {
            return $_POST;
        }

        return (isset($_POST[$key])) ?  $_POST[$key] : $default;
    }

    public function setRequestUri($requestUri = null)
    {
    
        if ($requestUri === null) {
            if (isset($_SERVER['HTTP_X_REWRITE_URL'])) { // check this first so IIS will catch
                $requestUri = $_SERVER['HTTP_X_REWRITE_URL'];
            } elseif (isset($_SERVER['REQUEST_URI'])) {
                $requestUri = $_SERVER['REQUEST_URI'];
            } elseif (isset($_SERVER['ORIG_PATH_INFO'])) { // IIS 5.0, PHP as CGI
                $requestUri = $_SERVER['ORIG_PATH_INFO'];
                if (!empty($_SERVER['QUERY_STRING'])) {
                    $requestUri .= '?' . $_SERVER['QUERY_STRING'];
                }
            } else {
                return $this;
            }
        } elseif (!is_string($requestUri)) {
            return $this;
        } else {
            // Set GET items, if available
            $_GET = array();
            if (false !== ($pos = strpos($requestUri, '?'))) {
                // Get key => value pairs and set $_GET
                $query = substr($requestUri, $pos + 1);
                parse_str($query, $vars);
                $_GET = $vars;
            }
        }

        $this->_requestUri = $requestUri;
        return $this;
    }

    public function getRequestUri()
    {
        if (empty($this->_requestUri)) {
            $this->setRequestUri();
        }
        return $this->_requestUri;
    }

    public function setParam($key,$value)
    {
        $this->_params[$key] = $value;  
    }

    public function setParams($params)
    {
        if (!empty($params)) {
            foreach($params as $key => $name) {
                $this->_params[$key] = $name;
            }
        }
    }
    
    public function getBaseUrl()
    {
        if (null === $this->_baseUrl) {
            $this->setBaseUrl();
        }
        return $this->_baseUrl;
    }
    
    public function setPathInfo($pathInfo = null)
    {
        if ($pathInfo === null) {
            $baseUrl = $this->getBaseUrl();

            if (null === ($requestUri = $this->getRequestUri())) {
                return $this;
            }

            // Remove the query string from REQUEST_URI
            if ($pos = strpos($requestUri, '?')) {
                $requestUri = substr($requestUri, 0, $pos);
            }

            if ((null !== $baseUrl)) {

                $pathInfo = substr($requestUri,strlen($baseUrl));
                // If substr() returns false then PATH_INFO is set to an empty string
                /*   工作于CGI模式下需要注释以下部分
                if($pathInfo !==false && ($requestUri !== $_SERVER['SCRIPT_NAME']) && ($_SERVER['SCRIPT_NAME'] == $_SERVER['PHP_SELF']))    
                {
                    //add ssi support eg.#include /public/header
                    $pathInfo = $_SERVER['SCRIPT_NAME'];
                }
                */

                if ($pathInfo === false) $pathInfo = '';
            } elseif (null === $baseUrl) {
                $pathInfo = $requestUri;
            }
        }

        $this->_pathInfo = (string) $pathInfo;
        return $this;
    }
    
    public function getPathInfo()
    {
        if (empty($this->_pathInfo)) {
            $this->setPathInfo();
        }

        return $this->_pathInfo;
    }

    public function setBaseUrl($baseUrl = null)
    {
        if ((null !== $baseUrl) && !is_string($baseUrl))  return $this;

        if ($baseUrl === null) {
            $filename = basename($_SERVER['SCRIPT_FILENAME']);

            if (basename($_SERVER['SCRIPT_NAME']) === $filename) {
                $baseUrl = $_SERVER['SCRIPT_NAME'];
            } elseif (basename($_SERVER['PHP_SELF']) === $filename) {
                $baseUrl = $_SERVER['PHP_SELF'];
            } elseif (isset($_SERVER['ORIG_SCRIPT_NAME']) && basename($_SERVER['ORIG_SCRIPT_NAME']) === $filename) {
                $baseUrl = $_SERVER['ORIG_SCRIPT_NAME'];
            } else {
                $path    = $_SERVER['PHP_SELF'];
                $segs    = explode('/', trim($_SERVER['SCRIPT_FILENAME'], '/'));
                $segs    = array_reverse($segs);
                $index   = 0;
                $last    = count($segs);
                $baseUrl = '';
                do {
                    $seg     = $segs[$index];
                    $baseUrl = '/' . $seg . $baseUrl;
                    ++$index;
                } while (($last > $index) && (false !== ($pos = strpos($path, $baseUrl))) && (0 != $pos));
            }
            // Does the baseUrl have anything in common with the request_uri?
            $requestUri = $this->getRequestUri();

            if (0 === strpos($requestUri, $baseUrl)) {
                // full $baseUrl matches
                $this->_baseUrl = $baseUrl;
                return $this;
            }

            if (0 === strpos($requestUri, dirname($baseUrl))) {
                // directory portion of $baseUrl matches
                $this->_baseUrl = rtrim(dirname($baseUrl), '/');
                return $this;
            }

            if (!strpos($requestUri, basename($baseUrl))) {
                // no match whatsoever; set it blank
                $this->_baseUrl = '';
                return $this;
            }

            // If using mod_rewrite or ISAPI_Rewrite strip the script filename
            // out of baseUrl. $pos !== 0 makes sure it is not matching a value
            // from PATH_INFO or QUERY_STRING
            if ((strlen($requestUri) >= strlen($baseUrl))
                && ((false !== ($pos = strpos($requestUri, $baseUrl))) && ($pos !== 0))) {
                $baseUrl = substr($requestUri, 0, $pos + strlen($baseUrl));
            }
        }

        $this->_baseUrl = rtrim($baseUrl, '/');
        return $this;
    }
}