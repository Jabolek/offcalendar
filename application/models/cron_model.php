<?php

class Cron_model extends CI_Model {

    function __construct() {

        parent::__construct();
    }

    function updateCronStatus($cronName, $data) {

        $this->db->update('cron', $data, array('job_name' => $cronName));
    }

    function isAlreadyRunning($cronName, $timestamp, $tolerance) {

        $toleranceTime = $timestamp - $tolerance;

        $this->db->update('cron', array('last_update' => $timestamp, 'last_start' => $timestamp), array('last_update <' => $toleranceTime, 'job_name' => $cronName));

        if ($this->db->affected_rows() > 0) {
            return false;
        }

        $res = $this->db->get_where('cron', array('job_name' => $cronName));

        if ($res->num_rows() == 0) {

            $this->db->ignore();
            $this->db->insert('cron', array('job_name' => $cronName, 'last_update' => $timestamp, 'last_start' => $timestamp));

            if ($this->db->affected_rows())
                return false;
        } else {
            return true;
        }
    }

}
