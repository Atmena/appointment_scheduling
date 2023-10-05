<?php
namespace Models;

use PDO;

class Appointment {
    private $conn;
    private $table_name = "appointments";

    public $id;
    public $user_id;
    public $start_time;
    public $duration;
    public $participant_name;
    public $participant_email;
    public $participant_phone;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " (user_id, start_time, duration, participant_name, participant_email, participant_phone) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$this->user_id, $this->start_time, $this->duration, $this->participant_name, $this->participant_email, $this->participant_phone]);
    }

    public function list($user_id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . " SET start_time = ?, duration = ?, participant_name = ?, participant_email = ?, participant_phone = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$this->start_time, $this->duration, $this->participant_name, $this->participant_email, $this->participant_phone, $this->id]);
    }

    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$this->id]);
    }
}
?>
