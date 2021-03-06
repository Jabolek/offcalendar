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

            $currTimestamp = round(microtime(true) * 1000);

            $dbEvents = $this->events->getDbEventsForSynchronization($userId, $lastSyncTimestamp);
            
            $postEvents = $this->input->post('events');

            $remoteEvents = $this->events->getEventsFromPostData($postEvents, $user);
            
            $eventsToUpdate = $this->events->synchronize($remoteEvents, $dbEvents, $currTimestamp);

            $toUpdate = array();
            
            foreach($eventsToUpdate as $e){
                $toUpdate[] = $e->toApiArray();
            }

            $response = array(
                'sync_timestamp' => $currTimestamp,
                'events_to_update' => $toUpdate,
            );

            return $this->jsonResponse($response);
        } catch (Exception $e) {

            return $this->jsonError($e->getMessage());
        }
    }

}
