<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Welcome extends CI_Controller {

    public function index() {

        $this->data['page_title'] = 'Sign in';

        $this->load->view('login', $this->data);
    }

    public function register() {

        $this->data['page_title'] = 'Sign up';

        $this->load->view('register', $this->data);
    }

    public function dashboard() {

        $this->load->view('master');
    }

    public function unauthorized() {

        $this->data['page_title'] = 'Unauthorized access!';

        $this->load->view('unauthorized', $this->data);
    }

}
