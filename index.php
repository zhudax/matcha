<?php

require "vendor/autoload.php";
require "app/controllers/Controller.class.php";
require "app/controllers/GetPages.class.php";
require "app/controllers/PostPages.class.php";
require "app/middlewares/FlashMiddleware.class.php";
require "app/models/Database.class.php";
require "app/models/Validator.class.php";
require "app/models/CheckSign.class.php";
require "app/models/Picture.class.php";
require "app/models/Email.class.php";
require "app/models/CheckMatch.class.php";
require "app/models/SugList.class.php";
require "app/models/Order.class.php";
require "app/models/Filtre.class.php";
require "app/models/PostProfil.class.php";
require "app/models/PostUprofil.class.php";
require "app/models/Refresh.class.php";

if (!isset($_SESSION))
    session_start();

$app = new \Slim\App([
		'settings' => [
			'displayErrorDetails' => true
		]
]);

require("app/container.php");

$app->add(new FlashMiddleware($container->view->getEnvironment()));

require("app/controllers/pagesController.php");

$app->run();
