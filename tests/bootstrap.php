<?php
error_reporting(E_ALL);
//Test Suite bootstrap
include __DIR__ . "/../vendor/autoload.php";

define('TESTS_ROOT_DIR', dirname(__FILE__));
define('APP_ROOT', TESTS_ROOT_DIR . '/fixtures');

$configArray = require_once TESTS_ROOT_DIR . '/config.php';

$_SERVER['HTTP_HOST'] = 'vegas.dev';
$_SERVER['REQUEST_URI'] = '/';

$config = new \Phalcon\Config($configArray);
$di = new Phalcon\DI\FactoryDefault();


$di->set('config', $config);
$di->set('collectionManager', function() use ($di) {
    return new \Phalcon\Mvc\Collection\Manager();
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

$di->set('mongo', function() use ($config) {
    $mongo = new \MongoClient();
    return $mongo->selectDb($config->mongo->db);
}, true);
$di->set('modelManager', function() use ($di) {
    return new \Phalcon\Mvc\Model\Manager();
}, true);
$di->set('db', function() use ($config) {
    return new \Phalcon\Db\Adapter\Pdo\Mysql($config->db->toArray());
}, true);

Phalcon\DI::setDefault($di);