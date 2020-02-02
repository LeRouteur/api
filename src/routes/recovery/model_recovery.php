<?php

/** This file contains the required methods to update the password of the user.
 * @author Cyril Buchs
 * @version 1.2
 */

class model_recovery
{
    protected $pdo;

    /**
     * model_recovery constructor.
     * @param PDO $pdo
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Method used to modify the user password.
     * @param $pass
     * @param $session_email
     * @return string
     */
    public function updateFields($pass, $session_email)
    {
        try {
            $req = $this->pdo->prepare("UPDATE tip.user SET password = :password WHERE email = :email");
            $req->execute(array(
                ':password' => $pass,
                ':email' => $session_email
            ));
            if ($req) {
                $result = '{"success":"Password Modified"}';
                session_destroy();
            } else {
                $result = '{"error":"Contact Administrator"}';
            }
        } catch (PDOException $e) {
            $result = '{"error":'. $e->getMessage() . '}';
        }
        return $result;
    }
}