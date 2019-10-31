<?php

namespace point\web;

/**
 * 假的替代驗證器，用來定義 Break
 * 
 * @author sj
 */
class Validate_Rule_Optional extends Validate_Base
{
    /**
     * 假的替代驗證器，用來定義 Break
     *
     * @param mixed  [$args]
     * @param string [$message]
     */
    public function __construct($args = null, $message = '')
    {
        parent::__construct($args, $message);
    }

    public function validate($input, array &$columns)
    {
        return true;
    }
}
