<?php
/**
 * Raven API Framework.
 * Copyright 2024 Enicore Solutions.
 */
namespace Enicore\RavenApi;

/**
 * The Injection trait provides automatic access to singleton instances of key classes within the Raven API framework.
 * It maps class names to service aliases and ensures that each service is accessed via a single instance.
 */
trait Injection
{
    private array $classes = [
        "auth" => "Enicore\\RavenApi\\Auth",         // Auth service
        "code" => "Enicore\\RavenApi\\Code",         // Code handling service
        "db" => "Enicore\\RavenApi\\Database",       // Database service
        "request" => "Enicore\\RavenApi\\Request",   // Request handling service
        "response" => "Enicore\\RavenApi\\Response", // Response handling service
        "router" => "Enicore\\RavenApi\\Router",     // Router service
        "session" => "Enicore\\RavenApi\\Session",   // Session management service
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
        if (array_key_exists($name, $this->classes)) {
            return $this->classes[$name]::instance();
        }

        return null;
    }
}
