<?php
namespace App\Controller;

use Models\AppointmentNote;
use Models\Database;

class NoteController {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function add($request, $response, $args) {
        $data = $request->getParsedBody();
        $stmt = $this->db->prepare("INSERT INTO appointment_notes (appointment_id, note) VALUES (?, ?)");
        $stmt->execute([$data['appointment_id'], $data['note']]);
        return $response->withStatus(201)->withJson(['message' => 'Note added successfully']);
    }

    public function list($request, $response, $args) {
        $stmt = $this->db->prepare("SELECT * FROM appointment_notes WHERE appointment_id = ?");
        $stmt->execute([$args['appointment_id']]);
        $notes = $stmt->fetchAll();
        return $response->withJson($notes);
    }
}
?>