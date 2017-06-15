<?php

use \point\core\Bean;

$point['Name']         = 'PHP Point Web Framework';
$point['SymbolicName'] = 'point.web';
$point['Enabled']      = true;
$point['AutoStart']    = true;
$point['Activator']    = 'Activity';
$point['Class-Path']   = array('/src');

$point['Beans'] = array(
    array(
        Bean::CLASS_NAME => '\point\web\Dispatcher'
    ),
    array(
        Bean::CLASS_NAME => '\point\web\Http_Request',
        Bean::INCLUDE_PATH => __DIR__ . '/src/Http/Request.php'
    ),
    array(
        Bean::CLASS_NAME => '\point\web\Http_Response',
        Bean::INCLUDE_PATH => __DIR__ . '/src/Http/Response.php'
    ),
    array(
        Bean::CLASS_NAME => '\point\web\Handler_ExceptionViewer'
    ),
);
