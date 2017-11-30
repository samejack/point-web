<?php

namespace point\web;

/**
 * 驗證 Ip Address
 * 
 * @author sj
 */
class Validate_Rule_IpAddress extends Validate_Base
{
    /**
     * 驗證 Ip Address
     *
     * @param mixed   [$args] 驗證參數
     * @param string  [$message]
     */
    public function __construct($args = null, $message = '')
    {
        parent::__construct($args, $message);
    }

    public function validate($input, array &$columns)
    {
        $regex  = '/^([1-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])';
        $regex .= '(\.([0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])){3}$/';
        return preg_match($regex, $input) > 0;
    }
}
