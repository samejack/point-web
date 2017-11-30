<?php

namespace point\web;

/**
 * 驗證文字是否符合 HTTP URL 格式
 * 
 * @author sj
 */
class Validate_Rule_HttpUrl extends Validate_Base
{
    /**
     * 驗證文字是否符合 HTTP URL 格式
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
        return preg_match('/(https?:\/\/[\w-\.]+(:\d+)?(\/[~\w\/\.]*)?(\?\S*)?(#\S*)?)/', $input) > 0;
    }
}
