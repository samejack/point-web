<?php
namespace point\web;

class Viewer_Image implements Viewer_Interface
{

    private $_filepath = null;
    
    private $_content = null;
    
    private $_filename = null;
    
    public function setModel($key, $model)
    {
    }
    
    public function removeModel($key)
    {
    }
    
    public function getModel($key)
    {
        return null;
    }
    /**
     * 設定提供下載的檔案路徑
     * 
     * @param string $filePath File path
     * @param string $fileName File name
     */
    public function setOutputFilepath ($filePath, $fileName = null)
    {
        if (strpos($filePath, '..') !== false) die('File path has security issue. (\'..\' found)');
        $this->_filepath = $filePath;
        $this->_content = null;
        if (is_null($fileName)) {
            $this->_filename = basename($filePath);
        } else {
            $this->_filename = basename($fileName);
        }
    }
    
    public function render(Http_Request &$request, Http_Response &$response)
    {

        if (!is_readable($this->_filepath)) {
            if (!is_file($this->_filepath)) {
                $response->setStatusCode(404);
                $response->sendHeaders();
            } else {
                $response->setStatusCode(403);
                $response->sendHeaders();
            }
            return;
        }

        $lastModified = gmdate('D, d M Y H:i:s', intval(filemtime($this->_filepath))) . ' GMT+0800';
        $etag = fileinode($this->_filepath);

        $server = $request->getServerParams();
        if ((isset($server['HTTP_IF_MODIFIED_SINCE']) && $server['HTTP_IF_MODIFIED_SINCE'] === $lastModified)
            || (isset($server['HTTP_IF_NONE_MATCH']) && $server['HTTP_IF_NONE_MATCH'] === $etag)
        ) {
            $response->setStatusCode(304);
            $response->sendHeaders();
            return;
        }

        Header('ETag: "' . md5($etag) . '"');
        Header('Last-Modified: ' . $lastModified);

        //load file
        if (is_null($this->_filepath)) {
            $mimetype = 'application/octet-stream';
        } else {
            $mimetype = self::getMineType($this->_filename);
        }
        //Set Http Herder
        Header('Content-type: ' . $mimetype);
        Header('Expires: 0');
        Header('Cache-Control: max-age=0');
        Header('Pragma: private');
        Header('Content-Length: ' . filesize($this->_filepath));
        $fp = fopen($this->_filepath, 'r');
        while (!feof($fp)) {
            echo fgets($fp);
        }
        fclose($fp);
    
        exit(0);
    }
    
    public static function getMineType($filename)
    {
         
        preg_match("|\\.([a-z0-9]{2,4})$|i", $filename, $fileSuffix);
    
        switch(strtolower($fileSuffix[1])) {
            case 'js' :
                return 'application/x-javascript';
            case 'json' :
                return 'application/json';
            case 'jpg' :
            case 'jpeg' :
            case 'jpe' :
                return 'image/jpeg';
            case 'png' :
            case 'gif' :
            case 'bmp' :
            case 'tiff' :
                return 'image/'.strtolower($fileSuffix[1]);
            case 'css' :
                return 'text/css';
            case 'xml' :
                return 'application/xml';
            case 'doc' :
            case 'docx' :
                return 'application/msword';
            case 'xls' :
            case 'xlt' :
            case 'xlm' :
            case 'xld' :
            case 'xla' :
            case 'xlc' :
            case 'xlw' :
            case 'xll' :
                return 'application/vnd.ms-excel';
            case 'ppt' :
            case 'pps' :
                return 'application/vnd.ms-powerpoint';
            case 'rtf' :
                return 'application/rtf';
    
            case 'pdf' :
                return 'application/pdf';
            case 'html' :
            case 'htm' :
            case 'php' :
                return 'text/html';
            case 'txt' :
                return 'text/plain';
            case 'mpeg' :
            case 'mpg' :
            case 'mpe' :
                return 'video/mpeg';
            case 'mp3' :
                return 'audio/mpeg3';
            case 'wav' :
                return 'audio/wav';
            case 'aiff' :
            case 'aif' :
                return 'audio/aiff';
            case 'avi' :
                return 'video/msvideo';
            case 'wmv' :
                return 'video/x-ms-wmv';
            case 'mov' :
                return 'video/quicktime';
            case 'zip' :
                return 'application/zip';
            case 'tar' :
                return 'application/x-tar';
            case 'swf' :
                return 'application/x-shockwave-flash';
            default :
                if (function_exists('mime_content_type')) {
                    return mime_content_type($filename);
                }
                return 'application/octet-stream';
        }
    }

    public function errorHandler(
        Http_Request &$request,
        Http_Response &$response,
        \Exception &$exception
    ) {
        $response->setStatusCode($exception->getCode());
        $response->output($exception->getMessage());
    }
}
