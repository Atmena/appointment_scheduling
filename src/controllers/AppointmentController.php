<?php
namespace App\Controller;

use Models\Appointment;
use Models\Database;

class AppointmentController {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function create($request, $response, $args) {
        $data = $request->getParsedBody();
        $stmt = $this->db->prepare("INSERT INTO appointments (user_id, start_time, duration, participant_name, participant_email, participant_phone) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$data['user_id'], $data['start_time'], $data['duration'], $data['participant_name'], $data['participant_email'], $data['participant_phone']]);
        return $response->withStatus(201)->withJson(['message' => 'Appointment created successfully']);
    }

    public function list($request, $response, $args) {
        $stmt = $this->db->prepare("SELECT * FROM appointments WHERE user_id = ?");
        $stmt->execute([$args['user_id']]);
        $appointments = $stmt->fetchAll();
        return $response->withJson($appointments);
    }

    // ... Ajoutez les méthodes update, delete, etc.
}

?>