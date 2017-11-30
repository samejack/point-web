<?php

namespace point\web;

/**
 * 使用指定編碼驗證文字長度
 * 
 * @author sj
 */
class Validate_Rule_Length extends Validate_Base
{
    /**
     * 使用指定編碼驗證文字長度
     *
     * @param mixed  [$args]
     * <pre>
     *   $args['min'] = 最小長度
     *   $args['max'] = 最大長度
     * </pre>
     * @param string [$message]
     */
    public function __construct($args = null, $message = '')
    {
        parent::__construct($args, $message);
    }

    public function validate($input, array &$columns)
    {
        if (isset($this->_args['encode'])) {
            $count = mb_strlen($input, $this->_args['encode']);
        } else {
            $count = strlen($input);
        }
        if (isset($this->_args['min']) && isset($this->_args['max'])) {
            return ($count >= $this->_args['min'] && $count <= $this->_args['max']);
        } else if (isset($this->_args['min'])) {
            return ($count >= $this->_args['min']);
        } else if (isset($this->_args['max'])) {
            return ($count <= $this->_args['max']);
        }
        return false;
    }
}
