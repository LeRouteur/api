<?php

/** This file contains the required methods to update the subscription status of a user.
 * @author Cyril Buchs
 * @version 1.0
 */

class model_sub
{
    protected $pdo;
    protected $result_db;

    /**
     * model_sub constructor.
     * @param PDO $pdo
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->result_db = array(
            'changed' => false,
            'error_db' => ""
        );
    }

    /**
     * Method used to update the sub_status of a user.
     * @param $new_status
     * @param $email
     * @return array
     */
    public function updateStatus($new_status, $email)
    {
        try {
            $req = $this->pdo->prepare("UPDATE tip.user SET sub_status = :sub_status WHERE email = :email");
            $req->execute(array(
                ':sub_status' => $new_status,
                ':email' => $email
            ));
            if ($req) {
                $this->result_db['changed'] = true;
            } else {
                $this->result_db['error_db'] = '{"error":"Contact Administrator"}';
            }
        } catch (PDOException $e) {
            $this->result_db['error_db'] = '{"error":' . $e->getMessage() . '}';
        }
        return $this->result_db;
    }
}