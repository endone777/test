<?php

require 'autoload.php';

use App\Router;

$router = new Router();
$router->get('/', function () {
    echo '🎡';
});
$router->post('/author/add', [\App\Controller\AuthorController::class, 'create']);
$router->post('/author/update', [\App\Controller\AuthorController::class, 'update']);
$router->post('/author/delete', [\App\Controller\AuthorController::class, 'delete']);
$router->get('/author/list', [\App\Controller\AuthorController::class, 'getList']);

$router->post('/magazine/add', [\App\Controller\MagazineController::class, 'create']);
$router->post('/magazine/update', [\App\Controller\MagazineController::class, 'update']);
$router->post('/magazine/delete', [\App\Controller\MagazineController::class, 'delete']);
$router->get('/magazine/list', [\App\Controller\MagazineController::class, 'getList']);

$router->dispatch();

