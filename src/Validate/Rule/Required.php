<?php

namespace point\web;

/**
 * 驗證必須不為空
 * 
 * @author sj
 */
class Validate_Rule_Required extends Validate_Base
{
    /**
     * 驗證必須不為空
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
        if (!isset($input)) {
            return false;
        }
        if (is_array($input)) {
            return !is_null($input) && count($input) > 0;
        } else {
            return strlen(chop($input)) > 0;
        }
    }
}
