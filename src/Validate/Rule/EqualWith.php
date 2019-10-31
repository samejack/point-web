<?php

namespace point\web;

/**
 * 驗證與其他欄位相等
 * 
 * @author sj
 */
class Validate_Rule_EqualWith extends Validate_Base
{
    /**
     * 驗證與其他欄位相等
     *
     * @param mixed   [$args] Columns name
     * @param string  [$message]
     */
    public function __construct($args = null, $message = '')
    {
        parent::__construct($args, $message);
    }

    public function validate($input, array &$columns)
    {
        return isset($this->_args) && isset($columns[$this->_args]) && $input === $columns[$this->_args];
    }
}
