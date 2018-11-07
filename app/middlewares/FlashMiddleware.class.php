<?php

class FlashMiddleware {
    private $twig;

    public function __construct($twig) {
        $this->twig = $twig;
    }

    public function __invoke($request, $response, $next) {
        $this->twig->addGlobal('flash', isset($_SESSION['flash']) ? $_SESSION['flash'] : []);
        if (isset($_SESSION['flash'])) {
            unset($_SESSION['flash']);
        }
        return $next($request, $response);
    }
}
