<?php

namespace Elephant\Base;

class StandRoute
{
    protected $_urlVariable = ':';
    protected $_urlDelimiter = '/';
    protected $_regexDelimiter = '#';
    protected $_defaultRegex = null;

    protected $_parts;
    protected $_defaults = array();
    protected $_requirements = array();
    protected $_staticCount = 0;
    protected $_vars = array();
    protected $_params = array();
    protected $_values = array();

    public function __construct($route,$defaults = array() , $reqs = array())
    {
        $route = trim($route,$this->_urlDelimiter);
        $this->_defaults     = (array)$defaults;
        $this->_requirements = (array)$reqs;

        if ($route != '') {
            foreach (explode($this->_urlDelimiter,$route) as $pos => $part) {
                if (substr($part, 0 , 1) == $this->_urlVariable) {
                    $name   = substr($part,1);
                    $regex  = (isset($reqs[$name])) ? $reqs[$name] : $this->_defaultRegex;
                    $this->_parts[$pos] = array('name' => $name , 'regex' => $regex);
                    $this->_vars[]      = $name;
                } else {
                    $this->_parts[$pos] = array('regex' => $part);
                    if ($part != '*') {
                        $this->_staticCount++;
                    }
                }
            }
        }
    }
    
    protected function _getWildcardData($parts, $unique)
    {
        $pos = count($parts);
        if ($pos % 2) {
            $parts[] = null;
        }
        foreach(array_chunk($parts, 2) as $part) {
            list($var, $value) = $part;
            $var = urldecode($var);
            if (!array_key_exists($var, $unique)) {
                $this->_params[$var] = urldecode($value);
                $unique[$var] = true;
            }
        }
    }

    /**
     * Matches a user submitted path with parts defined by a map. Assigns and
     * returns an array of variables on a successful match.
     *
     * @param string Path used to match against this routing map
     * @return array|false An array of assigned values or a false on a mismatch
     */
    public function match($path)
    {
        $pathStaticCount = 0;
        $defaults = $this->_defaults;

        if (count($defaults)) {
            $unique = array_combine(array_keys($defaults), array_fill(0, count($defaults), true));
        } else {
            $unique = array();
        }
        $path = trim($path, $this->_urlDelimiter);

        if ($path != '') {
            $path = explode($this->_urlDelimiter, $path);
            foreach ($path as $pos => $pathPart) {
                if (!isset($this->_parts[$pos])) {
                    return false;
                }

                if ($this->_parts[$pos]['regex'] == '*') {
                    $parts = array_slice($path, $pos);
                    $this->_getWildcardData($parts, $unique);
                    break;
                }

                $part = $this->_parts[$pos];
                $name = isset($part['name']) ? $part['name'] : null;
                $pathPart = urldecode($pathPart);

                if ($name === null) {
                    if ($part['regex'] != $pathPart) {
                        return false;
                    }
                } elseif ($part['regex'] === null) {
                    if (strlen($pathPart) == 0) {
                        return false;
                    }
                } else {
                    $regex = $this->_regexDelimiter . '^' . $part['regex'] . '$' . $this->_regexDelimiter . 'iu';
                    if (!preg_match($regex, $pathPart)) {
                        return false;
                    }
                }

                if ($name !== null) {
                    // It's a variable. Setting a value
                    $this->_values[$name] = $pathPart;
                    $unique[$name] = true;
                } else {
                    $pathStaticCount++;
                }

            }

        }

        $return = $this->_values + $this->_params + $this->_defaults;

        // Check if all static mappings have been met
        if ($this->_staticCount != $pathStaticCount) {
            return false;
        }

        // Check if all map variables have been initialized
        foreach ($this->_vars as $var) 
        {
            if (!array_key_exists($var, $return)) {
                return false;
            }
        }

        return $return;

    }
}