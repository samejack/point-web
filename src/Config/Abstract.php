<?php
namespace point\web;

/**
 * Class Config_Abstract
 * Environment default is production
 *
 * @package point\web
 */
abstract class Config_Abstract implements Config_Interface
{

    protected $_properties = array();

    protected $_environment = 'production';

    public function __construct($properties = null)
    {
        if (getenv('ENVIRONMENT') !== false) {
            $this->_environment = getenv('ENVIRONMENT');
        } else if (defined('ENVIRONMENT')) {
            $this->_environment = ENVIRONMENT;
        }
        if (is_array($properties)) {
            $this->_properties = $properties;
        }
    }

    /**
     * Set environment value
     *
     * @param string $environment
     * @return void
     */
    public function setEnvironment($environment)
    {
        $this->_environment = $environment;
    }

    /**
     * Get environment value
     *
     * @return string
     */
    public function getEnvironment()
    {
        return $this->_environment;
    }

    /**
     * load configuration file
     *
     * @param string $filepath File path
     * @return mixed
     */
    public function loadfile($filepath)
    {
        // not implement
    }

    //IteratorAggregate
    public function getIterator()
    {
        return new \ArrayIterator($this->_properties);
    }

    public function rewind()
    {
        reset($this->_properties);
    }

    public function valid()
    {
        return current($this->_properties);
    }

    public function current()
    {
        if (!is_null($this->offsetGet($this->key()))) {
            return current($this->_properties);
        }
        return null;
    }

    public function key()
    {
        return key($this->_properties);
    }

    public function next()
    {
        next($this->_properties);
    }

    //Countable
    public function count(){
        return count($this->_properties);
    }

    //ArrayAccess
    public function offsetExists($key)
    {
        if (array_key_exists($key, $this->_properties)) {
            return true;
        }
//        foreach ($this->_properties as $pkey=>$value) {
//            if (strpos($pkey, $key.'.') === 0) {
//                return true;
//            }
//        }
        return false;
    }

    public function offsetGet($key)
    {
        if (array_key_exists($key, $this->_properties)) {
            return $this->_properties[$key];
        }
//        $newArray = array();
//        foreach ($this->_properties as $pkey=>$value) {
//            if (strpos($pkey, $key.'.') === 0) {
//                $newArray[substr($pkey, strlen($key)+1)] = $value;
//            }
//        }
//        if (count($newArray) > 0) {
//            return new self($newArray);
//        }
        return null;
    }

    public function offsetSet($key, $value)
    {
        //readonly
    }

    public function offsetUnset($key)
    {
        //readonly
    }
}
