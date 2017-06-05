<?php
namespace point\web;

class Viewer_FileDownload implements Viewer_Interface
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
    public function setOutputFilepath ($filePath, $fileName = null) {
        $this->_filepath = $filePath;
        $this->_content = null;
        if (is_null($fileName)) {
            $this->_filename = basename($filePath);
        } else {
            $this->_filename = basename($fileName);
        }
    }
    /**
     * 設定提供下載的內容
     * 
     * @param string $content  Download output string
     * @param string $fileName File name
     */
    public function setOutputContent ($content, $fileName = null) {
        $this->_filepath = null;
        $this->_content = $content;
        if (is_null($fileName)) {
            $this->_filename = date('Y-m-d_His') . '.download';
        } else {
            $this->_filename = basename($fileName);
        }
    }
    
    public function render(Http_Request &$request, Http_Response &$response)
    {
        //load file
        if (is_null($this->_filepath)) {
            $mimetype = 'application/octet-stream';
        } else {
            $mimetype = self::getMineType($this->_filepath);
        }
        //Set Http Herder
        Header('Content-Description: File Transfer');
        Header('Content-type: ' . $mimetype);
            
        if (substr_count(strtolower($_SERVER['HTTP_USER_AGENT']), 'ie') > 0) {
            Header('Content-Disposition: attachment; filename='. rawurlencode($this->_filename));
        } else {
            Header('Content-Disposition: attachment; filename='. $this->_filename);
        }
            
        Header('Content-Transfer-Encoding: binary');
        Header('Expires: 0');
        Header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        Header('Pragma: public');
        if (is_null($this->_filepath)) {
            Header('Content-Length: ' . strlen($this->_content));
            $response->output($this->_content);
        } else {
            Header('Content-Length: ' . filesize($this->_filepath));
            $fp = fopen($this->_filepath, 'r');
            while (!feof($fp)) {
                echo fgets($fp);
            }
            fclose($fp);
        }
    
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
                return 'image/jpg';
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
