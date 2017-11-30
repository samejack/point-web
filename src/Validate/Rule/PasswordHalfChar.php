<?php

namespace point\web;

/**
 * 驗證字串必須為半形可視符號字元 (用在密碼驗證)
 * 
 * @author sj
 */
class Validate_Rule_PasswordHalfChar extends Validate_Base
{
    /**
     * 驗證字串必須為半形可視符號字元 (用在密碼驗證)
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
        return preg_match('/^[a-zA-Z0-9`~!@#$%^&*\(\)_\+-=\{\}\[\]|\\\\;\':",\.\/<>\?]+$/', $input) > 0;
    }
}
