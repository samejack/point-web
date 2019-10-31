<?php
namespace point\web;

/**
 * 驗證器 Interface
 * 
 * @author sj
 */
abstract class Validate_Base implements Validate_Interface
{

    protected $_args = null;

    protected $_message = null;

    /**
     * @param mixed  [$args]    驗證參數 [optional]
     * @param string [$message]  錯誤訊息
     */
    public function __construct($args = null, $message = '')
    {
        $this->_args = $args;
        $this->_message = $message;
    }

    /**
     * 驗證字串格式
     * 
     * @param string $input    驗證字串
     * @param array  $columns  All columns value
     * @return boolean 通過或未通過驗證
     */
    public function validate($input, array &$columns)
    {
        return false;
    }

    public function getMessage()
    {
        return $this->_message;
    }
}