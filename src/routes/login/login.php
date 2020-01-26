<?php

/**
 * This file contains the required functions to validate the login POST data.
 * @author Cyril Buchs
 * @version 1.4
 */

require_once "model_login.php";

class login
{
    protected $pdo;
    protected $model;

    /**
     * login constructor.
     * @param PDO $pdo
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->model = new Model_login($this->pdo);
    }

    /**
     * This function will receive the data and validate it.
     * @param $data
     * @return false|string
     */
    public function getFormData($data)
    {
        $valid_email = "";
        if (!empty($data)) {
            $formDataLogin = [$data['mail'], $data['password']];
            //var_dump($formDataLogin);

            // Get the email from the array
            $email = $formDataLogin[0];

            // Get the password from the array
            $pass = $formDataLogin[1];

            // Validate the data
            if (filter_var($email, FILTER_VALIDATE_EMAIL, FILTER_SANITIZE_EMAIL)) {
                $valid_email = $email;
            }
            // Call the function to check if the user exists in DB

            $result = $this->model->checkCredentials($valid_email, $pass);

        }

        return $result;
    }
}