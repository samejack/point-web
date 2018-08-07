<?php

namespace point\web;

/**
 * 字串相關常用副程式
 *
 * @author sj
 */
class Utility_File
{
    public static function isImage($filePath)
    {
        if (file_exists($filePath)) {
            if (($info = @getimagesize($filePath))
            return false;

            $ext = image_type_to_extension($info['2']);
            return preg_match('/^.+\.((jpg|jpeg|gif|bmp|png))$/', $ext) === 1;
        } else {
            return false;
        }
    }
}
