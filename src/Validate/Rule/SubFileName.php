<?php

namespace point\web;

/**
 * 驗證字串副檔名(不分大小寫)
 * 
 * @author sj
 */
class Validate_Rule_SubFileName extends Validate_Base
{
    /**
     * 驗證字串副檔名(不分大小寫)
     *
     * @param mixed   [$args] 驗證參數
     * <pre>
     *   $args = (
     *   	'jpg',
     *      'jpeg',
     *      'png',
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
        // check args
        if (is_null($this->_args) || !is_array($this->_args) || count($this->_args) === 0) {
            return false;
        }

        $position = strrpos($input, '.');
        if ($position > 0) {
            $subname = strtolower(substr($input, $position + 1));
            foreach ($this->_args as &$subName) {
                if ($subname === strtolower($subName)) {
                    return true;
                }
            }
        }
        return false;
    }
}
