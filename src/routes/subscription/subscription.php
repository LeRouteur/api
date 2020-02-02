<?php

/** This file contains the required methods
 * @author Cyril Buchs
 * @version 1.5
 */

require "model_sub.php";

class subscription
{

    protected $pdo;
    protected $result;
    private $auth_token;
    protected $model;

    /**
     * subscription constructor.
     * @param PDO $pdo
     * @param $auth_token
     */
    public function __construct(PDO $pdo, $auth_token)
    {
        $this->pdo = $pdo;
        $this->result = array(
            'changed' => false,
            'error' => "",
            'error_db' => ""
        );
        $this->auth_token = $auth_token;
        $this->model = new Model_sub($this->pdo);
    }

    /**
     * Method used to validate the auth_token.
     * @return bool
     */
    public function checkToken()
    {
        if ($this->auth_token === "LEJeM8ksw4dnvozEqaeqyWWaBGRB73Lf") {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Method used to validate the subscription status.
     * @param $new_status
     * @param $email
     * @return array
     */
    public function validateData($new_status, $email)
    {
        if ($new_status == 0 || $new_status == 1 || $new_status == 2 || $new_status == 3 || $new_status == 4) {
            $temp = $this->model->updateStatus($new_status, $email);
            if ($temp['changed']) {
                $this->result['changed'] = true;
            } elseif ($temp['error_db']) {
                $this->result['error_db'] = $temp['error_db'];
            }
        } else {
            $this->result['error'] = '{"error":"Status Invalid"}';
        }
        return $this->result;
    }
}
