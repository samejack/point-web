<?php

namespace point\web;

/**
 * 驗證字串必須為參數中的值 (列舉)
 * 
 * @author sj
 */
class Validate_Rule_InValues extends Validate_Base
{

    /**
     * 驗證字串必須為參數中的值 (列舉)
     *
     * @param mixed $args 驗證參數 [optional]
     * <pre>
     *   $args = (
     *   	'Enum_A',
     *      'Enum_B',
     *      'Enum_C',
     *   )
     * </pre>
     * @param string  [$message]
     */
    public function __construct($args = null, $message = '')
    {
        parent::__construct($args, $message);
    }

    public function validate($input, array &$columns)
    {
        return in_array(((string)$input), $this->_args);
    }
}
