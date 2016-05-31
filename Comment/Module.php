<?php

namespace Bcore\Comment;

use Phalcon\DiInterface;
use Phalcon\Loader;
use Phalcon\Mvc\View;
use Phalcon\Mvc\ModuleDefinitionInterface;
use Phalcon\Flash\Direct as Flash;
use Phalcon\Flash\Session as FlashSession;

class Module implements ModuleDefinitionInterface
{
    /**
     * Registers an autoloader related to the module
     *
     * @param DiInterface $di
     */
    public function registerAutoloaders(DiInterface $di = null)
    {

        $loader = new Loader();

        $loader->registerNamespaces([
            'Bcore\Comment\Controllers' => __DIR__ . '/controllers/',
            'Bcore\Comment\Models'      => __DIR__ . '/models/',
        ]);

        $loader->register();
    }

    /**
     * Registers services related to the module
     *
     * @param DiInterface $di
     */
    public function registerServices(DiInterface $di)
    {
        /**
         * Read configuration
         */
//        $config = new Ini(APP_PATH  . "/app/frontend/config/config.ini");

        /**
         * Setting up the view component
         */
        $view = $di->getShared('view');
        $view->setViewsDir(__DIR__ . '/Views/');

        $simpleView = $di->getShared('simpleView');
        $simpleView->setViewsDir(BASE_DIR . 'app/modules/Backend/Views/');

        /**
         * Flash service with custom CSS classes
         */
        $di->set('flash', function () {
            return new Flash([
                'error'   => 'alert alert-danger',
                'success' => 'alert alert-success',
                'notice'  => 'alert alert-info',
                'warning' => 'alert alert-warning',
            ]);
        });

        /**
         * Flash session service with custom CSS classes
         */
        $di->set(
            'flashSession',
            function () {
                return new FlashSession([
                    'error'   => 'alert alert-dismissable alert-danger',
                    'success' => 'alert alert-dismissable alert-success',
                    'notice'  => 'alert alert-dismissable alert-info',
                ]);
            }
        );

        /**
         * Database connection is created based in the parameters defined in the configuration file
         */
//        $di['db'] = function () use ($config) {
//            $config = $config->database->toArray();
//
//            $dbAdapter = '\Phalcon\Db\Adapter\Pdo\\' . $config['adapter'];
//            unset($config['adapter']);
//
//            return new $dbAdapter($config);
//        };

//        $simpleView = $di->getShared('simpleView');
//        $simpleView->setViewsDir(__DIR__ . '/Views/');

    }
}
