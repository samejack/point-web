<?php

namespace point\web;

/**
 * 檔案相關常用副程式
 *
 * @author sj
 */
class Utility_File
{
    public static $allowImageType = [
        'jpg' => true,
        'jpeg' => true,
        'gif' => true,
        'bmp' => true,
        'png' => true,
        'tif' => true
    ];

    public static function isImage($filePath, $checkExtension = true)
    {
        if (file_exists($filePath)) {
            if ($checkExtension) {
                $ext = pathinfo($filePath, PATHINFO_EXTENSION);
                if (!isset(self::$allowImageType[strtolower($ext)])) {
                    return false;
                }
            }
            $info = @getimagesize($filePath);
            if ($info)  return true;
        }
        return false;
    }

    public static function removeDirectory($path)
    {
        if (is_dir($path)) {
            $files = scandir($path);
            foreach ($files as $file) {
                if ($file !== '.' && $file !== '..') {
                    if (is_dir($path . DIRECTORY_SEPARATOR  . $file)) {
                        self::removeDirectory($path . DIRECTORY_SEPARATOR . $file);
                    } else {
                        unlink($path . DIRECTORY_SEPARATOR . $file);
                    }
                }
            }
            rmdir($path);
            return true;
        }
        return false;
    }
}
