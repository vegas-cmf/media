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

// \Phalcon\Mvc\Collection requires non-static binding of service providers.
class DiProvider
{

    public function resolve(\Phalcon\Config $config)
    {
        $di = new \Phalcon\Di\FactoryDefault();

        $di->set('config', $config);

        $di->set('collectionManager', function() {
            return new \Phalcon\Mvc\Collection\Manager();
        }, true);

        $di->set('mongo', function() use ($config) {
            $mongoConfig = $config->mongo->toArray();

            if (isset($mongoConfig['dsn'])) {
                $hostname = $mongoConfig['dsn'];
                unset($mongoConfig['dsn']);
            } else {
                //obtains hostname
                if (isset($mongoConfig['host'])) {
                    $hostname = 'mongodb://' . $mongoConfig['host'];
                } else {
                    $hostname = 'mongodb://localhost';
                }
                if (isset($mongoConfig['port'])) {
                    $hostname .= ':' . $mongoConfig['port'];
                }
                //removes options that are not allowed in MongoClient constructor
                unset($mongoConfig['host']);
                unset($mongoConfig['port']);
            }
            $dbName = $mongoConfig['dbname'];
            unset($mongoConfig['dbname']);

            $mongo = new \MongoClient($hostname, $mongoConfig);
            return $mongo->selectDb($dbName);
        }, true);

        $di->set('modelManager', function() {
            return new \Phalcon\Mvc\Model\Manager();
        }, true);

        $di->set('db', function() use ($config) {
            return new \Phalcon\Db\Adapter\Pdo\Mysql($config->db->toArray());
        }, true);

        $view = new \Phalcon\Mvc\View();
        $view->registerEngines(array(
            '.volt' => function ($view, $di) {
                $volt = new \Phalcon\Mvc\View\Engine\Volt($view, $di);
                $volt->setOptions(array(
                    'compiledPath' => TESTS_ROOT_DIR.'/fixtures/cache/',
                    'compiledSeparator' => '_'
                ));

                return $volt;
            },
            '.phtml' => 'Phalcon\Mvc\View\Engine\Php'
        ));

        $di->set('view', $view);

        $di->set('filter', '\Vegas\Filter', true);

        \Phalcon\Di::setDefault($di);
    }

}

(new \DiProvider)->resolve($config);