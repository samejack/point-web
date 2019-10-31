<?php

namespace point\web;

/**
 * 驗證 Email輸入格式
 * 
 * @author sj
 */
class Validate_Rule_Email extends Validate_Base
{
    /**
     * 驗證Email輸入格式
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
        $pattern = '/^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/';
        return preg_match($pattern, $input) > 0;
    }
}
