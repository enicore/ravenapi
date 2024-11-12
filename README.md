# Raven API Framework

Raven API is a lightweight PHP framework for building APIs quickly and efficiently. It provides essential tools for
handling requests, managing sessions, interacting with databases, routing, and more, all wrapped in a modular structure
ideal for API development.

## Installation

```shell
composer require enicore/ravenapi
```

To copy from a local directory, add the following configuration in your composer.json:

```json
{
    "repositories": [
        {
            "type": "path",
            "url": "path/to/ravenapi",
            "options": {
                "symlink": false
            }
        }
    ],
    "require": {
        "enicore/ravenapi": "*"
    },
    "minimum-stability": "dev",
    "prefer-stable": true
}
```
## Usage

In ```index.php```, initialize the program: 

```php
const API_DIR = __DIR__ . "/";
require __DIR__ . "/vendor/autoload.php";

Enicore\RavenApi\Database::instance([
    "host" => "db_host",
    "port" => "db_port",
    "username" => "db_username",
    "password" => "db_password",
    "database" => "db_database",
    "options" => [],
]);

Enicore\RavenApi\App::instance()->run();
```

### Directory structure

Classes will be auto-loaded according to their namespaces relative to ```APP_DIR```. For example:

```
Controllers\DashboardController -> APP_DIR/controllers/DashboardController.php
Models\User -> APP_DIR/models/User.php 
``` 

### Routing

The requests will be routed to controllers based on the POST or GET values: "controller" and "action". For example:

```
https://api.com?controller=dashboard&action=getpage
```

Controllers will be loaded from the ```Controllers``` namespace. Their class names must be suffixed with 
```Controller```, and the action methods must be suffixed with ```Action```. For example:

```php
namespace Controllers;

class DashboardController extends \Enicore\RavenApi\Controller
{
    public function getPageAction(): array
```

Parameters can be obtained by using the ```Request``` class, for example:

```php
$id = $this->request->get('id');
$id = $this->request->getDecodedId(); // retrieve and decode the encoded id
$all = $this->request->all();
```

The controller action should return an array of parameters that will be passed back to the frontend:

```php
public function getPageAction(): array
{
    return ["hello" => "world"];
}
```

The above result will be returned as:

```json
{
    "success": true,
    "data": {
        "hello": "world"
    }
}
```

You can also use the ```Response``` class to send the same response:

```php
$this->response->success(["hello" => "123"]);
```

To send an error response, use the ```error()``` method (with optional data that can be passed as the second parameter):

```php
$this->response->error("Cannot save record.");
```

Error responses are returned with header 200 just like successful responses, but with the "success" parameter set to 
false. The above response will be returned as:

```json
{
    "success": false,
    "message": "Cannot save record.",
    "data": []
}
```

### Authentication

By default, all routes require authentication. To allow access to a route without authentication, add the 
```#[NoAuth]``` annotation to the method:

```php
#[NoAuth]
public function getPageAction(): array
```

### Dependency Injection

To use dependency injection in your classes, use the ```Injection``` trait:

```php
class MyClass
{
    use Injection;
```

This will make all the framework singletons directly available in the class. The injected classes will be created on
first access. Controllers inheriting from ```Enicore\RavenApi\Controller``` and Models inheriting from
```Enicore\RavenApi\Model``` already use the ```Injection``` trait.

```php
$this->auth     => Enicore\RavenApi\Auth;       // Auth service
$this->code     => Enicore\RavenApi\Code;       // Encoding and encryption
$this->db       => Enicore\RavenApi\Database;   // Database service
$this->request  => Enicore\RavenApi\Request;    // Request handling
$this->response => Enicore\RavenApi\Response;   // Response handling
$this->router   => Enicore\RavenApi\Router;     // Router service
$this->session  => Enicore\RavenApi\Session;    // Session management
```

Injection is done using the magic ```__get()``` method. If your class already uses this method for other purposes, you
can inject the dependencies manually calling ```$this->injectDependencies()``` in the constructor of the class. In this 
case, all the dependencies will be created right away.

```php
class MyClass
{
    use Injection;
    
    public function __construct()
    {
        $this->injectDependencies();
    }
    
    public function __get(string $variable): string
    {
        return "something";
    }
}    
```

To access the dependencies directly, use their ```instance()``` method, for example:

```php
use Enicore\RavenApi\Database;
Database::instance()->getFirst("...");
```

## License

Raven API is licensed under the MIT License. See LICENSE for more information.
