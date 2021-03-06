<?php

/** This file contains the required methods to delete a user from the DB.
 * @author Cyril Buchs
 * @version 1.4
 */

class delete
{
    protected $pdo;
    protected $username;
    private $result;
    private $auth_token;

    /**
     * delete constructor.
     * @param PDO $pdo
     * @param $username
     * @param $auth_token
     */
    public function __construct(PDO $pdo, $username, $auth_token)
    {
        $this->pdo = $pdo;
        $this->username = $username;
        $this->auth_token = $auth_token;
    }

    /**
     * Method used to delete a user from the DB.
     * @return bool|string
     */
    public function deleteUser()
    {
        if ($this->auth_token === "aQcOuZnmxjJZ0Y8L3aZ0Xv3WYxbVT4Bo")
            try {
                $req = $this->pdo->prepare("DELETE FROM tip.user WHERE ldap_username = :ldap_username");
                $req->bindParam(':ldap_username', $this->username);
                $req->execute();
                if ($req) {
                    $this->result = true;
                } else {
                    $this->result = '{"error":"Contact Administrator"}';
                }
            } catch (PDOException $e) {
                $this->result = $e->getMessage();
            }
        return $this->result;
    }

    public function deleteDbUser($email)
    {
        try {
            $req = $this->pdo->prepare("DELETE FROM tip.user WHERE email = :email");
            $req->bindParam(':email', $email);
            $req->execute();
            if ($req) {
                $this->result = true;
            } else {
                $this->result = '{"error":"Contact Administrator"}';
            }
        } catch (PDOException $e) {
            $this->result = $e->getMessage();
        }
        return $this->result;
    }
}
