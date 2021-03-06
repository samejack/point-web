<?php
namespace point\web;

class Handler_ExceptionViewer
{

    /**
     * @Autowired
     * @var \point\web\Http_Response
     */
    private $_response;

    /**
     * @Autowired
     * @var \point\web\Http_Request
     */
    private $_request;

    /**
     * @Autowired
     * @var \point\web\Dispatcher
     */
    private $_dispatcher;

    public function exceptionHandler(&$exception)
    {
        // 呼叫 viewer 自行實作的 error render
        $viewer = $this->_dispatcher->getViewer();
        if (!is_null($viewer) && $viewer instanceof Viewer_Interface) {
            if ($viewer->errorHandler($this->_request, $this->_response, $exception) === true) {
                return true;
            }
        }
        return false;
    }
}
