<?php

class Todos_model extends CI_Model {

    function __construct() {

        parent::__construct();
    }

    function addTodo(&$todo) {

        if (!isset($todo['text'])) {
            $todo['text'] = '';
        }

        $data = array(
            'timeStamp' => $todo['timeStamp'],
            'active' => $todo['active'],
            'text' => $todo['text'],
            'last_update' => time(),
        );

        $this->db->insert('todos', $data);

        return $this->db->affected_rows();
    }

    function getTodos($where = array()) {

        if ($where) {
            $this->db->where($where);
        }

        $query = $this->db->get('todos');
        
        return $query->result_array();
    }

    function updateTodo($todo) {

        $timeStamp = $todo['timeStamp'];

        $text = $todo['text'];

        $active = $todo['active'];

        $lastUpdate = $todo['last_update'];

        $this->db->update('todos', array('text' => $text, 'active' => $active, 'last_update' => $lastUpdate), array('timeStamp' => $timeStamp));

        return $this->db->affected_rows();
    }

}