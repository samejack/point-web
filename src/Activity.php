<?php

namespace point\web;

use \point\core\Bean;

class Activity
{

    /**
     * @Autowired
     * @var \point\core\Context
     */
    private $_context;

    /**
     * @Autowired
     * @var \point\web\Config_Interface
     */
    private $_config;

    public function start()
    {
        if (!is_null($this->_config->getIterator()) && isset($this->_config->getIterator()['system']['timezone'])) {
            date_default_timezone_set($this->_config->getIterator()['system']['timezone']);
        }

        $eventHandleManager = $this->_context->getBeanByClassName('\point\core\EventHandleManager');
        $eventHandleManager->addExceptionHandler(
            $this->_context->getBeanByClassName('\point\web\Handler_ExceptionViewer')
        );

        // get dispatcher and dispatches
        $request = $this->_context->getBeanByClassName('\point\web\Http_Request');
        $response = $this->_context->getBeanByClassName('\point\web\Http_Response');

        // cli mode
        global $argv;
        if (php_sapi_name() === 'cli' &&  count($argv) >= 3) {
            $request->setHttpMethod(strtoupper($argv[1]));
            $request->setUri($argv[2]);
        }

        // dispatch
        $this->_context->getBeanByClassName('\point\web\Dispatcher')->direct($request, $response, $request->getUri());
    }
    
    public function stop()
    {

    }

}
