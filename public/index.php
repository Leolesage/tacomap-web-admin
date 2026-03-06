<?php
declare(strict_types=1);

session_start();

require __DIR__ . '/../app/Core/Env.php';

spl_autoload_register(function (string $class): void {
    $prefix = 'App\\';
    $baseDir = __DIR__ . '/../app/';
    if (strncmp($prefix, $class, strlen($prefix)) !== 0) {
        return;
    }
    $relative = substr($class, strlen($prefix));
    $file = $baseDir . str_replace('\\', '/', $relative) . '.php';
    if (is_file($file)) {
        require $file;
    }
});

if (is_file(__DIR__ . '/../vendor/autoload.php')) {
    require __DIR__ . '/../vendor/autoload.php';
}

use App\Core\Env;
use App\Core\Request;
use App\Core\Router;
use App\Core\Response;
use App\Core\Auth;
use App\Core\Csrf;
use App\Core\View;
use App\Controllers\AuthController;
use App\Controllers\TacosPlaceController;

Env::load(__DIR__ . '/../.env');

$config = require __DIR__ . '/../config/config.php';
View::setApiPublicUrl($config['api']['public_url'] ?? '');

set_exception_handler(function (Throwable $e): void {
    Response::abort(500, 'An unexpected error occurred.');
});

$request = Request::capture();
$router = new Router();

$authController = new AuthController($config);
$tacosPlaceController = new TacosPlaceController($config);

$csrfMiddleware = [Csrf::class, 'handle'];
$authMiddleware = [Auth::class, 'handle'];

$router->add('GET', '/login', [$authController, 'showLogin']);
$router->add('POST', '/login', [$authController, 'login'], [$csrfMiddleware]);
$router->add('POST', '/logout', [$authController, 'logout'], [$csrfMiddleware]);

$router->add('GET', '/', [$tacosPlaceController, 'home']);
$router->add('GET', '/tacos-places', [$tacosPlaceController, 'publicIndex']);
$router->add('GET', '/tacos-places/search', [$tacosPlaceController, 'publicSearch']);
$router->add('GET', '/tacos-places/{id}', [$tacosPlaceController, 'publicShow']);

$router->add('GET', '/admin/tacos-places', [$tacosPlaceController, 'index'], [$authMiddleware]);
$router->add('GET', '/admin/tacos-places/search', [$tacosPlaceController, 'search'], [$authMiddleware]);
$router->add('GET', '/admin/tacos-places/create', [$tacosPlaceController, 'create'], [$authMiddleware]);
$router->add('POST', '/admin/tacos-places', [$tacosPlaceController, 'store'], [$authMiddleware, $csrfMiddleware]);
$router->add('GET', '/admin/tacos-places/{id}', [$tacosPlaceController, 'show'], [$authMiddleware]);
$router->add('GET', '/admin/tacos-places/{id}/edit', [$tacosPlaceController, 'edit'], [$authMiddleware]);
$router->add('POST', '/admin/tacos-places/{id}/update', [$tacosPlaceController, 'update'], [$authMiddleware, $csrfMiddleware]);
$router->add('POST', '/admin/tacos-places/{id}/delete', [$tacosPlaceController, 'delete'], [$authMiddleware, $csrfMiddleware]);
$router->add('GET', '/admin/tacos-places/{id}/pdf', [$tacosPlaceController, 'pdf'], [$authMiddleware]);

$router->dispatch($request);
