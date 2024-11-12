<?php
/**
 * Raven API Framework.
 * Copyright 2024 Enicore Solutions.
 */
namespace Enicore\RavenApi;

/**
 * Manages JSON-based AJAX responses for success and error handling, providing utilities for sending responses and
 * terminating execution with a specific HTTP status code.
 *
 * @package Enicore\RavenApi
 */
class Response
{
    use Singleton;

    /**
     * Sends a success AJAX response with optional data.
     *
     * @param array $data The data to include in the response.
     * @return void
     */
    public function success(array $data = []): void
    {
        ob_get_level() && @ob_end_clean();
        header('Content-Type: application/json');
        exit(json_encode(["success" => true, "data" => $data]));
    }

    /**
     * Sends an error AJAX response with a message and optional data.
     *
     * @param string $message The error message to include in the response.
     * @param array $data Additional data to include in the response.
     * @return void
     */
    public function error(string $message = "", array $data = []): void
    {
        ob_get_level() && @ob_end_clean();
        header('Content-Type: application/json');
        exit(json_encode(["success" => false, "message" => $message, "data" => $data]));
    }

    /**
     * Terminates the program with a specific HTTP status code and an optional message.
     *
     * @param int $code The HTTP status code to send.
     * @param string $message The message to include in the response header, if any.
     * @return void
     */
    public function terminate(int $code = 404, string $message = ""): void
    {
        ob_get_level() && @ob_end_clean();

        // don't cache this response
        header('Cache-Control: no-store, no-cache, must-revalidate, proxy-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');

        if ($message) {
            header("HTTP/1.1 $code " . $message);
        } else {
            http_response_code($code);
        }

        exit();
    }
}
