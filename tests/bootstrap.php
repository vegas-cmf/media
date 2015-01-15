<?php
//Test Suite bootstrap
include __DIR__ . "/../vendor/autoload.php";

define('TESTS_ROOT_DIR', dirname(__FILE__));
//define('APP_ROOT', TESTS_ROOT_DIR . '/fixtures');

$configArray = require_once dirname(__FILE__) . '/config.php';

$config = new \Phalcon\Config($configArray);
$di = new \Phalcon\DI\FactoryDefault();

$di->set('config', $config);

$di->set('mongo', function() use ($config) {
    $mongo = new \MongoClient();
    return $mongo->selectDb($config->mongo->db);
}, true);

$di->set('collectionManager', function() {
    return new \Phalcon\Mvc\Collection\Manager();
});

$view = new \Phalcon\Mvc\View();
$view->registerEngines(array(
    '.volt' => function ($this, $di) {
            $volt = new \Phalcon\Mvc\View\Engine\Volt($this, $di);
            $volt->setOptions(array(
                'compiledPath' => TESTS_ROOT_DIR.'/fixtures/cache/',
                'compiledSeparator' => '_'
            ));

            return $volt;
        },
    '.phtml' => 'Phalcon\Mvc\View\Engine\Php'
));

$di->set('view', $view);

\Phalcon\DI::setDefault($di);