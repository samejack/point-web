<?php

namespace point\web;

use \point\web\Http_Request;
use \point\web\Http_Response;
use \point\web\Http_Exception;

class Viewer_Json implements Viewer_Interface
{
    /**
     * Response model
     * @var array
     */
    private $_models = array();
    
    public function setModel($key, $model)
    {
        $this->_models[$key] = $model;
    }
    
    public function getModel($key)
    {
        if (array_key_exists($key, $this->_models)) {
            return $this->_models[$key];
        }
        return null;
    }
    
    public function removeModel($key)
    {
        if (array_key_exists($key, $this->_models)) {
            unset($this->_models[$key]);
        }
    }
    
    public function render(Http_Request &$request, Http_Response &$response)
    {
        $response->addHeader('Content-Type', 'application/json');
        if (!is_null($this->_models)) {
            $outputData = self::marshal($this->_models);
            $response->addHeader('Content-length', strlen($outputData));
            $response->output($outputData);
        } else {
            $response->output();
        }
    }

    public function errorHandler(
        Http_Request &$request,
        Http_Response &$response,
        &$exception
    ) {
        $data = array('msg' => $exception->getMessage());
        $response->setStatusCode($exception->getCode());
        $response->output(self::marshal($data));
    }

    public static function marshal (&$model)
    {
        return json_encode($model);
    }
}
