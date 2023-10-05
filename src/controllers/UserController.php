<?php
namespace App\Controller;

use Models\User;
use Models\Database;
use Firebase\JWT\JWT;

class UserController {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function register($request, $response, $args) {
        $data = $request->getParsedBody();
        $password_hash = password_hash($data['password'], PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("INSERT INTO users (username, password_hash, email) VALUES (?, ?, ?)");
        $stmt->execute([$data['username'], $password_hash, $data['email']]);
        return $response->withStatus(201)->withJson(['message' => 'User registered successfully']);
    }

    public function login($request, $response, $args) {
        $data = $request->getParsedBody();
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$data['username']]);
        $user = $stmt->fetch();
        if ($user && password_verify($data['password'], $user['password_hash'])) {
            $token = JWT::encode(['id' => $user['id'], 'username' => $user['username']], 'your_secret_key');
            return $response->withJson(['token' => $token]);
        }
        return $response->withStatus(401)->withJson(['message' => 'Invalid credentials']);
    }
}
?>