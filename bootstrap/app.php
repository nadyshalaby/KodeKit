<?php

class App {

    public function __construct() {
        //starting the sessions
        session_start();

        //autoloading classes
        require_once 'vendor/autoload.php';

        //loading the aliases for classes
        $this->loadAliases();

        //building the database schema if the dbrestore = true
        if (!App\Libs\Concretes\DB::getInstance()->buildDB()) {
            throw new Exception("Error Processing Database Restore", 1);
        }

        // setting up routes to pages
        $route = new App\Libs\Concretes\Router;
        require_once 'app/Http/routes.php';
    }

    private function loadAliases() {
        $aliases = include_once 'config/alias.php';
        foreach ($aliases as $alias => $class) {
            if (class_exists($class)) {
                class_alias($class, $alias);
            }
        }
    }

}
