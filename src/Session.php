<?php
/**
 * Raven API Framework.
 * Copyright 2024 Enicore Solutions.
 */
namespace Enicore\RavenApi;

/**
 * Manages session data using a singleton pattern, providing convenient methods for storing, retrieving, checking, and
 * removing session values. Ensures that the session is active before each operation.
 *
 * @package Enicore\RavenApi
 */
class Session
{
    use Singleton;

    /**
     * Returns a value from the session if it exists, or a default value if it doesn't.
     *
     * @param string $key The session key to retrieve.
     * @param mixed|null $default The default value to return if the key does not exist.
     * @return mixed The value associated with the session key or the default.
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $this->start();
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Checks if a session key exists.
     *
     * @param string $key The session key to check.
     * @return bool True if the session key exists, false otherwise.
     */
    public function has(string $key): bool
    {
        $this->start();
        return array_key_exists($key, $_SESSION);
    }

    /**
     * Stores a value in the session.
     *
     * @param string $key The session key to set.
     * @param mixed $value The value to store in the session.
     * @return void
     */
    public function set(string $key, mixed $value): void
    {
        $this->start();
        $_SESSION[$key] = $value;
    }

    /**
     * Removes a session key if it exists.
     *
     * @param string $key The session key to remove.
     * @return void
     */
    public function remove(string $key): void
    {
        $this->start();
        unset($_SESSION[$key]);
    }

    /**
     * Starts the session if it hasn't been started yet.
     *
     * @return void
     */
    public function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
}