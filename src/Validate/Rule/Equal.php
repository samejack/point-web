<?php

namespace point\web;

/**
 * 驗證變數相等
 * 
 * @author sj
 */
class Validate_Rule_Equal extends Validate_Base
{
    const FILTER_MD5 = 1;

    /**
     * 驗證變數相等
     *
     * @param mixed   [$args]
     * <pre>
     *   $args['with'] = Equal string or integer
     *   $args['filter'] = Filter can be give Validate_Rule_Equal::FILTER_MD5
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
            // md5
            if (isset($this->_args['filter']) && $this->_args['filter'] === self::FILTER_MD5) {
                return (md5($input) === $this->_args['with']);
            }
            return ($input === $this->_args['with']);
        }
        return false;
    }
}
