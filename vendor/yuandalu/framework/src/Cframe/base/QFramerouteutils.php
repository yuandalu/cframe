<?php
class QFrameStandRoute
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
    {/*{{{*/
        $route = trim($route,$this->_urlDelimiter);
        $this->_defaults     = (array)$defaults;
        $this->_requirements = (array)$reqs;

        if($route != '')
        {
            foreach(explode($this->_urlDelimiter,$route) as $pos => $part)
            {
                if(substr($part, 0 , 1) == $this->_urlVariable)
                {
                    $name   = substr($part,1);
                    $regex  = (isset($reqs[$name])) ? $reqs[$name] : $this->_defaultRegex;
                    $this->_parts[$pos] = array('name' => $name , 'regex' => $regex);
                    $this->_vars[]      = $name;
                }else
                {
                    $this->_parts[$pos] = array('regex' => $part);
                    if($part != '*')
                    {
                        $this->_staticCount++;
                    }
                }
            }
        }
    }/*}}}*/
    
    protected function _getWildcardData($parts, $unique)
    {/*{{{*/
        $pos = count($parts);
        if ($pos % 2) 
        {
            $parts[] = null;
        }
        foreach(array_chunk($parts, 2) as $part) 
        {
            list($var, $value) = $part;
            $var = urldecode($var);
            if (!array_key_exists($var, $unique)) 
            {
                $this->_params[$var] = urldecode($value);
                $unique[$var] = true;
            }
        }
    }/*}}}*/

    /**
     * Matches a user submitted path with parts defined by a map. Assigns and
     * returns an array of variables on a successful match.
     *
     * @param string Path used to match against this routing map
     * @return array|false An array of assigned values or a false on a mismatch
     */
    public function match($path)
    {/*{{{*/
        $pathStaticCount = 0;
        $defaults = $this->_defaults;

        if (count($defaults)) 
        {
            $unique = array_combine(array_keys($defaults), array_fill(0, count($defaults), true));
        } else 
        {
            $unique = array();
        }
        $path = trim($path, $this->_urlDelimiter);

        if ($path != '') 
        {
            $path = explode($this->_urlDelimiter, $path);
            foreach ($path as $pos => $pathPart) 
            {
                if (!isset($this->_parts[$pos])) 
                {
                    return false;
                }

                if ($this->_parts[$pos]['regex'] == '*') 
                {
                    $parts = array_slice($path, $pos);
                    $this->_getWildcardData($parts, $unique);
                    break;
                }

                $part = $this->_parts[$pos];
                $name = isset($part['name']) ? $part['name'] : null;
                $pathPart = urldecode($pathPart);

                if ($name === null) 
                {
                    if ($part['regex'] != $pathPart) 
                    {
                        return false;
                    }
                } elseif ($part['regex'] === null) 
                {
                    if (strlen($pathPart) == 0) 
                    {
                        return false;
                    }
                } else 
                {
                    $regex = $this->_regexDelimiter . '^' . $part['regex'] . '$' . $this->_regexDelimiter . 'iu';
                    if (!preg_match($regex, $pathPart)) 
                    {
                        return false;
                    }
                }

                if ($name !== null) 
                {
                    // It's a variable. Setting a value
                    $this->_values[$name] = $pathPart;
                    $unique[$name] = true;
                } else 
                {
                    $pathStaticCount++;
                }

            }

        }

        $return = $this->_values + $this->_params + $this->_defaults;

        // Check if all static mappings have been met
        if ($this->_staticCount != $pathStaticCount) 
        {
            return false;
        }

        // Check if all map variables have been initialized
        foreach ($this->_vars as $var) 
        {
            if (!array_key_exists($var, $return)) 
            {
                return false;
            }
        }

        return $return;

    }/*}}}*/
}

class QFrameRouteRegex
{
    protected $_regex       = null;
    protected $_defaults    = array();
    protected $_reverse     = null;

    protected $_values      = array();
    
    public function __construct($route, $defaults = array(), $map = array(), $reverse = null)
    {/*{{{*/
        $this->_regex = '#^' . $route . '$#i';
        $this->_defaults = (array) $defaults;
        $this->_map = (array) $map;
        $this->_reverse = $reverse;
    }/*}}}*/
    
    /**
     * Matches a user submitted path with a previously defined route.
     * Assigns and returns an array of defaults on a successful match.
     *
     * @param string Path used to match against this routing map
     * @return array|false An array of assigned values or a false on a mismatch
     */

    public function match($path)
    {/*{{{*/
        $path = trim(urldecode($path),'/');
        $res  = preg_match($this->_regex,$path,$values);

        if($res === 0) return false;
        foreach($values as $i => $value)
        {
            if(!is_int($i) || $i === 0)
            {
                unset($values[$i]);
            }
        }
        
        $this->_values = $values;

        $values = $this->_getMappedValues($values);
        $defaults = $this->_getMappedValues($this->_defaults, false, true);

        $return = $values + $defaults;

        return $return;
    }/*}}}*/

   /**
    * Maps numerically indexed array values to it's associative mapped counterpart.
    * Or vice versa. Uses user provided map array which consists of index => name
    * parameter mapping. If map is not found, it returns original array.
    *
    * Method strips destination type of keys form source array. Ie. if source array is
    * indexed numerically then every associative key will be stripped. Vice versa if reversed
    * is set to true.
    *
    * @param array Indexed or associative array of values to map
    * @param boolean False means translation of index to association. True means reverse.
    * @param boolean Should wrong type of keys be preserved or stripped.
    * @return array An array of mapped values
    **/ 
    protected function _getMappedValues($values, $reversed = false, $preserve = false)
    {/*{{{*/
        if (count($this->_map) == 0) 
        {
            return $values;
        }

        $return = array();
        foreach ($values as $key => $value) 
        {
            if (is_int($key) && !$reversed) 
            {
                if (array_key_exists($key, $this->_map)) 
                {
                    $index = $this->_map[$key];
                } elseif (false === ($index = array_search($key, $this->_map))) 
                {
                    $index = $key;
                }
                $return[$index] = $values[$key];
            } elseif ($reversed) 
            {
                $index = (!is_int($key)) ? array_search($key, $this->_map, true) : $key;
                if (false !== $index) 
                {
                    $return[$index] = $values[$key];
                }
            } elseif ($preserve) 
            {
                $return[$key] = $value;
            }
        }
        return $return;
    }/*}}}*/

}
?>
