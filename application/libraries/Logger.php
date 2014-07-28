<?php

/**
 * Logger is used to log messages to file, supports several types of logs
 * available, check source for all available ones.
 *
 * @author Aleksander Nowak
 */
class Logger {

    private $log_file;
    private $echo_logs;
    private $flags_count;
    private $instance_mode;
    private $instance_id;
    private static $flags = array(
        0 => array(
            'type' => 'finished',
            'color' => 'green'
        ),
        1 => array(
            'type' => 'info',
            'color' => 'blue'
        ),
        2 => array(
            'type' => 'notice',
            'color' => 'yellow'
        ),
        3 => array(
            'type' => 'warning',
            'color' => 'orange'
        ),
        4 => array(
            'type' => 'error',
            'color' => 'red'
        ),
        5 => array(
            'type' => 'fatal error',
            'color' => 'red'
        ),
    );

    /**
     * Logger class constructor
     * 
     * @param string $filename File to write the logs to
     * @param boolean $echo_mode Whether to output the log info to screen or not
     * @param boolean $instance_mode Whether to use instance mode
     * @param int $instance_id Id to be used if instance mode on
     */
    public function __construct($params) {

//        date_default_timezone_set("America/Los_Angeles");

        $this->log_file = $params['filename'];

        $this->echo_logs = $params['echo_mode'];

        $this->instance_mode = $params['instance_mode'];

        $this->instance_id = $params['instance_id'];

        // Reset type counts
        foreach (Logger::$flags as $type => $flag) {
            $this->flags_count[$type] = 0;
        }

        // Set up specified log file
        if (!file_exists($this->log_file)) {

            $this->resetLogFile();
        } else {

            $fp = fopen($this->log_file, 'a');
            if (!$fp) {
                echo $this->log_file;
                echo "Couldn't open log file !";
                //die;
            } else {
                fclose($fp);
            }
        }
    }

    public function enableInstanceMode($instanceId) {

        $this->instance_mode = true;
        $this->instance_id = $instanceId;
    }

    /**
     * Logs message to specified earlier log file, types:
     * 0 - finished
     * 1 - info
     * 2 - notice
     * 3 - warning
     * 4 - error
     * 5 - fatal error
     * @param string $message Message to by logged
     * @param int $type Type of log
     */
    public function log($message, $type = 1) {

        // Wrap status text with color span
        $status = "<span style=\"color:" . Logger::$flags[$type]['color'] . "\">" . Logger::$flags[$type]['type'] . "</span>";

        // Format the message
        $log_content = "[" . date('d M H:i:s') . "]";
        if ($this->instance_mode)
            $log_content .= "[" . $this->instance_id . "]";
        $log_content .= "[" . $status . "] " . $message . "<BR>\r\n";

        $fp = fopen($this->log_file, 'a');

        if (!$fp)
            return;

        fputs($fp, $log_content);
        fclose($fp);

        $this->flags_count[$type]++;

        if (!$this->echo_logs)
            return;

        echo $log_content;
        
//        flush();
    }

    /**
     * Gets the number of occurences of specified log type
     *
     * @param int $type Type of the log to count
     * @return int Number of occurences
     */
    public function getOccurences($type) {

        return $this->flags_count[$type];
    }

    /**
     * Cleans the log file
     */
    public function resetLogFile() {

        $fp = fopen($this->log_file, 'w');

        if (!$fp) {

            echo "Couldn't open log file";
            die;
        }

        fputs($fp, "<?php if ( !isset($" . "_GET" . "['key']) && $" . "_GET" . "['key'] != 'log' ) die; ?>");

        fclose($fp);
    }

}

?>