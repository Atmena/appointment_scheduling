<?php
namespace App;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Models\AppointmentNote;

class NoteController {
    private $note;

    public function __construct($db) {
        $this->note = new AppointmentNote($db);
    }

    private function getData(Request $request) {
        $data = $request->getParsedBody();
        return $data;
    }

    public function add(Request $request, Response $response, array $args): Response {
        $data = $this->getData($request);
        if(empty($data['appointment_id']) || empty($data['note'])) {
            $response->getBody()->write(json_encode(array("message" => "Les données fournies sont incomplètes ou invalides.")));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $this->note->appointment_id = $data['appointment_id'];
        $this->note->note = htmlspecialchars($data['note']);

        try {
            if($this->note->add()) {
                $response->getBody()->write(json_encode(array("message" => "Note ajoutée avec succès.")));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
            } else {
                $response->getBody()->write(json_encode(array("message" => "Échec de l'ajout de la note.")));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
            }
        } catch (Exception $e) {
            $response->getBody()->write(json_encode(array("error" => "Une erreur est survenue.")));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function list(Request $request, Response $response, array $args): Response {
        $appointment_id = (int) $args['appointment_id'];

        try {
            $notes = $this->note->list($appointment_id);
            if(!empty($notes)) {
                $response->getBody()->write(json_encode($notes));
                return $response->withHeader('Content-Type', 'application/json');
            } else {
                $data = array('error' => 'Aucune note trouvée');
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
        $this->note->id = $args['id'];
        $this->note->note = htmlspecialchars($data['note']);

        if($this->note->update()) {
            $response->getBody()->write(json_encode(array("message" => "Note mise à jour avec succès.")));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } else {
            $response->getBody()->write(json_encode(array("message" => "Échec de la mise à jour de la note.")));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function delete(Request $request, Response $response, array $args): Response {
        $this->note->id = $args['id'];

        if($this->note->delete()) {
            $response->getBody()->write(json_encode(array("message" => "Note supprimée avec succès.")));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } else {
            $response->getBody()->write(json_encode(array("message" => "Échec de la suppression de la note.")));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }
}
?>
