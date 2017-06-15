<?php
namespace point\web;

class Http_Response
{
    const HTTP_STATUS_CODE_MSG_100 = 'Continue';
    const HTTP_STATUS_CODE_MSG_101 = 'Switching Protocols';
    const HTTP_STATUS_CODE_MSG_200 = 'OK';
    const HTTP_STATUS_CODE_MSG_201 = 'Created';
    const HTTP_STATUS_CODE_MSG_202 = 'Accepted';
    const HTTP_STATUS_CODE_MSG_203 = 'Non-Authoritative Information';
    const HTTP_STATUS_CODE_MSG_204 = 'No Content';
    const HTTP_STATUS_CODE_MSG_205 = 'Reset Content';
    const HTTP_STATUS_CODE_MSG_206 = 'Partial Content';
    const HTTP_STATUS_CODE_MSG_300 = 'Multiple Choices';
    const HTTP_STATUS_CODE_MSG_301 = 'Moved Permanently';
    const HTTP_STATUS_CODE_MSG_302 = 'Moved Temporarily';
    const HTTP_STATUS_CODE_MSG_303 = 'See Other';
    const HTTP_STATUS_CODE_MSG_304 = 'Not Modified';
    const HTTP_STATUS_CODE_MSG_305 = 'Use Proxy';
    const HTTP_STATUS_CODE_MSG_400 = 'Bad Request';
    const HTTP_STATUS_CODE_MSG_401 = 'Unauthorized';
    const HTTP_STATUS_CODE_MSG_402 = 'Payment Required';
    const HTTP_STATUS_CODE_MSG_403 = 'Forbidden';
    const HTTP_STATUS_CODE_MSG_404 = 'Not Found';
    const HTTP_STATUS_CODE_MSG_405 = 'Method Not Allowed';
    const HTTP_STATUS_CODE_MSG_406 = 'Not Acceptable';
    const HTTP_STATUS_CODE_MSG_407 = 'Proxy Authentication Required';
    const HTTP_STATUS_CODE_MSG_408 = 'Request Time-out';
    const HTTP_STATUS_CODE_MSG_409 = 'Conflict';
    const HTTP_STATUS_CODE_MSG_410 = 'Gone';
    const HTTP_STATUS_CODE_MSG_411 = 'Length Required';
    const HTTP_STATUS_CODE_MSG_412 = 'Precondition Failed';
    const HTTP_STATUS_CODE_MSG_413 = 'Request Entity Too Large';
    const HTTP_STATUS_CODE_MSG_414 = 'Request-URI Too Large';
    const HTTP_STATUS_CODE_MSG_415 = 'Unsupported Media Type';
    const HTTP_STATUS_CODE_MSG_500 = 'Internal Server Error';
    const HTTP_STATUS_CODE_MSG_501 = 'Not Implemented';
    const HTTP_STATUS_CODE_MSG_502 = 'Bad Gateway';
    const HTTP_STATUS_CODE_MSG_503 = 'Service Unavailable';
    const HTTP_STATUS_CODE_MSG_504 = 'Gateway Time-out';
    const HTTP_STATUS_CODE_MSG_505 = 'HTTP Version not supported';

    private $_headers;
    
    private $_protocol;
    
    private $_responseRawHeader;

    private $_responseRawBoby;

    public function __construct ()
    {
        $this->reset();
    }

    public function reset()
    {

        unset($this->_headers);
        $this->_headers = array();

        unset($this->_protocol);
        if (isset($_SERVER['SERVER_PROTOCOL'])) {
            $this->_protocol = $_SERVER['SERVER_PROTOCOL'];
        } else {
            $this->_protocol = 'HTTP/1.1';
        }

        unset($this->_responseRawHeader);
        $this->_responseRawHeader = array();

        unset($this->_responseRawBoby);
        $this->_responseRawBoby = null;

        // default header
        $this->addHeader($this->_protocol . ' 200 ' . self::HTTP_STATUS_CODE_MSG_200);
    }

    /**
     * HTTP 301 Moved Permanently
     *
     * @param string $url Redirect URL
     */
    public function redirect($url)
    {
        $this->addHeader($this->_protocol . ' 301 Moved Permanently');
        $this->addHeader('Location', $url);
        $this->sendHeaders();
        if (!$this->isCliMode()) {
            exit();
        }
    }
    
    public function addHeader($name, $value = null)
    {
        if (is_null($value)) {
            $this->_headers['HTTP'] = $name;
        } else {
            $this->_headers[$name] = $name . ': ' . $value;
        }
    }
    
    public function output($data = null)
    {
        $this->sendHeaders();
        if (!is_null($data)) {
            echo $data;
        }
    }
    
    public function getResponseRawBoby()
    {
        return $this->_responseRawBody;
    }

    public function setResponseRawBoby(&$raw)
    {
        $this->_responseRawBody = $raw;
    }

    public function getHeaders()
    {
        return $this->_headers;
    }

    public function sendHeaders()
    {
        if (!$this->isCliMode()) {
            foreach ($this->_headers as &$header) {
                header($header);
            }
            $this->_headers = array();
        }
    }
 
    public function setStatusCode($statusCode)
    {
        if (isset($this->_headers['HTTP'])) {
            $this->_headers['HTTP'] = sprintf(
                '%s %d %s',
                $this->_protocol,
                $statusCode,
                constant('self::HTTP_STATUS_CODE_MSG_' . $statusCode)
            );
        }
    }

    public function isCliMode()
    {
        return php_sapi_name() === 'cli';
    }

}
