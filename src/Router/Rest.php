<?php

namespace point\web;

class Router_Rest implements Router_Interface
{

    /**
     * RESTful URI parameters
     * @var array
     */
    private $_restParameters = array();
    
    /**
     * \ReflectionMethod object for invoke task
     * @var ReflectionMethod
     */
    private $_reflectionMethod;

    /**
     * @Autowired
     * @var \point\core\Context
     */
    private $_context;

    public function route(&$controller, Http_Request &$request, $uri)
    {
        // TODO 重複 new
        $reflection = new \ReflectionClass($controller);
        $methods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);

        $this->_context->log('Scan controller : ' . $reflection->getName());

        foreach ($methods as &$method) {
            if (preg_match('/^.+Action$/', $method->getName())) {

                $doc = $method->getDocComment();
                if ($doc !== false) {
                    // mapping http method
                    if (preg_match('/@METHOD\(([A-Z]+)\)/', $doc, $matches) > 0
                        && strtoupper($matches[1]) === $request->getHttpMethod()
                    ) {
                        // mapping url
                        if (strlen($uri) === 0) {
                            $this->_reflectionMethod = $method;
                            $this->_context->log('Find method : ' . $method->getName());
                            return true;
                        } else if (preg_match('/@URI\((.+)\)/', $doc, $matches) > 0) {
                            // 處理 * 符號
                            $matches[1] = str_replace('*', '%ALL%', $matches[1]);
                            // 處理 {}
                            preg_match_all('/\{[^\}]+\}/', $matches[1], $results);
                            $pattern = preg_replace('/\{[^\}]+\}/', '%PARM%', $matches[1]);
                            $pattern = '/^' . str_replace(array('%PARM%', '%ALL%'), array('([^\/]+)', '.*'), preg_quote($pattern, '/')) . '$/';

                            if (preg_match($pattern, $uri, $matches) > 0) {
                                $this->_reflectionMethod = $method;
                                // make REST parameters from URI
                                foreach ($results[0] as $key => &$name) {
                                    $this->_restParameters[substr($name, 1, strlen($name)-2)] = $matches[$key+1];
                                }
                                $this->_context->log('Find method : ' . $method->getName());
                                return true;
                            }
                        }
                    }
                }
            }
        }

        $this->_context->log('Method not found.');

        return false;
    }
    
    public function invoke(&$controller, Http_Request &$request, Http_Response &$response)
    {
        $this->_context->log('Invoke controller');
        // make invoke agrs
        $args = array();
        foreach ($this->_reflectionMethod->getParameters() as $parameter){
            if (strtolower($parameter->getName()) === 'request') {
                $args[] = $request;
            } else if (strtolower($parameter->getName()) === 'response') {
                $args[] = $response;
            } else if (array_key_exists($parameter->getName(), $this->_restParameters)) {
                $args[] = $this->_restParameters[$parameter->getName()];
            } else {
                $args[] = null;
            }
        }

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
