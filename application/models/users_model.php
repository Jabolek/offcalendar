<?php

class Users_model extends CI_Model {

    function __construct() {

        parent::__construct();
    }

    function addUser($user) {

        $this->db->ignore();

        $this->db->insert('users', $user);

        if ($this->db->affected_rows()) {
            return $this->db->insert_id();
        }

        return null;
    }

    function updateUser($userId, $toUpdate) {

        $this->db->update('users', $toUpdate, array('id' => $userId));

        return $this->db->affected_rows();
    }

    function getUserByEmail($email) {

        $query = $this->db->get_where('users', array('email' => $email));

        return $query->row_array();
    }

    function getUserById($userId) {

        $query = $this->db->get_where('users', array('id' => $userId));

        return $query->row_array();
    }

}

class User {

    /**
     * @var CI_Controller
     */
    private $_ci;
    private $id = null;
    private $name;
    private $email;
    private $hash;

    /**
     * @param array $user
     * @return \User|null
     */
    public static function fromRowArray($user) {

        if (!$user) {
            return null;
        }

        return new User($user);
    }

    function persist() {

        if ($this->id) {
            $this->_ci->users_model->updateUser($this->id, $this->toDbArray());
        } else {
            $userId = $this->_ci->users_model->addUser($this->toDbArray());

            if (!$userId) {
                throw new Exception('Email already exists in database.');
            }

            $this->id = $userId;
        }

        return $this;
    }

    public function toDbArray() {

        return array(
            'name' => $this->name,
            'email' => $this->email,
            'hash' => $this->hash,
        );
    }

    public function toApiResponse() {

        return array(
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
        );
    }

    public function __construct($properties = array()) {

        $this->_ci = &get_instance();

        if ($properties) {

            $this->id = (int) $properties['id'];

            $this->name = $properties['name'];

            $this->email = $properties['email'];

            $this->hash = $properties['hash'];
        }
    }

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getName() {
        return $this->name;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getHash() {
        return $this->hash;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function setEmail($email) {
        $this->email = $email;
    }

    public function setHash($hash) {
        $this->hash = $hash;
    }

}
