<?php

require_once "model_recovery.php";

class recovery
{
    protected $pdo;
    protected $model;
    protected $result;
    protected $auth;

    public function __construct(PDO $pdo, $auth)
    {
        $this->pdo = $pdo;
        $this->model = new Model_recovery($this->pdo);
        $this->result = array();
        $this->auth = $auth;
    }

    /**
     * This function will receive the POST and check if it isn't empty. If not, it'll put the POST data in an array.
     * @param $email
     * @param $password
     * @return false|string
     */
    public function getFormDataRecovery($email, $password)
    {
        $valid_email = $valid_password = "";
        // Validate the data
        if (filter_var($email, FILTER_VALIDATE_EMAIL, FILTER_SANITIZE_EMAIL)) {
            $valid_email = $email;
        }

        if (filter_var($password, FILTER_SANITIZE_STRING)) {
            $valid_password = $this->hashPass($password);
        }

        // Call the function to check if the user exists in DB
        $this->result = $this->model->updateFields($valid_password, $valid_email);

        return $this->result;
    }

    private function hashPass($password)
    {
        // Hash the password before inserting it in the DB
        $hashedPass = password_hash($password, PASSWORD_DEFAULT);
        return $hashedPass;
    }
}