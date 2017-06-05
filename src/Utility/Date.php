<?php

namespace point\web;

/**
 * 日期相關常用副程式
 *
 * @author sj
 * @copyright Copyright 2012 toRight.com
 */
class Utility_Date
{
    const DEFAULT_TIMEZONE = 'UTC';

    /**
     * 取得 UTC 日期文字
     *
     * @return string
     */
    public static function getUtcTimezoneDatetime()
    {
        return self::getTimezoneDatetime('UTC');
    }
    /**
     * 取得時間
     *
     * @param string $timezone [optional]<br />
     *   預設為 UTC
     * @param string $date [optional]<br />
     *  Format = 'Y-m-d H:i:s'
     * @return string 時間字串
     */
    public static function getTimezoneDatetime($timezone = Utility_Date::DEFAULT_TIMEZONE, $date=null)
    {
        if (is_null($date)) {
            $date = date('Y-m-d H:i:s');
        }
        if ($timezone === Utility_Date::DEFAULT_TIMEZONE) {
            return $date;
        } else {
            // need to convert time zone
            return self::convertTimezone($date, Utility_Date::DEFAULT_TIMEZONE, $timezone);
        }
    }
    /**
     * 檢查 Timezone 名稱是否合法
     *
     * @param string $timezoneCode Timezone 名稱
     * @return bool<br />
     *   true:合法<br />
     *   false:不合法
     */
    public static function checkTimezoneCode($timezoneCode)
    {
        foreach (timezone_identifiers_list() as $timezone) {
            if (Utility_String::compare($timezoneCode, $timezone)) {
                return true;
            }
        }
        return false;
    }
    /**
     * 日期文字轉換
     *
     * @param string $strDatetime 日期文字 (Y-m-d H:i:s)
     * @param string $inputTimezone 原本的 Timezone
     * @param string $outputTimezone 要轉換的 Timezone
     * @return string 轉換的日期文字
     * @throws \Exception
     */
    public static function convertTimezone($strDatetime, $inputTimezone, $outputTimezone)
    {
        if (!self::checkTimezoneCode($inputTimezone)) {
            throw new \Exception('Timezone code is invalidate : '.$inputTimezone);
        }
        if (!self::checkTimezoneCode($outputTimezone)) {
            throw new \Exception('Timezone code is invalidate : '.$outputTimezone);
        }
    
        $date = new Zend_Date();
        $defaultTimeZoneStore = date_default_timezone_get();
        date_default_timezone_set($inputTimezone);
        $date->setTimestamp(strtotime($strDatetime));
        $date->setTimezone($outputTimezone);
        date_default_timezone_set($defaultTimeZoneStore);

        return str_replace('T', ' ', substr($date->get(Zend_Date::W3C), 0, 19));
    }
    /**
     * 比較傳入的時間差
     *
     * @param string $strDateFirst 要比較的時間之一
     * @param string $strDateSecond 要比較的時間之二
     * @return int 秒數差
     */
    public static function compare($strDateFirst, $strDateSecond)
    {
        return (strtotime($strDateSecond) - strtotime($strDateFirst));
    }
}