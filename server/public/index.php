<?php declare(strict_types=1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../settings.php';

$app = AppFactory::create();

$app->addErrorMiddleware(DEBUG, true, true);

function fetch($message) {
    $conn = pg_connect('host=' . DB_HOST . ' dbname=airport_booking user=main password=password')
            or die('Could not connect to PostgreSQL');
    $query = "SELECT '$message' AS message";
    $result = pg_query($conn, $query);
    $row = pg_fetch_assoc($result);

    return $row;
}

$app->group('/v1', function (RouteCollectorProxy $group) {
    $group->get('/hello/{name}', function (Request $request, Response $response, array $args) {
        $payload = json_encode(array('name' => $args['name'], 'debug' => DEBUG, 'database' => fetch('Hello from PostgreSQL!')));
        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
    });
});

$app->run();
