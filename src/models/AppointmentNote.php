<?php 
namespace Models;

use PDO;

class AppointmentNote {
    private $conn;
    private $table_name = "appointment_notes";

    public $id;
    public $appointment_id;
    public $note;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function add() {
        $query = "INSERT INTO " . $this->table_name . " (appointment_id, note) VALUES (?, ?)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$this->appointment_id, $this->note]);
    }

    public function list($appointment_id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE appointment_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$appointment_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . " SET note = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$this->note, $this->id]);
    }

    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$this->id]);
    }
}
?>