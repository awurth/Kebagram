<?php
$container = $app->getContainer();

/**
* Eloquent
*/
$capsule = new \Illuminate\Database\Capsule\Manager;
$capsule->addConnection($container['settings']['db']);
$capsule->setAsGlobal();
$capsule->bootEloquent();

/**
* Attach Eloquent to $container
*/
$container['db'] = function () use ($capsule) {
    return $capsule;
};

$container['auth'] = function () {
    return new App\Auth\Auth;
};

$container['flash'] = function () {
    return new Slim\Flash\Messages;
};

$container['view'] = function ($container) {
    $view = new Slim\Views\Twig($container['settings']['view']['template_path'], $container['settings']['view']['twig']);

    // Extensions to view
    $view->addExtension(new Slim\Views\TwigExtension(
        $container->router,
        $container->request->getUri()
    ));
    $view->addExtension(new Twig_Extension_Debug());
    $view->addExtension(new App\TwigExtension\DiffForHumans());
    $view->addExtension(new App\TwigExtension\Hashtag($container->router));
    $view->addExtension(new App\TwigExtension\Mentions($container->router));

    $view->getEnvironment()->addGlobal('auth', [
        'check' => $container->auth->check(),
        'user' => $container->auth->user(),
    ]);

    $view->getEnvironment()->addGlobal('flash', $container->flash);

    return $view;
};

/**
* Custom 404
* Override the default Not Found Handler
*/
$container['notFoundHandler'] = function ($c) {
    return function ($request, $response) use ($c) {
        $c['view']->render($response, 'errors/404.twig');
        return $response->withStatus(404);
    };
};

/**
* Custom CSRF fail response
* Throw a "Method not allowed" error message if CSRF check fails.
*/
$container['csrf'] = function () {
    $guard = new Slim\Csrf\Guard();
    $guard->setFailureCallable(function ($request, $response, $next) {
        $request = $request->withAttribute("csrf_status", false);
        return $next($request, $response);
    });
    return $guard;
};

$container['validator'] = function () {
    return new App\Validation\Validator;
};

/**
* Attach controllers to $container
*/
$container['HomeController'] = function ($container) {
    return new App\Controllers\HomeController($container);
};

$container['AuthController'] = function ($container) {
    return new App\Controllers\Auth\AuthController($container);
};

$container['PasswordController'] = function ($container) {
    return new App\Controllers\Auth\PasswordController($container);
};

$container['ProfileController'] = function ($container) {
    return new App\Controllers\ProfileController($container);
};

$container['PictureController'] = function ($container) {
    return new \App\Controllers\PictureController($container);
};

$container['SocialController'] = function ($container) {
    return new \App\Controllers\SocialController($container);
};
