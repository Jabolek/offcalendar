<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Users_api extends Api_Controller {

    function __construct() {

        parent::__construct('users');
    }

    function index() {
        show_404('', FALSE);
    }

    function register() {

        try {

            if (!$this->input->post()) {
                throw new Exception('Please fill required fields');
            }

            $fields = $this->users->getFieldsValidationRules(array('name', 'email', 'password'));

            $this->form_validation->set_rules($fields);

            if (!$this->form_validation->run()) {
                throw new Exception(strip_tags(validation_errors()));
            }

            $vals = array();

            foreach (array_keys($fields) as $f) {
                $vals[] = $this->form_validation->set_value($f);
            }

            list($name, $email, $password) = $vals;

            $user = $this->users->register($name, $email, $password);

            return $this->jsonResponse($user->toApiResponse());
        } catch (Exception $e) {
            return $this->jsonError($e->getMessage());
        }
    }

    function login() {

        try {
            
            $email = (string) $this->input->post('email');
            
            $password = (string) $this->input->post('password');
            
            $user = $this->users->login($email, $password);

            return $this->jsonResponse($user->toApiResponse());
        } catch (Exception $e) {
            return $this->jsonError($e->getMessage());
        }
    }

}
