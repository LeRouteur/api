<?php

require "recovery/recovery.php";
require "auth/model_auth.php";

class auth
{
    protected $pdo;
    protected $result;
    private $model;
    private $auth_token;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->model = new Model_auth($this->pdo);
        $this->result = array(
            'authorized' => false,
            'error' => "",
            'error_db' => ""
        );
        $this->auth_token = "GDGTjUZV9DglELJtoyApzZOR0x6HluW2";
    }

    /**
     * Method used to check if the token correspond to a specific pattern.
     * The token should :
     * - equal 40 chars to be considered as a "token" in the DB
     * - equal 60 chars to be considered as a "token_login" in the DB
     * @param $user_token
     * @param $email
     * @param $auth_token
     * @return array
     */
    public function checkToken($user_token, $email, $auth_token)
    {
        if ($this->auth_token === $auth_token) {
            if (strlen($user_token) === 40) {
                $temp = $this->model->authenticateUser($user_token, $email, 40);

                if ($temp['error']) {
                    $this->result['error_db'] = $temp['error'];
                } elseif ($temp['authorized'] === true) {
                    $this->result['authorized'] = true;
                }
            } else {
                $this->result['error'] = '{"error":"Bad Token"}';
            }
        } else {
            $this->result['error'] = '{"error":"Bad Token"}';
        }
        return $this->result;
    }
}

