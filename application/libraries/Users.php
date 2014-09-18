<?php

class Users {

    /**
     * @var CI_Controller
     */
    private $ci;

    const EMAIL_NOT_FOUND = 'Email does not exist.';
    const INVALID_PASSWORD = 'Invalid password.';

    public function __construct() {

        $this->ci = &get_instance();
    }

    /**
     * 
     * @param string $name
     * @param string $email
     * @param string $password
     * @return \User
     * @throws Exception
     */
    function register($name, $email, $password) {

        $user = new User();

        $hash = $this->hash($password);

        $user->setName($name);
        $user->setEmail($email);
        $user->setHash($hash);

        $user->persist();

        return $user;
    }

    /**
     * @param string $email
     * @param string $password
     * @return \User
     * @throws Exception
     */
    function login($email, $password) {

        $result = $this->ci->users_model->getUserByEmail($email);

        if (!$result) {
            throw new Exception(self::EMAIL_NOT_FOUND);
        }

        $user = User::fromRowArray($result);

        $hash = $this->hash($password);

        if ($user->getHash() !== $hash) {
            throw new Exception(self::INVALID_PASSWORD);
        }

        return $user;
    }

    function getUserById($userId) {

        $userArr = $this->ci->users_model->getUserById($userId);

        return new User($userArr);
    }

    private function hash($password) {

        return md5($password);
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
