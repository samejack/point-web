<?php
namespace point\web;

use \point\core\Bean;

class Dispatcher
{

    const DEFAULT_CONTROLLER = 'Default';

    /**
     * @Autowired
     * @var \point\core\Context
     */
    private $_context;
    
    /**
     * @Autowired
     * @var \point\core\Runtime
     */
    private $_runtime;
    
    /**
     * Router instance
     * @var Router_Interface
     */
    private $_router;

    /**
     * Controller instance reference
     * @var Object
     */
    private $_controller;

    /**
     * ReflectionClass of Controller
     *
     * @var \ReflectionClass
     */
    private $_controllerReflection;

    private $_routePluginId;
    
    /**
     * Render enable status
     * @var boolean
     */
    private $_isRender = true;

    /**
     * Run MVC flow
     */ 
    public function direct(Http_Request &$request, Http_Response &$response, $uri)
    {
        //TODO redirect / forward
        $this->_context->log('Direct to : ' . $uri);
        $this->_runtime->setCurrentPluginId(str_replace('\\', '.', __NAMESPACE__));

        $this->route($request, $uri);

        if ($this->preDispatch($request, $response) === false) {
            $this->render($request, $response);
            $this->_runtime->restoreCurrentPluginId();
            return;
        }

        $this->dispatch($request, $response);

        if ($this->postDispatch($request, $response) === false) {
            $this->render($request, $response);
            $this->_runtime->restoreCurrentPluginId();
            return;
        }
        
        $this->render($request, $response);
        
        $this->_runtime->restoreCurrentPluginId();
    }

    /**
     * Route HTTP request
     *
     * @param Http_Request $request
     * @param string $uri
     * @throws \Exception
     * @return boolean
     */
    public function route(Http_Request &$request, $uri)
    {
        $controllerExtensions = $this->_runtime->getExtension('ControllerPath');
        foreach ($controllerExtensions as $controllerPluginId => &$eachControllerList) {

            foreach ($eachControllerList as $pattern => &$routeConfig) {

                $fixPattern = sprintf('/^%s([^\/]*).*/', preg_quote($pattern, '/'));

                if (preg_match($fixPattern, $uri, $matcheUri) === 0) {
                    continue;
                } else {
                    $config = $this->_runtime->getPluginConfig($controllerPluginId);
                    $fixUri = substr($uri, strlen($pattern));

                    // fix route default
                    if (strlen($fixUri) === 0|| $fixUri === '/') {
                        $fixUri = self::DEFAULT_CONTROLLER;
                    }
                    if (preg_match('/^([^\/]+)(.*)/', $fixUri, $matches) === 0) {
                        $matches[1] = $matcheUri[1];
                        $matches[2] = '';
                    }

                    // Normalization, convet '-' char to file format
                    $controllerClassname = ucwords(str_replace('-', ' ', strtolower($matches[1])));
                    $controllerClassname = str_replace(' ', '', $controllerClassname);

                    $filename = $config['Path'] . $routeConfig['Path'] . '/' . $controllerClassname . '.php';

                    // find controller php file
                    if (is_file($filename)) {
                        $this->_prepareController (
                            $filename,
                            $controllerPluginId,
                            $request,
                            $matches[2],
                            $routeConfig,
                            $controllerClassname
                        );

                        return true;
                    }
                    
                    // If $pattern euqal '/' and try to match default controller
                    if ($pattern === '/') {
                        $controllerClassname = self::DEFAULT_CONTROLLER;
                        $filename = $config['Path'] . $routeConfig['Path'] . '/' . self::DEFAULT_CONTROLLER . '.php';
                        if (is_file($filename)) {
                            $this->_prepareController (
                                $filename,
                                $controllerPluginId,
                                $request,
                                $fixUri,
                                $routeConfig,
                                $controllerClassname
                            );
                            return true;
                        }
                    }
                }
            }
        }
        throw new \Exception(sprintf('Route fail, controller not found. (URI=%s)', $uri));
    }
    
    public function preDispatch (Http_Request &$request, Http_Response &$response)
    {
        $result = true;
        if (method_exists($this->_controller, 'preDispatch')) {
            $this->_context->log('Call preDispatch : ' . get_class($this->_controller));
            $this->_runtime->setCurrentPluginId($this->_routePluginId);
            $result = $this->_controller->preDispatch($request, $response);
            $this->_runtime->restoreCurrentPluginId();
        }
        return $result;
    }

