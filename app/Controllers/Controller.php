<?php

namespace App\Controllers;

use App\Auth\Auth;
use App\Validation\Validator;
use Slim\Flash\Messages;
use Slim\Router;
use Slim\Views\Twig;
use Interop\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * @property Auth auth
 * @property Messages flash
 * @property Twig view
 * @property Validator validator
 * @property Router router
 */
class Controller
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function redirect(Response $response, $route, array $params = array())
    {
        return $response->withRedirect($this->router->pathFor($route, $params));
    }

    public function __get($property)
    {
        if ($this->container->{$property}) {
            return $this->container->{$property};
        }
    }
}
