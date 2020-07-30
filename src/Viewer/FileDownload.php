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
            setlocale(LC_ALL, 'C.UTF-8');
            $this->_filename = basename($fileName);
        }
    }
    
    public function render(Http_Request &$request, Http_Response &$response)
    {
        //load file
        if (is_null($this->_filepath)) {
            $mimetype = 'application/octet-stream';
        } else {
            $mimetype = Utility_MimeType::getMineType($this->_filepath);
        }
        //Set Http Herder
        $response->addHeader('Content-Description', 'File Transfer');
        $response->addHeader('Content-type', $mimetype);
            
        if (substr_count(strtolower($request->getServerParam('HTTP_USER_AGENT')), 'ie') > 0) {
            $response->addHeader('Content-Disposition', 'attachment; filename='. rawurlencode($this->_filename));
        } else {
            $response->addHeader('Content-Disposition', 'attachment; filename='. $this->_filename);
        }
        
        $response->addHeader('Content-Transfer-Encoding', 'binary');
        $response->addHeader('Expires', '0');
        $response->addHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0');
        $response->addHeader('Pragma', 'public');
        if (is_null($this->_filepath)) {
            $response->addHeader('Content-Length', strlen($this->_content));
            $response->output($this->_content);
        } else {
            $response->addHeader('Content-Length', filesize($this->_filepath));
            $response->sendHeaders();
            $fp = fopen($this->_filepath, 'r');
            while (!feof($fp)) {
                echo fgets($fp);
            }
            fclose($fp);
        }
    
        exit(0);
    }

    public function errorHandler(
        Http_Request &$request,
        Http_Response &$response,
        &$exception
    ) {
        $response->setStatusCode($exception->getCode());
        $response->output($exception->getMessage());
    }
}
