<?php
namespace point\web;

interface Viewer_Interface
{
    public function setModel($key, $model);
    
    public function getModel($key);
    
    public function removeModel($key);
    
    public function render(Http_Request &$request, Http_Response &$response);

    /**
     * @param Http_Request $request
     * @param Http_Response $response
     * @param Exception $exception
     * @return boolean return true let the error chain stop
     */
    public function errorHandler(
        Http_Request &$request,
        Http_Response &$response,
        &$exception
    );
}
