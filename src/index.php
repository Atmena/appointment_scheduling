<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/vendor/autoload.php';

$app = AppFactory::create();

$jwtMiddleware = function ($request, $handler) {
    $token = $request->getHeader('Authorization')[0] ?? null;
    if (!$token) {
        return new \Slim\Psr7\Response(401);
    }

    try {
        $decoded = \Firebase\JWT\JWT::decode($token, 'your_secret_key', ['HS256']);
        $request = $request->withAttribute('user', $decoded);
        return $handler->handle($request);
    } catch (\Exception $e) {
        return new \Slim\Psr7\Response(401);
    }
};

$app->group('/api', function ($group) use ($jwtMiddleware) {
    $group->post('/login', 'App\Controller\UserController:login');
    $group->post('/appointments', 'App\Controller\AppointmentController:create')->add($jwtMiddleware);
    $group->get('/appointments', 'App\Controller\AppointmentController:list')->add($jwtMiddleware);
    // Ajoutez d'autres routes CRUD pour les rendez-vous ici
});

$app->run();
?>