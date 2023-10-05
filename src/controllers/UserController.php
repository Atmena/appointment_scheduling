<?php
namespace App;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Models\User;
use Firebase\JWT\JWT;

class UserController {
    private $user;

    public function __construct($db) {
        $this->user = new User($db);
    }

    private function getData(Request $request) {
        $data = $request->getParsedBody();
        return $data;
    }

    public function register(Request $request, Response $response, array $args): Response {
        $data = $this->getData($request);
        if(empty($data['username']) || empty($data['password']) || empty($data['email'])) {
            $response->getBody()->write(json_encode(array("message" => "Les données fournies sont incomplètes ou invalides.")));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $this->user->username = htmlspecialchars($data['username']);
        $this->user->password = $data['password'];
        $this->user->email = htmlspecialchars($data['email']);

        try {
            if($this->user->register()) {
                $response->getBody()->write(json_encode(array("message" => "Utilisateur enregistré avec succès.")));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
            } else {
                $response->getBody()->write(json_encode(array("message" => "Échec de l'enregistrement de l'utilisateur.")));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
            }
        } catch (Exception $e) {
            $response->getBody()->write(json_encode(array("error" => "Une erreur est survenue.")));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function login(Request $request, Response $response, array $args): Response {
        $data = $this->getData($request);
        if(empty($data['username']) || empty($data['password'])) {
            $response->getBody()->write(json_encode(array("message" => "Les données fournies sont incomplètes ou invalides.")));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $this->user->username = htmlspecialchars($data['username']);
        $this->user->password = $data['password'];

        try {
            $user = $this->user->login();
            if($user) {
                $token = JWT::encode(['id' => $user['id'], 'username' => $user['username']], 'your_secret_key');
                $response->getBody()->write(json_encode(['token' => $token]));
                return $response->withHeader('Content-Type', 'application/json');
            } else {
                $response->getBody()->write(json_encode(array("message" => "Identifiants invalides.")));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
            }
        } catch (Exception $e) {
            $response->getBody()->write(json_encode(array("error" => "Une erreur est survenue.")));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function update(Request $request, Response $response, array $args): Response {
        $data = $this->getData($request);
        $this->user->id = $args['id'];
        $this->user->username = htmlspecialchars($data['username']);
        $this->user->email = htmlspecialchars($data['email']);
        $this->user->phone = htmlspecialchars($data['phone']);

        if($this->user->update()) {
            $response->getBody()->write(json_encode(array("message" => "Utilisateur mis à jour avec succès.")));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } else {
            $response->getBody()->write(json_encode(array("message" => "Échec de la mise à jour de l'utilisateur.")));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function delete(Request $request, Response $response, array $args): Response {
        $this->user->id = $args['id'];

        if($this->user->delete()) {
            $response->getBody()->write(json_encode(array("message" => "Utilisateur supprimé avec succès.")));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } else {
            $response->getBody()->write(json_encode(array("message" => "Échec de la suppression de l'utilisateur.")));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function profile(Request $request, Response $response, array $args): Response {
        $this->user->id = $args['id'];
        $profile = $this->user->getProfile();

        if($profile) {
            $response->getBody()->write(json_encode($profile));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } else {
            $response->getBody()->write(json_encode(array("message" => "Profil non trouvé.")));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }
    }
}
?>