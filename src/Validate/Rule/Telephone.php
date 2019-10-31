<?php

namespace point\web;

/**
 * 驗證電話號碼格式
 * 
 * @author sj
 */
class Validate_Rule_Telephone extends Validate_Base
{
    const STYLE_TW = 0;

    /**
     * 驗證電話號碼格式
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
        if ($this->_args === self::STYLE_TW) {
            return preg_match('/^09[0-9]{8,8}$/', $input) > 0;
        }
        return preg_match('/^[0-9\-\+#]+$/', $input) > 0;
    }
}
