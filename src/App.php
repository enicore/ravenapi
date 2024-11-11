<?php
/**
 * Raven API Framework.
 * Copyright 2024 Enicore Solutions.
 */
namespace Enicore\RavenApi;


/**
 * Manages application configuration and initialization, including setting up the project root directory and autoloading
 * classes based on their namespace and class name. The class also provides a method to run the application by invoking
 * the router's execute method.
 *
 * @property object|null $router
 * @package Enicore\RavenApi
 */
class App
{
    use Injection;
    use Singleton;

    /**
     * App constructor. Initializes the application by defining the project root directory if it's not already defined,
     * and registering an autoloader to map class namespaces to file paths.
     */
    public function __construct()
    {
        // If the project root directory is not defined, define it using the backtrace to find the first file.
        if (!defined("API_DIR")) {
            $bt = debug_backtrace();
            define("API_DIR", dirname(array_pop($bt)['file']) . DIRECTORY_SEPARATOR);
        }

        // Register an autoloader that maps class namespaces to directory paths and loads the appropriate class file.
        spl_autoload_register(function($class) {
            $array = explode('\\', $class);
            $name = array_pop($array); // extract the class name
            $path = API_DIR . strtolower(implode(DIRECTORY_SEPARATOR, $array)) . DIRECTORY_SEPARATOR . "$name.php"; // Build the path to the class file
            file_exists($path) && require_once $path;
        });
    }

    /**
     * Executes the application by invoking the router's execute method.
     *
     * @return void
     */
    public function run(): void
    {
        $this->router->execute();
    }
}
