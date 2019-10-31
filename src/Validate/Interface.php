<?php
namespace point\web;

/**
 * 驗證器 Interface
 * 
 * @author sj
 */
interface Validate_Interface
{
    /**
     * Validate string
     * 
     * @param string $input   Validate string
     * @param array  $columns All columns
     * @return boolean Pass or not
     */
    public function validate($input, array &$columns);

    /**
     * Get error message on verification failed
     *
     * @return string
     */
    public function getMessage();
}