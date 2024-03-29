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

    private $_responseRawBody;

    private $_statusCode = 200;

    private $_emptyReturn = false;

    /**
     * Response model
     * @var array
     */
    private $_models = array();

    public function __construct ()
    {
        $this->reset();
    }
  
    public function setModel($key, $model)
    {
        $this->_models[$key] = $model;
    }
    
    public function getModel($key = null)
    {
        if (is_null($key)) {
            return $this->_models;
        } else if (array_key_exists($key, $this->_models)) {
            return $this->_models[$key];
        }
        return null;
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

        unset($this->_responseRawBody);
        $this->_responseRawBody = null;

        unset($this->_models);
        $this->_models = [];

        // default header
        $this->addHeader($this->_protocol . ' 200 ' . self::HTTP_STATUS_CODE_MSG_200);
    }

    /**
     * HTTP 301 Moved Permanently
     *
     * @param string $url Redirect URL
     * @param integer $mode 301 | 302
     */
    public function redirect($url, $mode = 302)
    {
        $this->setStatusCode($mode);
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
   
    public function setEmptyReturn($enabled)
    {
        $this->_emptyReturn = (boolean)$enabled;
    }
 
    public function output($data = null)
    {
        $this->sendHeaders();
        if (!is_null($data) && !$this->_emptyReturn) {
            echo $data;
        }
    }
    
    public function getRawBody()
    {
        return $this->_responseRawBody;
    }

    public function setRawBody(&$raw)
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

    public function setCookie(
        $name,
        $value,
        $lifetime = 0,
        $samesite = 'None',
        $path = '/',
        $domain = null,
        $secure = true,
        $httponly = true
    ) {
        if ($lifetime === 0) {
            $expires = '';
        } else {
            $expires = time() + $lifetime;
        }
        $cookirHeader = rawurlencode($name) . '=' . rawurlencode($value)
            . (empty($expires) ? '' : '; expires=' . gmdate('D, d-M-Y H:i:s', $expires) . ' GMT')
            . (empty($path) ? '' : '; path=' . $path)
            . (empty($samesite) ? '' : '; SameSite=' . $samesite)
            . (empty($domain) ? '' : '; domain=' . $domain)
            . (!$secure ? '' : '; secure')
            . (!$httponly ? '' : '; HttpOnly');

        $this->addHeader('Set-Cookie', $cookirHeader);

        //point-sid=akqfk6s5rd1r3oacsum9n7jfsp; expires=Wed, 04-Nov-2020 16:18:27 GMT; Max-Age=5184000; path=/; SameSite=None; secure; HttpOnly
        if (preg_match('/^7\.3.*$/', phpversion()) !== 0) {
            setcookie(
                $name,
                $value,
                [
                    'lifetime' => $lifetime,
                    'domain' => $domain,
                    'secure' => $secure,
                    'httponly' => $httponly,
                    'samesite' => $samesite
                ]
            );
        } else {
            $path = $this->_config['session']['path'];
            setcookie(
                $name,
                $value,
                $lifetime,
                is_null($path) ? null : $path . '; SameSite=' . $samesite,
                $domain,
                $secure,
                $httponly
            );
        }
    }

    public function setStatusCode($statusCode)
    {
        if (defined('self::HTTP_STATUS_CODE_MSG_' . $statusCode)) {
            $message = constant('self::HTTP_STATUS_CODE_MSG_' . $statusCode);
        } else if ($statusCode < 200) {
            $message = 'Informational Responses';
        } else if ($statusCode < 300) {
            $message = 'Success';
        } else if ($statusCode < 400) {
            $message = 'Redirection';
        } else {
            $message = 'Server Error';
        }
        $this->_headers['HTTP'] = sprintf(
            '%s %d %s',
            $this->_protocol,
            $statusCode,
            $message
        );
        $this->_statusCode = $statusCode;
    }

    public function getStatusCode()
    {
        return $this->_statusCode;
    }

    public function isCliMode()
    {
        return php_sapi_name() === 'cli';
    }

}
