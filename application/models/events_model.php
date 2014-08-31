<?php

class Events_model extends CI_Model {

    function __construct() {

        parent::__construct();
    }
    
    function getEventById($eventId) {

        $query = $this->db->get_where('events', array('id' => $eventId));

        return $query->row_array();
    }

    function updateEvent($eventId, $toUpdate) {

        $this->db->update('events', $toUpdate, array('id' => $eventId));

        return $this->db->affected_rows();
    }

    function addEvent($event) {

        $this->db->insert('events', $event);

        return $this->db->insert_id();
    }

    function getEventsByUserId($userId, $where = array()) {

        $where['user_id'] = $userId;

        $query = $this->db->get_where('events', $where);

        return $query->result_array();
    }

    function getEventsForNotificationSend($maxStartTimestamp) {

        $where = array(
            'voided' => 0,
            'send_notification' => 1,
            'notification_sent' => 0,
            'start_timestamp <' => $maxStartTimestamp,
        );

        $query = $this->db->get_where('events', $where);

        return $query->result_array();
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
    private $description;
    private $sendNotification;
    private $voided;
    private $remoteId = null;
    private $remoteTimestamp;
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

        foreach ($events as $e) {
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

    function notificationSent(){
        
        $this->_ci->events_model->updateEvent($this->id, array('notification_sent' => 1));
        
    }
    
    public function toDbArray() {

        return array(
            'user_id' => $this->userId,
            'start_timestamp' => $this->startTimestamp,
            'end_timestamp' => $this->endTimestamp,
            'description' => $this->description,
            'send_notification' => $this->sendNotification,
            'voided' => $this->voided,
            'remote_timestamp' => $this->remoteTimestamp,
            'last_update_timestamp' => $this->lastUpdateTimestamp,
        );
    }

    public function toApiResponse() {

        $arr = $this->toDbArray();

        $additional = array(
            'id' => $this->id,
        );

        if ($this->remoteId) {
            $additional['remote_id'] = $this->remoteId;
        }

        return array_merge($additional, $arr);
    }

    public function __construct($properties = array()) {

        $this->_ci = &get_instance();

        if ($properties) {

            if (isset($properties['id']) && $properties['id']) {
                $this->id = (int) $properties['id'];
            }

            $this->userId = (int) $properties['user_id'];

            $this->startTimestamp = (int) $properties['start_timestamp'];

            $this->endTimestamp = (int) $properties['end_timestamp'];

            $this->description = (string) $properties['description'];

            $this->sendNotification = (int) $properties['send_notification'];

            $this->voided = (int) $properties['voided'];

            $this->remoteTimestamp = (int) $properties['remote_timestamp'];

            if (isset($properties['remote_id']) && $properties['remote_id']) {
                $this->remoteId = (int) $properties['remote_id'];
            }

            $this->lastUpdateTimestamp = (int) $properties['last_update_timestamp'];
        }
    }

    public function hasId() {

        return ($this->id != 0);
    }

    public function getId() {
        return $this->id;
    }

    public function getUserId() {
        return $this->userId;
    }

    public function getStartTimestamp() {
        return $this->startTimestamp;
    }

    public function getEndTimestamp() {
        return $this->endTimestamp;
    }

    public function getDescription() {
        return $this->description;
    }

    public function getSendNotification() {
        return $this->sendNotification;
    }

    public function getVoided() {
        return $this->voided;
    }

    public function getRemoteId() {
        return $this->remoteId;
    }

    public function getRemoteTimestamp() {
        return $this->remoteTimestamp;
    }

    public function getLastUpdateTimestamp() {
        return $this->lastUpdateTimestamp;
    }

    public function setStartTimestamp($startTimestamp) {
        $this->startTimestamp = $startTimestamp;
    }

    public function setEndTimestamp($endTimestamp) {
        $this->endTimestamp = $endTimestamp;
    }

    public function setDescription($description) {
        $this->description = $description;
    }

    public function setSendNotification($sendNotification) {
        $this->sendNotification = $sendNotification;
    }

    public function setVoided($voided) {
        $this->voided = $voided;
    }

    public function setRemoteId($remoteId) {
        $this->remoteId = $remoteId;
    }

    public function setRemoteTimestamp($remoteTimestamp) {
        $this->remoteTimestamp = $remoteTimestamp;
    }

    public function setLastUpdateTimestamp($lastUpdateTimestamp) {
        $this->lastUpdateTimestamp = $lastUpdateTimestamp;
    }

}
