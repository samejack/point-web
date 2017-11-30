<?php

namespace point\web;

/**
 * 透過正規表示法驗證字串
 * 
 * @author sj
 */
class Validate_Rule_Regular extends Validate_Base
{

    /**
     * 透過正規表示法驗證字串
     *
     * @param mixed   $args Regular Expression
     * @param string  [$message]
     */
    public function __construct($args = null, $message = '')
    {
        parent::__construct($args, $message);
    }

    public function validate($input, array &$columns)
    {
        return preg_match($this->_args, $input) === 1;
    }
}
