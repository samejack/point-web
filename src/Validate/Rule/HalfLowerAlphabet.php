<?php

namespace point\web;

/**
 * 驗證字串必須為半形英文小寫
 * 
 * @author sj
 */
class Validate_Rule_HalfLowerAlphabet extends Validate_Base
{
    /**
     * 驗證字串必須為半形英文小寫
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
        return preg_match('/^[a-z]+$/', $input) > 0;
    }
}
