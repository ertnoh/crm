<?php
namespace crm;

use \ArrayAccess;

class Registry Implements ArrayAccess
{
    protected static $_instance;
    private $vars = array();

    /**
     * Singltone необходим для обращения к регистру из - под ajax и не только.
     * В этом случае все переменные уже сформированы, поэтому нет
     * смысла создавать новый объект данного класса.
     *
     * @return Registry
     */
    public static function getInstance()
    {
        if (self::$_instance === null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }


    function set($key, $var)
    {
        $this->vars[$key] = $var;
        return true;
    }

    function get($key)
    {
        if (!isset($this->vars[$key])) {
            return null;
        }
        return $this->vars[$key];
    }

    function remove($key)
    {
        unset($this->vars[$key]);
    }

    function offsetExists($offset)
    {
        return isset($this->vars[$offset]);
    }

    function offsetGet($offset)
    {
        return $this->get($offset);
    }

    function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    function offsetUnset($offset)
    {
        unset($this->vars[$offset]);
    }
}