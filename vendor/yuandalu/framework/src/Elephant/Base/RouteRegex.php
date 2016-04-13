<?php

namespace Elephant\Base;

class RouteRegex
{
    protected $_regex       = null;
    protected $_defaults    = array();
    protected $_reverse     = null;

    protected $_values      = array();
    
    public function __construct($route, $defaults = array(), $map = array(), $reverse = null)
    {
        $this->_regex = '#^' . $route . '$#i';
        $this->_defaults = (array) $defaults;
        $this->_map = (array) $map;
        $this->_reverse = $reverse;
    }
    
    /**
     * Matches a user submitted path with a previously defined route.
     * Assigns and returns an array of defaults on a successful match.
     *
     * @param string Path used to match against this routing map
     * @return array|false An array of assigned values or a false on a mismatch
     */

    public function match($path)
    {
        $path = trim(urldecode($path),'/');
        $res  = preg_match($this->_regex,$path,$values);

        if ($res === 0) return false;
        foreach ($values as $i => $value) {
            if (!is_int($i) || $i === 0) {
                unset($values[$i]);
            }
        }
        
        $this->_values = $values;

        $values = $this->_getMappedValues($values);
        $defaults = $this->_getMappedValues($this->_defaults, false, true);

        $return = $values + $defaults;

        return $return;
    }

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
    {
        if (count($this->_map) == 0) {
            return $values;
        }

        $return = array();
        foreach ($values as $key => $value) {
            if (is_int($key) && !$reversed) {
                if (array_key_exists($key, $this->_map)) {
                    $index = $this->_map[$key];
                } elseif (false === ($index = array_search($key, $this->_map))) {
                    $index = $key;
                }
                $return[$index] = $values[$key];
            } elseif ($reversed) {
                $index = (!is_int($key)) ? array_search($key, $this->_map, true) : $key;
                if (false !== $index) {
                    $return[$index] = $values[$key];
                }
            } elseif ($preserve) {
                $return[$key] = $value;
            }
        }
        return $return;
    }

}