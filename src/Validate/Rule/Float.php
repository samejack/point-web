<?php

namespace point\web;

/**
 * 驗證字串為小數
 * 
 * @author sj
 */
class Validate_Rule_Float extends Validate_Base
{
    /**
     * 驗證字串為小數
     *
     * @param mixed   [$args] 驗證參數 [optional]<br />
     * <pre>
     *   $args['range'] = 小數點最高位數
     * </pre>
     * @param string  [$message]
     */
    public function __construct($args = null, $message = '')
    {
        parent::__construct($args, $message);
    }

    public function validate($input, array &$columns)
    {
        return preg_match('/^\d$|^\d+\.?\d{1,' . $this->_args['range'] . '}+$/', $input) > 0;
    }
}
