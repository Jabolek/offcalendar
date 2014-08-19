<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Welcome extends CI_Controller {

    public function index() {

        $this->data['view'] = 'calendar';

        $this->load->view('master', $this->data);
    }

    public function notifications() {

        $this->data['view'] = 'notifications';
        $this->data['page_title'] = 'Notifications';

        $this->load->view('master', $this->data);
    }

    public function past_events() {

        $this->data['view'] = 'past_events';
        $this->data['page_title'] = 'Past Events';

        $this->load->view('master', $this->data);
    }

    public function your_profile() {

        $this->data['view'] = 'your_profile';
        $this->data['page_title'] = 'Your Profile';

        $this->load->view('master', $this->data);
    }

    public function settings() {

        $this->data['view'] = 'settings';
        $this->data['page_title'] = 'Settings';

        $this->load->view('master', $this->data);
    }
    
    public function upcoming_events() {

        $this->data['view'] = 'upcoming_events';
        $this->data['page_title'] = 'Upcoming Events';

        $this->load->view('master', $this->data);
    }
    
    public function add() {
        
    }

}
