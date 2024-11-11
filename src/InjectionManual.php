<?php
/**
 * Raven API Framework.
 * Copyright 2024 Enicore Solutions.
 */
namespace Enicore\RavenApi;

/**
 * InjectionManual trait for manually setting dependencies.
 *
 * This trait allows a class to manually set dependencies by directly assigning singleton instances to class properties.
 * It is useful when the class does not rely on dynamic property resolution via `__get()` but still needs to manage
 * dependency injection.
 */
trait InjectionManual
{
    protected Auth $auth;
    protected Code $code;
    protected Database $db;
    protected Request $request;
    protected Response $response;
    protected Router $router;
    protected Session $session;

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
     * Sets the dependencies by assigning the corresponding singleton instances to the class properties. This function
     * is called to initialize the class properties with their respective singleton instances, allowing the class to
     * access various services (e.g., authentication, database, etc.).
     */
    public function setDependencies(): void
    {
        foreach ($this->classes as $key => $class) {
            $this->$key = $class::instance();
        }
    }
}
