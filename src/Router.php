<?php
/**
 * Raven API Framework.
 * Copyright 2024 Enicore Solutions.
 */
namespace Enicore\RavenApi;

/**
 * Router class for handling routing logic in the framework. It processes incoming requests, validates routes, handles
 * CSRF token verification, and executes the corresponding controller method with attributes.
 *
 * @package Enicore\RavenApi
 */
class Router
{
    use Injection;
    use Singleton;

    /**
     * Executes the router logic by validating routes, checking authentication, and calling the appropriate controller
     * method.
     *
     * @return void
     */
    public function execute(): void
    {
        // if uploading a file the post data comes in $_POST--let's add $_FILES to it and load it to the request
        if (!empty($_FILES) && !empty($_POST['data'])) {
            $post = json_decode($_POST['data'], true) ?: [];
            $post['files'] = $_FILES;
            $this->request->setAll($post);
        }

        // check if controller and action are set
        if (empty($controller = $this->request->get("controller"))) {
            $this->response->terminate(405, "Missing route [299482]");
        }

        if (empty($method = $this->request->get("action"))) {
            $this->response->terminate(405, "Missing route [299483]");
        }

        // format controller and method
        $controller = "Controllers\\" . ucfirst($controller) . "Controller";
        $method = str_replace("-", "", $method) . "Action";

        // check if controller and method exist and get the function attributes
        if (($attributes = $this->getMethodAttributes($controller, $method)) === false) {
            $this->response->terminate(405, "No route [229338]");
        }

        // if #[NoAuth] not specified, check if user logged in and if the csrf token is correct
        if (!in_array("NoAuth", $attributes)) {
            if (!($userData = $this->auth->getUserData())) {
                $this->response->terminate(401);
            }

            if (!($this->verifyCsrfToken($userData))) {
                $this->response->terminate(403);
            }
        }

        // execute the method in the controller, make sure an array is passed to success
        $result = (new $controller())->$method();
        $this->response->success(is_array($result) ? $result : []);
    }

    /**
     * Retrieves the attributes for a controller method, currently supporting #[NoAuth]. Future support can be added for
     * additional attributes, e.g., #[Admin]. If the controller or method does not exist, returns false.
     *
     * @param string $controller The controller class name.
     * @param string $method The controller method name.
     * @return array|bool The method attributes or false if not found.
     */
    private function getMethodAttributes(string $controller, string $method): array|bool
    {
        $attributes = [];
        try {
            $reflection = new \ReflectionMethod($controller, $method);
            foreach ($reflection->getAttributes() as $attribute) {
                $attributes[] = substr(strrchr($attribute->getName(), "\\"), 1);
            }
        } catch (\Exception) {
            return false;
        }

        return $attributes;
    }

    /**
     * Verifies the CSRF token by checking the Authorization header in the request. The token must match the one stored
     * in the user's data.
     *
     * @param array $userData The user data array containing the token.
     * @return bool Returns true if the CSRF token matches, false otherwise.
     */
    private function verifyCsrfToken(array $userData): bool
    {
        foreach ([$_SERVER, function_exists('apache_request_headers') ? apache_request_headers() : []] as $array) {
            foreach (["Authorization", "authorization", "HTTP_AUTHORIZATION"] as $key) {
                if (isset($array[$key])) {
                    return preg_match('/Bearer\s(\S+)/', trim($array[$key]), $matches) &&
                        $userData['token'] == $matches[1];
                }
            }
        }


        return false;
    }
}
