<?php
namespace App;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Models\Appointment;

class AppointmentController {
    private $appointment;

    public function __construct($db) {
        $this->appointment = new Appointment($db);
    }

    private function getData(Request $request) {
        $data = $request->getParsedBody();
        return $data;
    }

    public function create(Request $request, Response $response, array $args): Response {
        $data = $this->getData($request);
        if(empty($data['user_id']) || empty($data['start_time']) || empty($data['duration']) || empty($data['participant_name']) || empty($data['participant_email']) || empty($data['participant_phone'])) {
            $response->getBody()->write(json_encode(array("message" => "Les données fournies sont incomplètes ou invalides.")));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $this->appointment->user_id = $data['user_id'];
        $this->appointment->start_time = $data['start_time'];
        $this->appointment->duration = $data['duration'];
        $this->appointment->participant_name = htmlspecialchars($data['participant_name']);
        $this->appointment->participant_email = htmlspecialchars($data['participant_email']);
        $this->appointment->participant_phone = htmlspecialchars($data['participant_phone']);

        try {
            if($this->appointment->create()) {
                $response->getBody()->write(json_encode(array("message" => "Rendez-vous créé avec succès.")));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
            } else {
                $response->getBody()->write(json_encode(array("message" => "Échec de la création du rendez-vous.")));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
            }
        } catch (Exception $e) {
            $response->getBody()->write(json_encode(array("error" => "Une erreur est survenue.")));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function list(Request $request, Response $response, array $args): Response {
        $user_id = (int) $args['user_id'];

        try {
            $appointments = $this->appointment->list($user_id);
            if(!empty($appointments)) {
                $response->getBody()->write(json_encode($appointments));
                return $response->withHeader('Content-Type', 'application/json');
            } else {
                $data = array('error' => 'Aucun rendez-vous trouvé');
                $response->getBody()->write(json_encode($data));  
                return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
            }
        } catch (Exception $e) {
            $response->getBody()->write(json_encode(array("error" => "Une erreur est survenue.")));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function update(Request $request, Response $response, array $args): Response {
        $data = $this->getData($request);
        $this->appointment->id = $args['id'];
        $this->appointment->start_time = $data['start_time'];
        $this->appointment->duration = $data['duration'];
        $this->appointment->participant_name = htmlspecialchars($data['participant_name']);
        $this->appointment->participant_email = htmlspecialchars($data['participant_email']);
        $this->appointment->participant_phone = htmlspecialchars($data['participant_phone']);

        if($this->appointment->update()) {
            $response->getBody()->write(json_encode(array("message" => "Rendez-vous mis à jour avec succès.")));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } else {
            $response->getBody()->write(json_encode(array("message" => "Échec de la mise à jour du rendez-vous.")));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function delete(Request $request, Response $response, array $args): Response {
        $this->appointment->id = $args['id'];

        if($this->appointment->delete()) {
            $response->getBody()->write(json_encode(array("message" => "Rendez-vous supprimé avec succès.")));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } else {
            $response->getBody()->write(json_encode(array("message" => "Échec de la suppression du rendez-vous.")));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }
}
?>