<?php
namespace point\web;

class Http_Request 
{
    /**
     * parameters of http request
     * @var array
     */
    private $_params = null;
    /**
     * auto marshal model 
     * @var array
     */
    private $_model = null;
    /**
     * headers of http request
     * @var array
     */
    private $_headers = array();
    /**
     * uri information
     * @var string
     */
    private $_uri = '/';
    
    /**
     * Http request method
     * @var string
     */
    private $_httpMethod = 'GET';

    /**
     * Http request RAW message body
     * @var string
     */
    private $_rawBody = null;

    /**
     * Remote IP Address
     * @var string
     */
    private $_ipAddress = null;

    private $_requestId = null;

    public function __construct(
        array $params = null,
        array $headers = null,
        $uri = null,
        $httpMethod = null
    ) {
        $this->reset($params, $headers, $uri, $httpMethod);
    }

    public function reset(
        array $params = null,
        array $headers = null,
        $uri = null,
        $httpMethod = null
    ) {
        if (!is_null($params) && is_array($params)) {
            $this->_params = $params;
        } else if (isset($_REQUEST) && count($_REQUEST) > 0) {
            $this->_params = $_REQUEST;
        } else {
            $this->_params = array();
        }

        if (is_null($headers) && function_exists('getallheaders')) {
            $this->_headers = getallheaders();
        } else if (is_array($headers)) {
            $this->_headers = $headers;
        }

        if (is_null($uri) && isset($_SERVER['REDIRECT_URL'])) {
            $this->setUri($_SERVER['REDIRECT_URL']);
        } else if (is_string($uri)) {
            $this->setUri($uri);
        }

        if (is_null($httpMethod) && isset($_SERVER['REQUEST_METHOD'])) {
            $this->setHttpMethod($_SERVER['REQUEST_METHOD']);
        } else  if (is_string($httpMethod)) {
            $this->setHttpMethod($httpMethod);
        }

        $this->_ipAddress = null;

        if (is_null($this->_requestId)) {
            $httpRequestId = $this->getServerParam('HTTP_X_REQUEST_ID');
            if (!is_null($httpRequestId)) {
                $this->_requestId = $httpRequestId;
            } else {
                $this->_requestId = Utility_String::generateURLSafeRand(20);
            }
        }
    }

    public function getHttpMethod()
    {
        return $this->_httpMethod;
    }

    public function setHttpMethod($method)
    {
        $this->_httpMethod = strtoupper($method);
    }

    public function getHeader($key = null)
    {
        if (is_null($key)) {
            return $this->_headers;
        } else if (isset($this->_headers[$key])) {
            return $this->_headers[$key];
        }
        return null;
    }
    
    public function setParam($key, $value)
    {
        $this->_params[$key] = $value;
    }
    
    public function getParam($key = null)
    {
        if (is_null($key)) {
            return $this->_params;
        } else if (isset($this->_params[$key])) {
            return $this->_params[$key];
        }
        return null;
    }

    public function getParams()
    {
        return $this->_params;
    }
    
    public function getUri()
    {
        return $this->_uri;
    }

    public function getIpAddress()
    {
        if (is_null($this->_ipAddress)) {
            $serverParams = $this->getServerParams();
            $conditionHeaders = array(
                'HTTP_X_FORWARDED_FOR',
                'HTTP_X_CLIENT_IP',
                'HTTP_X_CLUSTER_CLIENT_IP',
                'HTTP_CLIENT_IP',
                'REMOTE_ADDR'
            );
            foreach ($conditionHeaders as &$header) {
                if (isset($serverParams[$header])) {
                    $this->_ipAddress = $serverParams[$header];
                }
            }
        }
        return $this->_ipAddress;
    }

    public function getRequestId()
    {
        return $this->_requestId;
    }

    public function setRequestId($requestId)
    {
        $this->_requestId = $requestId;
    }

    public function getProtocol()
    {
        $serverParams = $this->getServerParams();
        foreach (array('HTTP_X_FORWARDED_PROTO', 'SERVER_PROTOCOL') as $header) {
           if (isset($serverParams[$header])) {
               return strpos($serverParams[$header], 'HTTPS') === 0 ? 'https' : 'http';
           }
        }
        return 'http';
    }

    public function setUri($uri)
    {
        $queryString = parse_url($uri, PHP_URL_QUERY);
        if (!is_null($queryString)) {
            parse_str($queryString, $this->_params);
            $uri = parse_url($uri, PHP_URL_PATH);
        }
        $this->_uri = $uri;
    }

    public function getRawBody ()
    {
        if (is_null($this->_rawBody)) {
            $this->_rawBody = @file_get_contents('php://input');
        }
        return $this->_rawBody;
    }
    
    private function _makeModel ()
    {
        // auto parse by content type
        if (array_key_exists('Content-Type', $this->_headers)) {
            // fix content-type parameter
            $contentTypeParams = explode(';', $this->_headers['Content-Type']);
            // parse
            switch ($contentTypeParams[0]) {
                case 'text/json':
                case 'application/json':
                case 'application/xjson':
                    return json_decode($this->getRawBody(), true);
                    break;
                default:
                    return $_REQUEST;
                    break;
            }
        }
        return $_REQUEST;
    }
    
    public function setModel ($model)
    {
        $this->_model = $model;
    }
    
    public function getModel ()
    {
        if (is_null($this->_model)) {
            $this->_model = $this->_makeModel();
        }
        return $this->_model;
    }

    /**
     * 取得指定 Server 資訊 (Apache in PHP $_SERVER)
     * 
     * @param string $name 名稱
     * @return string
     */
    public function getServerParam ($name=null)
    {
        if (array_key_exists($name, $_SERVER)) {
            return $_SERVER[$name];
        }
        return null;
    }
    
    /**
     * 取得所有 Server 資訊 (Apache in PHP $_SERVER)
     *
     * @return array
     */
    public function getServerParams ()
    {
        return $_SERVER;
    }

    public function isCliMode()
    {
        return php_sapi_name() === 'cli';
    }
}
