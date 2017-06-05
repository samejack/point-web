<?php
namespace point\web;

class Router_ClassMethod implements Router_Interface
{
    private $_invokeMethodName;
    
    /**
     * RESTful URI parameters
     * @var array
     */
    private $_restParameters = array();
    
    public function route(&$controller, Http_Request &$request, $uri)
    {
        $subUrls = explode('/', $uri);

        // default route index action
        if ($uri === '' || $uri === '/') {
            $uri = '/index';
        }
        // fix start char
        if (strlen($uri) > 0 && substr($uri, 0 , 1) !== '/') {
            $uri = '/' . $uri;
        }
        if (preg_match('/^\/([^\/]+).*/', $uri, $matches) > 0) {
            // Normalization
            $words = explode('-', strtolower($matches[1]));
            $this->_invokeMethodName = $words[0];

            for ($i = 1, $size = count($words); $i < $size; $i++) {
                $this->_invokeMethodName = $this->_invokeMethodName . ucfirst($words[$i]);
            }
            $this->_invokeMethodName = $this->_invokeMethodName . 'Action';
        } else {
            $this->_invokeMethodName = 'indexAction';
        }
        // TODO 重複 new
        $reflection = new \ReflectionClass($controller);
        $methods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);

        foreach ($methods as &$method) {
            if ($method->getName() === $this->_invokeMethodName) {
                $this->_reflectionMethod = $method;

                foreach ($subUrls as &$subUrl) {
                    if ($subUrl !== '') {
                        $this->_restParameters[] = $subUrl;
                    }
                }
                return true;
            }
        }

        return false;
    }
    
    public function invoke(&$controller, Http_Request &$request, Http_Response &$response)
    {
        // make invoke agrs
        $args = array();
        foreach ($this->_reflectionMethod->getParameters() as $parameter){
            if (strtolower($parameter->getName()) === 'request') {
                $args[] = $request;
            } else if (strtolower($parameter->getName()) === 'response') {
                $args[] = $response;
            }
        }
        $args = array_merge($args, $this->_restParameters);

        // invoke action
        return $this->_reflectionMethod->invokeArgs($controller, $args);
    }
    
    public function getInvokeMethodName()
    {
        if ($this->_reflectionMethod !== null) {
            return $this->_reflectionMethod->getName();
        }
        return null;
    }
}
