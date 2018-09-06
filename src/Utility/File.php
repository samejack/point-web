<?php

namespace point\web;

/**
 * 檔案相關常用副程式
 *
 * @author sj
 */
class Utility_File
{
    public static function isImage($filePath, $checkExtension = true)
    {
        if (file_exists($filePath)) {
            $info = @getimagesize($filePath);
            if (!$info)  return false;

            if (!$checkExtension) {
                return true;
            } else {
                $ext = image_type_to_extension($info['2']);
                return preg_match('/^.+\.((jpg|jpeg|gif|bmp|png))$/', $ext) === 1;
            }
        } else {
            return false;
        }
    }
}
