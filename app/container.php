<?php

$container = $app->getContainer();

$container['debug'] = function() {
    return true;
};

$container['view'] = function($container) {
    $dir = dirname(__DIR__);
    $view = new \Slim\Views\Twig($dir . '/app/views', [
        'cache' => $container->debug ? false : $dir . '/tmp/cache',
        'debug' => $container->debug
    ]);

    if ($container->debug) {
        $view->addExtension(new Twig_Extension_Debug());
    }

    $basePath = rtrim(str_ireplace('index.php', '', $container->get('request')->getUri()->getBasePath()), '/');
    $view->addExtension(new Slim\Views\TwigExtension($container->get('router'), $basePath));
    return $view;
};

$container['notFoundHandler'] = function($container) {
    return function ($request, $response) use ($container) {
        return $container->view->render($response, 'pages/404.html', array('session' => $_SESSION));
    };
};

$container['db'] = function() {
    return (new Database());
};

$container['validator'] = function() {
    return (new Validator());
};

$container['checksign'] = function() {
    return (new CheckSign());
};

$container['picture'] = function() {
    return (new Picture());
};

$container['email'] = function() {
    return (new Email());
};

$container['user_picture_dir'] = function() {
    return dirname(__DIR__) . "/app/public/user_picture";
};

$container['match'] = function() {
    return (new CheckMatch());
};

$container['sug'] = function() {
    return (new SugList());
};

$container['order'] = function() {
    return (new Order());
};

$container['filtre'] = function() {
    return (new Filtre());
};

$container['profil'] = function() {
    return (new PostProfil());
};

$container['uprofil'] = function() {
    return (new PostUprofil());
};

$container['refresh'] = function() {
    return (new Refresh());
};
