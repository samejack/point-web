<?php

namespace point\web;

use PHPUnit\Framework\TestCase;
use point\core\Context;
use point\core\Bean;

abstract class Test_Case extends TestCase
{

    /**
     * @var \point\core\Framework
     */
    protected $_framework;

    public function __construct($config = null)
    {
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
        $this->_framework->getRuntime()->setCurrentPluginId('point.web');

        parent::__construct();
    }

}
