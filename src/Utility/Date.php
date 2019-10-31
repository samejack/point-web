<?php

namespace point\web;

/**
 * 日期相關常用副程式
 *
 * @author sj
 */
class Utility_Date
{

    /**
     * @Autowired
     * @var \point\web\Config_Interface
     */
    private $_config;

    public function getDefaultTimeZone()
    {
        if (isset($this->_config['system']['timezone'])) {
            return $this->_config['system']['timezone'];
        }
        return date_default_timezone_get();
    }

    /**
     * 取得 UTC 日期文字
     *
     * @return string
     */
    public function getUtcTimezoneDateTime()
    {
        return $this->getTimezoneDatetime('UTC');
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
    public function getTimezoneDateTime($timezone = null, $date = null, $fixString = '')
    {
        if (is_null($timezone)) {
            $timezone = $this->getDefaultTimeZone();
        }
        if (is_null($date)) {
            if ($fixString !== '') {
                $date = date('Y-m-d H:i:s', strtotime($date . $fixString));
            } else {
                $date = date('Y-m-d H:i:s');
            }
        }
        if ($timezone === date_default_timezone_get()) {
            return $date;
        } else {
            // need to convert time zone
            return self::convertTimezone($date, date_default_timezone_get(), $timezone);
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
    public function checkTimezoneCode($timezoneCode)
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
    public function convertTimezone($strDatetime, $inputTimezone, $outputTimezone)
    {
        if (!$this->checkTimezoneCode($inputTimezone)) {
            throw new \Exception('Timezone code is invalidate : '.$inputTimezone);
        }
        if (!$this->checkTimezoneCode($outputTimezone)) {
            throw new \Exception('Timezone code is invalidate : '.$outputTimezone);
        }
        if ($inputTimezone === $outputTimezone) {
            return $strDatetime;
        }

        $storeTimeZoneStore = date_default_timezone_get();
        date_default_timezone_set($inputTimezone);
        $timestamp = strtotime($strDatetime) - date('Z');
        date_default_timezone_set($outputTimezone);
        $convertedDate = date('Y-m-d H:i:s', $timestamp);
        date_default_timezone_set($storeTimeZoneStore);

        return $convertedDate;
    }
    /**
     * 比較傳入的時間差
     *
     * @param string $strDateFirst 要比較的時間之一
     * @param string $strDateSecond 要比較的時間之二
     * @return int 秒數差
     */
    public function compare($strDateFirst, $strDateSecond)
    {
        return (strtotime($strDateSecond) - strtotime($strDateFirst));
    }
}