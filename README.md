# Raven API Framework

Raven API is a lightweight PHP framework for building APIs quickly and efficiently. It provides essential tools for
handling requests, managing sessions, interacting with databases, routing, and more, all wrapped in a modular structure
ideal for API development.

## Features

- Singleton Pattern: Core classes follow the Singleton pattern, ensuring each service is accessed through a single
  instance.

- Dependency Injection: Provides both automatic and manual injection options for flexible dependency management.

- Routing and CSRF Protection: Built-in routing with support for controller-based actions, including CSRF token
  verification.

- Database Access: Simplified, PDO-based database handling with CRUD operations and flexible querying methods.

- Form Handling and Validation: Includes utilities for setting and validating form data for API inputs.

- File Management: Utility functions for handling file uploads, directory management, and MIME type detection.

- Utility Functions: Provides helper functions for string manipulation, URL generation, email validation, and more.

## Installation
```
composer require enicore-labs/ravenapi
```
To copy from a local directory, add the following configuration in your composer.json:
```
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
        "enicore-labs/ravenapi": "*"
    },
    "minimum-stability": "dev",
    "prefer-stable": true
}
```
## Usage
```
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
## License

Raven API is licensed under the MIT License. See LICENSE for more information.
