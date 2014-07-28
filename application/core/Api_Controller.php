<?php

/**
 * @property Logger $logger
 */
class Api_Controller extends MY_Controller {
    
    protected $apiName;
    
    function __construct($apiName) {

        parent::__construct();

        $this->apiName = $apiName;

        $loggerParams = array(
            "filename" => "logs/{$apiName}.php",
            "echo_mode" => false,
            "instance_mode" => false,
            "instance_id" => '',
        );

        $this->load->library('Logger', $loggerParams);
    }
    
    protected function jsonResponse($obj) {

        header('Content-type: application/json');

        echo json_encode($obj);

        die;
    }
    
    protected function jsonError($msg) {
        
        $this->log($msg, 4);
        
        header('HTTP/1.1 401 Bad Request', true, 400);
        header('Content-type: application/json');

        echo json_encode(array('error' => $msg));

        die;
    }
    
    function log($message, $type = 1) {

        $this->logger->log($message, $type);
    }

}