<?php

namespace point\web;

/**
 * 驗證日期範圍 (格式 = Y-m-d)
 * 
 * @author sj
 */
class Validate_Rule_DateBetween extends Validate_Base
{
    /**
     * 驗證日期範圍 (格式 = Y-m-d)
     *
     * @param mixed   [$args] 驗證參數 [optional]
     * <pre>
     *   $args['start'] = 開始日期
     *   $args['end'] = 結束日期
     * </pre>
     * @param string  [$message]
     */
    public function __construct($args = null, $message = '')
    {
        parent::__construct($args, $message);
    }

    public function validate($input, array &$columns)
    {
        //check agrs
        if (!isset($this->_args['start']) || !isset($this->args['end'])) {
            return false;
        }

        $time = strtotime($input);
        $start = strtotime($this->args['start']);
        $end = strtotime($this->args['end']);
        return ($start <= $time && $time <= $end);
    }
}
