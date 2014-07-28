<?php

class Events {

    /**
     *
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

    private function getEventsFromPostData($data, $userId) {
        
        $events = array();
        
        $data = json_decode($data, true);
        
        if(!$data){
            return $events;
        }
        
        
        
        foreach($data as $d){
            
            
            
        }

        

        if (!is_array($data)) {
            $err = true;
        } else {
            foreach ($data as &$a) {
                $a['timeStamp'] = (string) $a['timeStamp'];
            }
        }

        if ($err) {
            show_error('Invalid request data', 500);
            die;
        }

        return $data;
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
    
    public function getFieldsValidationRules($fields) {

        $config = array();

        foreach ($fields as $fieldName) {
            $config[$fieldName] = $this->validationRules[$fieldName];
        }

        return $config;
    }

    private $validationRules = array(
        'name' => array(
            'field' => 'name',
            'label' => 'Name',
            'rules' => 'trim|required|xss_clean|min_length[3]|max_length[60]',
            'exception_code' => 1001,
        ),
        'email' => array(
            'field' => 'email',
            'label' => 'Email',
            'rules' => 'trim|xss_clean|required|valid_email',
            'exception_code' => 1002,
        ),
        'password' => array(
            'field' => 'password',
            'label' => 'Password',
            'rules' => 'trim|xss_clean|required|min_length[8]|max_length[20]',
            'exception_code' => 1003,
        ),
    );

}
