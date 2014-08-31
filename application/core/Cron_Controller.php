<?php

/**
 * @property Cron_helper $cron_helper
 * @property Automation $automation
 * @property Logger $logger
 */
class Cron_Controller extends MY_Controller {
    /*
     * Saves cron run to db and allows only one instance running
     */
    protected $singleMode;
    protected $jobName;
    /*
     * specifies which functionality part job covers
     */
    protected $jobSubinstanceName;
    /*
     * In seconds. Cron will execute if last db update for given job older.
     */
    protected $tolerance = 600;
    /*
     * How many rows to fetch from db at once.
     */
    protected $batchSize = 1000;
    public $stats_helper;

    function __construct($jobName, $singleMode = true) {

        parent::__construct();


        if (strpos(current_url(), '1324') === false) {
            show_404();
            die;
        }

        // closeConnection(1);

        set_time_limit(0);

        ignore_user_abort(true);

        $this->jobName = $jobName;

        $this->singleMode = $singleMode;

        $date = date("d-m-y", time());

        $loggerParams = array(
            "filename" => "logs/{$jobName}-{$date}.php",
            "echo_mode" => true,
            "instance_mode" => false,
            "instance_id" => '',
        );

        $this->load->library('Logger', $loggerParams);

        $this->load->model('cron_model');

        $this->setSingleMode($singleMode);
    }

    function setSingleMode($set = true) {

        $this->singleMode = $set;

        if ($set) {
            $this->isAlreadyRunning();
        }
    }

    function cronStillWorking() {

        if ($this->singleMode) {

            $data = array(
                'last_update' => time(),
            );

            $this->cron_model->updateCronStatus($this->jobName, $data);
        }
    }

    function cronFinished() {

        if ($this->singleMode) {

            $data = array(
                'last_finish' => time(),
                'last_update' => 0,
            );

            $this->cron_model->updateCronStatus($this->jobName, $data);
        }
    }

    function killCron() {

        if ($this->singleMode) {

            $data = array(
                'last_update' => 0,
            );

            $this->cron_model->updateCronStatus($this->jobName, $data);
        }
        die;
    }

    function isAlreadyRunning() {
        if ($this->cron_model->isAlreadyRunning($this->jobName, time(), $this->tolerance)) {
            $this->logger->log("{$this->jobName} cron already running.", 0);
            die;
        } else {
            $this->logger->log("{$this->jobName} update started...");
        }
    }

    function log($message, $type = 1) {

        $this->logger->log($message, $type);
    }

}