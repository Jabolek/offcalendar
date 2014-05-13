<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * @property Todos_model $todos_model
 */
class Todos extends CI_Controller {

    public function __construct() {

        parent::__construct();

        $this->load->model('todos_model');
    }

    public function index() {
        
    }

    private $syncData = array(
        'add' => array(),
        'update' => array(),
    );

    private function validateRequest() {

        $err = false;

        if (!isset($_POST['todos'])) {
            $err = true;
        }

        $data = $_POST['todos'];

        $arr = json_decode($data, true);

        if (!is_array($arr)) {
            $err = true;
        } else {
            foreach ($arr as &$a) {
                $a['timeStamp'] = (string) $a['timeStamp'];
            }
        }

        if ($err) {
            show_error('Invalid request data', 500);
            die;
        }

        return $arr;
    }

    private function getTodosSynchronizationData(&$userTodos, &$dbTodos) {

        $data = $this->syncData;

        $userTodos = $this->sortById($userTodos);

        $dbTodos = $this->sortById($dbTodos);

        foreach ($userTodos as &$t) {

            $id = $t['timeStamp'];

            if (!isset($dbTodos[$id])) {

                $this->todos_model->addTodo($t);
            } else {

                if ($t['last_update'] > $dbTodos[$id]['last_update']) {

                    $this->todos_model->updateTodo($t);
                } else if ($t['last_update'] < $dbTodos[$id]['last_update']) {
                    
                    $data['update'][] = $dbTodos[$id];

                }
            }
        }

        foreach ($dbTodos as &$t) {

            $id = $t['timeStamp'];

            if (!isset($userTodos[$id])) {
                $data['add'][] = $t;
            }
        }

        return $data;
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

    public function synchronize() {

        $userTodos = $this->validateRequest();

        $dbTodos = $this->todos_model->getTodos();

        $data = $this->getTodosSynchronizationData($userTodos, $dbTodos);

        echo json_encode($data);
    }

    public function add_todo() {
        echo "ok";
    }

    public function get_todos() {
        
    }

}