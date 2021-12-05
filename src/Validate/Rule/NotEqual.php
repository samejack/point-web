<?php

namespace point\web;

/**
 * 驗證變數相等
 * 
 * @author sj
 */
class Validate_Rule_NotEqual extends Validate_Base
{

    /**
     * 驗證變數相等
     *
     * @param mixed   [$args]
     * <pre>
     *   $args['with'] = Equal string or integer
     * </pre>
     * @param string  [$message]
     */
    public function __construct($args = null, $message = '')
    {
        parent::__construct($args, $message);
    }

    public function validate($input, array &$columns)
    {
        if (isset($this->_args['with'])) {
            return ($input !== $this->_args['with']);
        }
        return false;
    }
}
