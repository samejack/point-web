<?php

namespace point\web;

/**
 * 驗證字串為半形數字 0~9 組成
 * 
 * @author sj
 */
class Validate_Rule_HalfNumber extends Validate_Base
{
    /**
     * 驗證字串為半形數字 0~9 組成
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
        return preg_match('/^[0-9]+$/', $input) > 0;
    }
}
