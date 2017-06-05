<?php
namespace point\web;

interface Router_Interface
{
    /**
     * Route http request
     * 
     * @param Object       $controller  Controller Instance (call by reference)
     * @param Http_Request $request     HTTP Request Object
     * @param String       $uri         URL
     * @return boolean route result
     */
    public function route(&$controller, Http_Request &$request, $uri);
    
    /**
     * Invoke the routing method
     * 
     * @param Object $controller (call by reference)
     * @param Http_Request $request
     * @param Http_Response $response
     * @return boolean render view status
     */
    public function invoke(&$controller, Http_Request &$request, Http_Response &$response);
    /**
     * Get prepare invoke method name of controller
     * 
     * @return string Method name
     */
    public function getInvokeMethodName();
}
