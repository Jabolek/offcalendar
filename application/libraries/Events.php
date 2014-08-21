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

    private $syncData = array(
        'add' => array(),
        'update' => array(),
    );

    /**
     * @param int $userId
     * @param int $lastSyncTimestamp
     * @return Event[]
     */
    public function getDbEventsForSynchronization($userId, $lastSyncTimestamp) {

        $eventsArr = $this->ci->events_model->getEventsByUserId($userId, array('last_sync_timestamp' > $lastSyncTimestamp));

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
    public function synchronize($remoteEvents, $dbEvents, $currTimestamp, &$toAdd, &$toUpdate) {

        foreach ($remoteEvents as $remoteEvent) {

            if (!$remoteEvent->hasId()) {

                $remoteEvent->setLastUpdateTimestamp($currTimestamp);

                $remoteEvent->persist();

                $toUpdate[] = $remoteEvent;

                continue;
            }

            $remoteEventId = $remoteEvent->getId();

            if (isset($dbEvents[$remoteEventId])) {

                $dbEvent = $dbEvents[$remoteEventId];

                if ($dbEvent->getRemoteTimestamp() > $remoteEvent->getRemoteTimestamp()) {

                    $dbEvent->setRemoteId($remoteEvent->getRemoteId());

                    $toUpdate[] = $dbEvent;
                } else {

                    $remoteEvent->setLastUpdateTimestamp($currTimestamp);

                    $remoteEvent->persist();

                    $toUpdate[] = $remoteEvent;
                }

                unset($dbEvents[$remoteEventId]);
            }
        }

        foreach ($dbEvents as $dbEvent) {

            $toAdd[] = $dbEvent;
        }
    }

    private function sortById(&$todos) {

        $data = array();

        foreach ($todos as &$t) {
            $t['timeStamp'] = (int) $t['timeStamp'];
            $t['last_update'] = (int) $t['last_update'];
            $t['active'] = (int) $t['active'];
            $data[(int) $t['timeStamp']] = $t;
        }

        return $data;
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
        'duration_seconds' => array(
            'field' => 'duration_seconds',
            'label' => 'Duration Seconds',
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
            'rules' => 'trim|required|is_natural',
        ),
        'voided' => array(
            'field' => 'voided',
            'label' => 'Voided',
            'rules' => 'trim|required|is_natural',
        ),
        'created_timestamp' => array(
            'field' => 'created_timestamp',
            'label' => 'Created Timestamp',
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
