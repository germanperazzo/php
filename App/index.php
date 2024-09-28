
<?php

require __DIR__ . '/vendor/autoload.php';

use Slim\Factory\AppFactory;


$app = AppFactory::create();


$app->addBodyParsingMiddleware();


//$app->addRoutingMiddleware();

require __DIR__ . '/src/routes/routes.php';


$app->run();

