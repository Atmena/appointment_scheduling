<?php 
namespace Models;

use PDO;

class User {
    private $conn;
    private $table_name = "users";

    public $id;
    public $username;
    public $password;
    public $password_hash;
    public $email;
    public $phone;
    public $role_id;
    public $last_login;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function register() {
        $this->password_hash = password_hash($this->password, PASSWORD_DEFAULT);
        $query = "INSERT INTO " . $this->table_name . " (username, password_hash, email) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$this->username, $this->password_hash, $this->email]);
    }

    public function login() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE username = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$this->username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user && password_verify($this->password, $user['password_hash'])) {
            return $user;
        }
        return false;
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . " SET username = ?, email = ?, phone = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$this->username, $this->email, $this->phone, $this->id]);
    }

    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$this->id]);
    }

    public function getProfile() {
        $query = "SELECT id, username, email, phone, role_id, last_login, created_at FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$this->id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
