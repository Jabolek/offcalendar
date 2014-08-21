<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Events_api extends Api_Controller {

    function __construct() {

        parent::__construct('events');
    }

    function index() {
        show_404('', FALSE);
    }

    private function authenticateUser() {

        try {

            $email = (string) $this->input->post('email');

            $password = (string) $this->input->post('password');

            $user = $this->users->login($email, $password);

            return $user;
        } catch (Exception $e) {

            throw new Exception('Authentication failed.');
        }
    }

    function synchronize() {

        try {

            $user = $this->authenticateUser();

            $userId = $user->getId();

            $lastSyncTimestamp = (int) $this->input->post('last_sync_timestamp');

            $currTimestamp = time();

            $dbEvents = $this->events->getDbEventsForSynchronization($userId, $lastSyncTimestamp);

            $remoteEvents = $this->events->getEventsFromPostData($this->input->post('events'));

            $userEventsToUpdate = $this->events->synchronize($remoteEvents, $dbEvents, $currTimestamp);

            $response = array(
                'sync_timestamp' => $currTimestamp,
                'events' => $userEventsToUpdate,
            );

            return $this->jsonResponse($response);
        } catch (Exception $e) {

            return $this->jsonError($e->getMessage());
        }
    }

}
