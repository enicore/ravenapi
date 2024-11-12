<?php
/**
 * Raven API Framework.
 * Copyright 2024 Enicore Solutions.
 */
namespace Enicore\RavenApi;

/**
 * The Injection trait provides automatic access to singleton instances of key classes within the Raven API framework.
 * It maps class names to service aliases and ensures that each service is accessed via a single instance.
 *
 * @property-read Auth $auth
 * @property-read Code $code
 * @property-read Database $db
 * @property-read Request $request
 * @property-read Response $response
 * @property-read Router $router
 * @property-read Session $session
 */
trait Injection
{
    private array $injectionClasses = [
        "auth" => "Enicore\\RavenApi\\Auth",
        "code" => "Enicore\\RavenApi\\Code",
        "db" => "Enicore\\RavenApi\\Database",
        "request" => "Enicore\\RavenApi\\Request",
        "response" => "Enicore\\RavenApi\\Response",
        "router" => "Enicore\\RavenApi\\Router",
        "session" => "Enicore\\RavenApi\\Session",
    ];

    /**
     * Magic method to catch calls to non-existing properties (like $this->auth) and return the corresponding
     * singleton class. It checks if the requested service exists in the $classes array and returns the singleton
     * instance for that service.
     *
     * @param string $name The service alias being accessed (e.g., 'auth', 'db').
     * @return object|null Returns the singleton instance of the service if it exists, or null if it's not found.
     */
    public function __get(string $name): ?object
    {
        return array_key_exists($name, $this->injectionClasses) ? $this->injectionClasses[$name]::instance() : null;
    }

    /**
     * Sets the dependencies by assigning the corresponding singleton instances to the class properties. This function
     * is called to initialize the class properties with their respective singleton instances, allowing the class to
     * access various services (e.g., authentication, database, etc.).
     */
    public function injectDependencies(): void
    {
        foreach ($this->injectionClasses as $key => $class) {
            $this->$key = $class::instance();
        }
    }
}
