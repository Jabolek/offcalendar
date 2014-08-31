<?php

class Events {

    /**
     * @var CI_Controller
     */
    private $ci;

    const EMAIL_NOT_FOUND = 'Email does not exist.';
    const INVALID_PASSWORD = 'Invalid password.';

    public function __construct() {

        $this->ci = &get_instance();
    }

    public function getEventById($eventId){
        
        $eventArr = $this->ci->events_model->getEventById($eventId);
        
        return new Event($eventArr);
        
    }
    
    /**
     * @param int $maxStartTimestamp
     * @return Event[]
     */
    public function getEventsForNotificationSend($maxStartTimestamp) {

        $eventsArr = $this->ci->events_model->getEventsForNotificationSend($maxStartTimestamp);

        $events = array();

        foreach ($eventsArr as $e) {

            $event = Event::fromRowArray($e);

            $events[$event->getId()] = $event;
        }

        return $events;
    }

    /**
     * @param int $userId
     * @param int $lastSyncTimestamp
     * @return Event[]
     */
    public function getDbEventsForSynchronization($userId, $lastSyncTimestamp) {

        $eventsArr = $this->ci->events_model->getEventsByUserId($userId, array('last_update_timestamp >' => $lastSyncTimestamp));

        $events = array();

        foreach ($eventsArr as $e) {

            $event = Event::fromRowArray($e);

            $events[$event->getId()] = $event;
        }

        return $events;
    }

    /**
     * @param [] $postEvents
     * @param User $user
     * @return Event[]
     */
    public function getEventsFromPostData($postEvents, User $user) {

        $events = array();

        $postEvents = json_decode($postEvents, true);

        if (!$postEvents) {
            return $events;
        }

        $userId = $user->getId();

        $this->ci->form_validation->set_rules($this->validationRules);

        foreach ($postEvents as $e) {

            $e['user_id'] = $userId;

            $_POST = $e;

            if (!$this->ci->form_validation->run()) {
                $err = validation_errors();
                continue;
            }

            $eventRow = array();

            foreach (array_keys($this->validationRules) as $fieldName) {
                $eventRow[$fieldName] = $this->ci->form_validation->set_value($fieldName);
            }

            $events[] = Event::fromRowArray($eventRow);
        }

        return $events;
    }

    /**
     * @param Event[] $remoteEvents
     * @param Event[] $dbEvents
     * @param int $currTimestamp
     * @return Event[]
     */
    public function synchronize($remoteEvents, $dbEvents, $currTimestamp) {

        $toUpdate = array();

        foreach ($remoteEvents as $remoteEvent) {

            if (!$remoteEvent->hasId()) {

                $remoteEvent->setLastUpdateTimestamp($currTimestamp);

                $remoteEvent->persist();

                $toUpdate[] = $remoteEvent;

                continue;
            }

            $eventId = $remoteEvent->getId();

            if (isset($dbEvents[$eventId])) {

                $dbEvent = $dbEvents[$eventId];

                if ($dbEvent->getRemoteTimestamp() > $remoteEvent->getRemoteTimestamp()) {

                    $dbEvent->setRemoteId($remoteEvent->getRemoteId());

                    $toUpdate[] = $dbEvent;
                } else {

                    $remoteEvent->setLastUpdateTimestamp($currTimestamp);

                    $remoteEvent->persist();

                    $toUpdate[] = $remoteEvent;
                }

                unset($dbEvents[$eventId]);
            }
        }

        foreach ($dbEvents as $dbEvent) {

            $toUpdate[] = $dbEvent;
        }

        return $toUpdate;
    }

    public function getFieldsValidationRules($fields) {

        $config = array();

        foreach ($fields as $fieldName) {
            $config[$fieldName] = $this->validationRules[$fieldName];
        }

        return $config;
    }

    private $validationRules = array(
        'user_id' => array(
            'field' => 'user_id',
            'label' => 'User ID',
            'rules' => 'trim|required|is_natural',
        ),
        'start_timestamp' => array(
            'field' => 'start_timestamp',
            'label' => 'Start Timestamp',
            'rules' => 'trim|required|is_natural',
        ),
        'end_timestamp' => array(
            'field' => 'end_timestamp',
            'label' => 'End Timestamp',
            'rules' => 'trim|required|is_natural',
        ),
        'description' => array(
            'field' => 'description',
            'label' => 'Description',
            'rules' => 'trim|required',
        ),
        'send_notification' => array(
            'field' => 'send_notification',
            'label' => 'Send Notification',
            'rules' => 'required|is_natural',
        ),
        'voided' => array(
            'field' => 'voided',
            'label' => 'Voided',
            'rules' => 'trim|required|is_natural',
        ),
        'remote_id' => array(
            'field' => 'remote_id',
            'label' => 'Remote ID',
            'rules' => 'trim|required|is_natural',
        ),
        'remote_timestamp' => array(
            'field' => 'remote_timestamp',
            'label' => 'Remote Timestamp',
            'rules' => 'trim|required|is_natural',
        ),
        'last_update_timestamp' => array(
            'field' => 'last_update_timestamp',
            'label' => 'Last Update Timestamp',
            'rules' => 'trim|required|is_natural',
        ),
    );

}
