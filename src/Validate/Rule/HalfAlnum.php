<?php

namespace point\web;

/**
 * 驗證字串必須為半形英數字
 * 
 * @author sj
 */
class Validate_Rule_HalfAlnum extends Validate_Base
{
    /**
     * 驗證字串必須為半形英數字
     *
     * @param mixed   [$args]
     * @param string  [$message]
     */
    public function __construct($args = null, $message = '')
    {
        parent::__construct($args, $message);
    }

    public function validate($input, array &$columns)
    {
        return preg_match('/^[a-zA-Z0-9]+$/', $input) > 0;
    }
}
