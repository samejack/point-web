<?php

namespace point\web;

/**
 * 驗證時間範圍 (格式 = H:i:s | H:i)
 * 
 * @author sj
 */
class Validate_Rule_TimeBetween extends Validate_Base
{
    /**
     * 驗證時間範圍 (格式 = H:i:s | H:i)
     *
     * @param mixed   [$args] 驗證參數
     * <pre>
     *   $args['start'] = 開始時間
     *   $args['end'] = 結束時間
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
        if (!isset($this->_args['start']) || !isset($this->_args['end'])) {
            return false;
        }
        
        // make vars
        $time = strtotime($input);
        $start = strtotime($this->_args['start']);
        $end = strtotime($this->_args['end']);
        return ($start <= $time && $time <= $end);
    }
}
