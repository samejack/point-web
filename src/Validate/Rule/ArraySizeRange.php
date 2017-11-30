<?php

namespace point\web;

/**
 * 驗證陣列元素數量
 * 
 * @author sj
 */
class Validate_Rule_ArraySizeRange extends Validate_Base
{
    /**
     * 驗證陣列元素數量
     *
     * @param mixed   [$args]
     * <pre>
     *   $args['min'] = 最小數量
     *   $args['max'] = 最大數量
     * </pre>
     * @param string  [$message]
     */
    public function __construct($args = null, $message = '')
    {
        parent::__construct($args, $message);
    }

    public function validate($input)
    {
        $min = $this->_args['min'];
        $max = $this->_args['max'];
        return is_array($input) && count($input) >= $min && count($input) <= $max;
    }
}
