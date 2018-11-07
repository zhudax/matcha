<?php

class Controller {
    private $container;

    public function __construct($container) {
        $this->container = $container;
    }

    public function checkSession() {
        if (!isset($_SESSION))
            session_start();
        if (!isset($_SESSION['uid']) || empty($_SESSION['uid']))
            return 0;
        return 1;
    }

    public function render($response, $file, $params = []) {
        $this->container->view->render($response, $file, $params);
    }

    public function redirect($response, $path, $status = 302) {
        return $response->withStatus($status)->withHeader('Location', $this->container->router->pathFor($path));
    }

    public function flash($type, $message) {
        if (!isset($_SESSION['flash'])) {
            $_SESSION['flash'] = [];
        }
        return $_SESSION['flash'][$type] = $message;
    }

    public function __get($name) {
        return $this->container->get($name);
    }
}
