<?php

class Events_model extends CI_Model {

    function __construct() {

        parent::__construct();
    }
    
    function updateEvent($eventId, $toUpdate) {

        $this->db->update('events', $toUpdate, array('id' => $eventId));

        return $this->db->affected_rows();
    }

    function addEvent($event) {

        $this->db->insert('events', $event);

        return $this->db->insert_id();
    }


}

class Event {

    /**
     * @var CI_Controller
     */
    private $_ci;
    private $id = null;
    private $userId;
    private $startTimestamp;
    private $endTimestamp;
    private $durationSeconds;
    private $description;
    private $sendNotification;
    private $voided;
    private $createdTimestamp;
    private $lastUpdateTimestamp;

    /**
     * @param [][] $events
     * @return \Event[]
     */
    public static function fromResultArray($events) {

        if (!$events) {
            return array();
        }
        
        $arr = array();
        
        foreach($events as $e){
            $arr[] = new Event($e);
        }

        return $arr;
    }
    
    /**
     * @param array $event
     * @return \Event|null
     */
    public static function fromRowArray($event) {

        if (!$event) {
            return null;
        }

        return new Event($event);
    }

    function persist() {

        if ($this->id) {
            $this->_ci->events_model->updateEvent($this->id, $this->toDbArray());
        } else {
            $this->id = $this->_ci->events_model->addEvent($this->toDbArray());
        }

        return $this;
    }

    public function toDbArray() {

        return array(
            'user_id' => $this->userId,
            'start_timestamp' => $this->startTimestamp,
            'end_timestamp' => $this->endTimestamp,
            'duration_seconds' => $this->durationSeconds,
            'description' => $this->description,
            'send_notification' => $this->sendNotification,
            'voided' => $this->voided,
            'created_timestamp' => $this->createdTimestamp,
            'last_update_timestamp' => $this->lastUpdateTimestamp,
        );
    }

    public function toApiResponse() {

        $arr = $this->toDbArray();

        return array_merge(array('id' => $this->id), $arr);
    }

    public function __construct($properties = array()) {

        $this->_ci = &get_instance();

        if ($properties) {

            $this->id = (int) $properties['id'];

            $this->userId = (int) $properties['user_id'];

            $this->startTimestamp = (int) $properties['start_timestamp'];
            
            $this->endTimestamp = (int) $properties['end_timestamp'];
            
            $this->durationSeconds = (int) $properties['duration_seconds'];
            
            $this->description = $properties['description'];
            
            $this->sendNotification = (int) $properties['send_notification'];
            
            $this->voided = (int) $properties['voided'];
            
            $this->createdTimestamp = (int) $properties['created_timestamp'];
            
            $this->lastUpdateTimestamp = (int) $properties['last_update_timestamp'];
        }
    }

}
