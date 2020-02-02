<?php

/** This file contains the required methods to receive and validate the form data of the password recovery.
 * @author Cyril Buchs
 * @version 1.6
 */

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

	// Validate password strength
	$uppercase = preg_match('@[A-Z]@', $password);
	$lowercase = preg_match('@[a-z]@', $password);
	$number    = preg_match('@[0-9]@', $password);
	$specialChars = preg_match('@[^\w]@', $password);

	if ($uppercase && $lowercase && $number && strlen($password) >= 8) {
	    $valid_password = $this->hashPass($password);	
	} else {
	    $this->result = '{"error":"Password Does Not Meet The Requirements"}';
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
