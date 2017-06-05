<?php

namespace point\web;

/**
 * 字串相關常用副程式
 *
 * @author sj
 * @copyright Copyright 2012 toRight.com
 */
class Utility_String
{
    /**
     * 隨機產生一個 UUID
     *
     * @param bool $hyphen 是否出現 hyphen word
     * @return string
     */
    public static function makeUuid ($hyphen = true)
    {
        $split = '';
        if ($hyphen === true) {
            $split = '-';
        }
        $chars = md5(uniqid(mt_rand(), true));
        $uuid = substr($chars, 0, 8) . $split;
        $uuid .= substr($chars, 8, 4) . $split;
        $uuid .= substr($chars, 12, 4) . $split;
        $uuid .= substr($chars, 16, 4) . $split;
        $uuid .= substr($chars, 20, 12);
        return $uuid;
    }
    
    /**
     * 文字比較
     * 比較時會自動去除頭尾空白與換行
     *
     * @param string $first  比較文字 1
     * @param string $second 比較文字 2
     * @return bool 比較結果
     *     true : 文字一致
     *     false : 文字不一致
     */
    public static function compare($first, $second)
    {
        if (!is_string($first)||!is_string($second)) {
            return false;
        }
        $first = trim($first);
        $second = trim($second);
        if (strcmp($first, $second) == 0) {
            return true;
        }
        return false;
    }
    
    /**
     * 隨機產生一組隨機的密碼
     *
     * @param integer $length 密碼長度
     * @return string
     */
    public static function makePassword($length = 12)
    {
        // make directory
        $strs = 'abcdefghkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ012345679`~!@#$%^&*()_+|-=\\[]{};\':",./<>?';
        $password = '';
        while (strlen($password) < $length) {
            $password .= substr($strs, mt_rand(1, strlen($strs)), 1);
        }
        return str_shuffle($password);
    }
}