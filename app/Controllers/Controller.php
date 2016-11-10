<?php

namespace App\Controllers;

use App\Auth\Auth;
use App\Validation\Validator;
use Slim\Exception\NotFoundException;
use Slim\Flash\Messages;
use Slim\Router;
use Slim\Views\Twig;
use Interop\Container\ContainerInterface;
use Psr\Http\Message\RequestInterface as Request;
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
    /**
     * Slim application container
     *
     * @var ContainerInterface
     */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Redirect to route with params
     *
     * @param Response $response
     * @param string $route
     * @param array $params
     * @return Response
     */
    public function redirect(Response $response, $route, array $params = array())
    {
        return $response->withRedirect($this->router->pathFor($route, $params));
    }

    /**
     * Redirect to url
     *
     * @param Response $response
     * @param string $url
     * @return Response
     */
    public function redirectTo(Response $response, $url)
    {
        return $response->withRedirect($url);
    }

    /**
     * Create new NotFoundException
     *
     * @param Request $request
     * @param Response $response
     * @return NotFoundException
     */
    public function notFoundException(Request $request, Response $response)
    {
        return new NotFoundException($request, $response);
    }

    public function __get($property)
    {
        if ($this->container->{$property}) {
            return $this->container->{$property};
        }
    }
}
