<?php
/**
 * Raven API Framework.
 * Copyright 2024 Enicore Solutions.
 */
namespace Enicore\RavenApi;

/**
 * The Controller class serves as a base class for other controllers using the framework. It provides shared
 * functionality to child controllers, including access to dependency injection features.
 *
 * @package Enicore\RavenApi
 */
class Controller
{
    use Injection;
}
