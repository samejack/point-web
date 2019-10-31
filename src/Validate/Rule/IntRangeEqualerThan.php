<?php

namespace point\web;

/**
 * 驗證數字範圍
 * 
 * @author sj
 */
class Validate_Rule_IntRangeEqualerThan extends Validate_Base
{
    /**
     * 驗證數字範圍
     *
     * @param mixed   [$args] 驗證參數 [optional]
     * <pre>
     *   $args['min'] = 最小值
     *   $args['max'] = 最大值
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
        if (!isset($this->_args['min']) || !isset($this->_args['max'])) {
            return false;
        }

        $min = $this->_args['min'];
        $max = $this->_args['max'];
        return ((int)$input >= $min && (int)$input <= $max);
    }
}
