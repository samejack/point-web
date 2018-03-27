<?php

namespace point\web;

use PHPUnit\Framework\TestCase;
use point\core\Context;
use point\core\Bean;

abstract class Test_Controller extends TestCase
{

    /**
     * @var \point\core\Framework
     */
    protected $_framework;

    public function __construct($config = null)
    {
        parent::__construct();

        $context = new Context($config);
        $context->addConfiguration(
            array(
                array(
                    Bean::CLASS_NAME => '\point\core\Framework',
                    Bean::CONSTRUCTOR_ARG => array($config)
                )
            )
        );
        $this->_framework = $context->getBeanByClassName('point\core\Framework');
        $this->_framework->prepare();
        $this->_framework->getRuntime()->resolve('point.web');
    }

    protected function setUp()
    {
        $response = $this->_framework->getContext()->getBeanByClassName('\point\web\Http_Response');
        $response->reset();
    }

    protected function tearDown()
    {
        $request = $this->_framework->getContext()->getBeanByClassName('\point\web\Http_Request');
        $request->reset();
    }

    protected function dispatch()
    {

        ob_start();

        $this->_dispatcher = $this->_framework->getContext()->getBeanByClassName('\point\web\Dispatcher');

        $this->_framework->launch()->destroy();

        $length = ob_get_length();

        $responseBody = ob_get_clean();

        $response = $this->_framework->getContext()->getBeanByClassName('\point\web\Http_Response');
        $response->setResponseRawBody($responseBody);

        return $response;
    }

}
