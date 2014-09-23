<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Cron_email_notifications extends Cron_Controller {

    private $maxSendTimestamp;

    /**
     * @var Mailer 
     */
    public $mailer;

    function __construct() {

        parent::__construct('email_notifications', false);

        $this->load->library('Mailer');

        $this->maxSendTimestamp = round(microtime(true) * 1000) + 600000;
    }

    function index() {
        die;
    }

    function full_update() {

        $errors = 0;

        $done = 0;

        $events = $this->events->getEventsForNotificationSend($this->maxSendTimestamp);

        foreach ($events as $event) {

            $logId = '#' . $event['id'];

            try {

                $user = $this->users_model->getUserById($event->getUserId());

                $this->sendNotification($user, $event);

                $this->log("Event {$logId}: OK");

                $done++;
            } catch (Exception $e) {

                $this->log("{$logId}: " . $e->getMessage(), 4);

                $errors++;
            }

            if (($done + $errors) % 10 == 0) {
                $this->log("{$done}.");
                $this->cronStillWorking();
            }
        }



        $this->cronFinished();

        $this->log("Finished. {$done} sent, {$errors} errors.", 0);
    }

    private function sendNotification(User $user, Event $event) {

        $to = $user->getEmail();

        $subject = date('Y-m-d H:i:s', $event->getStartTimestamp()) . ' - event starts soon.';

        $message = $event->getDescription();

        $this->mailer->sendMessage($to, $subject, $message);

        $event->notificationSent();
    }

    public function test() {

        $user = $this->users->getUserById(1);

        $user->setEmail('olek17@gmail.com');

        $event = $this->events->getEventById(7);

        try {

            $this->sendNotification($user, $event);

            $this->log('Test email sent.');
        } catch (Exception $e) {
            $this->log($e->getMessage(), 4);
        }

        $this->cronFinished();
    }

}
