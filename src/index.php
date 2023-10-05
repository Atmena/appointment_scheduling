<?php
// ------------------------------
// Importation des dépendances et configuration
// ------------------------------
require 'vendor/autoload.php';
require 'config.php';

use Slim\Factory\AppFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

// ------------------------------
// Création de l'application Slim
// ------------------------------
$app = AppFactory::create();

// ------------------------------
// Configuration de la connexion à la base de données
// ------------------------------
try {
    $db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASS);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données: " . $e->getMessage());
}

// ------------------------------
// Ajout du middleware pour analyser le corps de la requête
// ------------------------------
$app->addBodyParsingMiddleware();

// ------------------------------
// Middleware pour gérer les headers CORS
// ------------------------------
$app->add(function ($request, $handler) {
    $response = $handler->handle($request);
    return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
});

// ------------------------------
// Définition des routes pour UserController
// ------------------------------
$app->group('/api/users', function ($group) use ($db) {
    $controller = new \App\UserController($db);
    $group->post('/register', [$controller, 'register']);
    $group->post('/login', [$controller, 'login']);
    $group->get('/profile/{id}', [$controller, 'profile']);
});

// ------------------------------
// Définition des routes pour AppointmentController
// ------------------------------
$app->group('/api/appointments', function ($group) use ($db) {
    $controller = new \App\AppointmentController($db);
    $group->post('', [$controller, 'create']);
    $group->get('/{user_id}', [$controller, 'list']);
    $group->put('/{id}', [$controller, 'update']);
    $group->delete('/{id}', [$controller, 'delete']);
});

// ------------------------------
// Définition des routes pour NoteController
// ------------------------------
$app->group('/api/notes', function ($group) use ($db) {
    $controller = new \App\NoteController($db);
    $group->post('', [$controller, 'add']);
    $group->get('/{appointment_id}', [$controller, 'list']);
    $group->put('/{id}', [$controller, 'update']);
    $group->delete('/{id}', [$controller, 'delete']);
});

// ------------------------------
// Définition des routes pour l'authentification
// ------------------------------
$app->group('/auth', function ($group) {
    // Route pour le tableau de bord
    $group->get('/dashboard', function ($request, $response, $args) {
        ob_start();
        require 'auth/dashboard.php';
        $html = ob_get_clean();
        $response->getBody()->write($html);
        return $response;
    });

    // Route pour la connexion
    $group->get('/login', function ($request, $response, $args) {
        ob_start();
        require 'auth/connection.php';
        $html = ob_get_clean();
        $response->getBody()->write($html);
        return $response;
    });

    // Route pour la confirmation
    $group->get('/confirmation', function ($request, $response, $args) {
        ob_start();
        require 'auth/confirmation.php';
        $html = ob_get_clean();
        $response->getBody()->write($html);
        return $response;
    });

    // Route pour l'inscription
    $group->get('/signup', function ($request, $response, $args) {
        ob_start();
        require 'auth/signup.php';
        $html = ob_get_clean();
        $response->getBody()->write($html);
        return $response;
    });
});

// ------------------------------
// Gestionnaire d'erreurs personnalisé
// ------------------------------
$customErrorHandler = function (
    Request $request,
    Throwable $exception,
    bool $displayErrorDetails,
    bool $logErrors,
    bool $logErrorDetails
) use ($app) {
    $payload = ['error' => $exception->getMessage()];
    $response = $app->getResponseFactory()->createResponse();
    $response->getBody()->write(json_encode($payload, JSON_UNESCAPED_UNICODE));
    return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
};

// ------------------------------
// Ajout du middleware d'erreur
// ------------------------------
$errorMiddleware = $app->addErrorMiddleware(true, true, true);
$errorMiddleware->setDefaultErrorHandler($customErrorHandler);

// ------------------------------
// Démarrage de l'application Slim
// ------------------------------
$app->run();
?>