    public function dispatch (Http_Request &$request, Http_Response &$response)
    {
        $this->_context->log('Dispatch request into controller: ' . get_class($this->_controller));
        $this->_runtime->setCurrentPluginId($this->_routePluginId);
        $this->_isRender = $this->_router->invoke($this->_controller, $request, $response);
        $this->_runtime->restoreCurrentPluginId();
        return $this->_isRender;
    }

    public function postDispatch (Http_Request &$request, Http_Response &$response)
    {
        $result = true;
        if (method_exists($this->_controller, 'postDispatch')) {
            $this->_context->log('Call postDispatch');
            $this->_runtime->setCurrentPluginId($this->_routePluginId);
            $result = $this->_controller->postDispatch($request, $response);
            $this->_runtime->restoreCurrentPluginId();
        }
        return $result;
    }
    
    public function render(Http_Request &$request, Http_Response &$response)
    {
        // check render disabled
        if ($this->_isRender === false) {
            $response->sendHeaders();
            return;
        }
        $viewer = $this->getViewer();
        
        if (!is_null($viewer)) {
            $this->_runtime->setCurrentPluginId($this->_routePluginId);
            $viewer->render($request, $response);
            $this->_runtime->restoreCurrentPluginId();
        }
    }
    
    /**
     * Check and route controller
     * 
     * @param string $filename
     * @param string $pluginId
     * @param Http_Request $request
     * @param string $uri
     * @param array $routeConfig
     * @param string $controllerClassname
     * @throws \Exception
     */
    private function _prepareController (&$filename, &$pluginId, Http_Request &$request, &$uri, array &$routeConfig, &$controllerClassname)
    {
        //start plugin
        $this->_runtime->start($pluginId);
        
        // load controller class
        $this->_runtime->setCurrentPluginId($pluginId);
        include_once $filename;
        $this->_runtime->restoreCurrentPluginId();
        
        // append config to ApplicationContext
        $className = str_replace('.', '\\', $pluginId) . '\\' . $routeConfig['Prefix'] . $controllerClassname;
        $beans = array(
            array(
                Bean::CLASS_NAME => $className,
            ),
        );

        // switch current plugin id before
        $this->_runtime->setCurrentPluginId($pluginId);
        $this->_context->addConfiguration($beans);
        $this->_routePluginId = $pluginId;
        $this->_controller = $this->_context->getBeanByClassName($className);
        $this->_controllerReflection = $this->_context->getReflectionClass($className);
        $this->_runtime->restoreCurrentPluginId();
        
        // find route from annotation
        $reflection = new \ReflectionClass($this->_controller);
        $doc = $reflection->getDocComment();
        if ($doc !== false && preg_match('/@Controller\(([a-zA-Z_]+)\)/', $doc, $routes) > 0) {
            $routeClassName = str_replace('.', '\\', $this->_runtime->getCurrentPluginId()) . '\\' . 'Router_'.$routes[1];
            $beanConfig = array(array(Bean::CLASS_NAME => $routeClassName));
            $this->_context->addConfiguration($beanConfig);
            $this->_router = $this->_context->getBeanByClassName($routeClassName);
        } else {
            throw new \Exception('Route not defined in the annotation doc.');
        }
        
        // route action
        if (!$this->_router->route($this->_controller, $request, $uri)) {
            throw new Http_Exception('Route fail, action not found. (Controller=' . get_class($this->_controller) . ')', 404);
        }
    }
    
    /**
     * Use reflection to find viewer from controller object by _viewer property
     * 
     * @return Object
     */
    public function getViewer()
    {
        if (!is_null($this->_controller)) {
            if ($this->_controllerReflection->hasProperty('_viewer')) {
                $reflectionProperty = $this->_controllerReflection->getProperty('_viewer');
                $reflectionProperty->setAccessible(true);
                $viewer = $reflectionProperty->getValue($this->_controller);

                if (is_object($viewer) && method_exists($viewer, 'render')) {
                    return $viewer;
                }
            }
        }
        return null;
    }

    public function getController()
    {
        return $this->_controller;
    }

}
