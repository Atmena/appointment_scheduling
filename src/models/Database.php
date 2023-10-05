<?php
namespace Models;

use PDO;

class Database {
    private static $instance = null;
    private $conn;

    private function __construct() {
        $this->conn = new PDO("mysql:host=localhost;dbname=tech_db", "theFirstUser", "theFirstUser1234");
    }

    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->conn;
    }
}
?>