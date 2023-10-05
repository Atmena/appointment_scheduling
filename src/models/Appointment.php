<?php
namespace Models;

class Appointment {
    public $id;
    public $user_id;
    public $start_time;
    public $duration;
    public $participant_name;
    public $participant_email;
    public $participant_phone;
    public $created_at;
}
?>