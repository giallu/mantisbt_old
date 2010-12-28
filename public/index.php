<?php

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    get_include_path(),
)));

/** Zend_Application */
require_once 'Zend/Application.php';

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);


// Support for legacy code, idea from:
// http://www.chrisabernethy.com/zend-framework-legacy-scripts/

$request = new Zend_Controller_Request_Http();
$docroot = $request->get('DOCUMENT_ROOT');
$uri = $request->getPathInfo();
    
if ($uri == '/' ) $uri = '/index.php';

if (is_file($docroot . '/../legacy'. $uri)) {
    ob_start();
    include $docroot. '/../legacy' . $uri;

    $response = new Zend_Controller_Response_Http();
    $response->setBody(ob_get_clean());
    $response->sendResponse();

    exit;

}

$application->bootstrap()
            ->run();