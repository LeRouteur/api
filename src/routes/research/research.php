<?php

/** This file contains the required methods to research a user.
 * @author Cyril Buchs
 * @version 1.7
 */

class Research
{
    protected $pdo;
    protected $email;
    private $auth_token;
    private $user_token;

    /**
     * Research constructor.
     * @param PDO $pdo
     * @param $email
     * @param $auth_token
     */
    public function __construct(PDO $pdo, $email, $auth_token)
    {
        $this->pdo = $pdo;
        $this->email = $email;
        $this->auth_token = "YiWAMRJvR6kt07vkgM3eLl5f2PNo16b4";
        $this->user_token = $auth_token;
    }

    /**
     * Method used to check if the token provided by the user matches the one that is in the code.
     * @return bool
     */
    public function checkToken()
    {
        if ($this->user_token === $this->auth_token) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Method used to research data for a user.
     * @param $arg
     * @return array|mixed|string|null
     */
    public function search($arg)
    {
        switch ($arg) {
            case "user":
                try {
                    //$conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
                    $req = $this->pdo->prepare("SELECT lastname, firstname, email, sex, address, city, postal_code, sub_status, ldap_username FROM tip.user WHERE email = :email");
                    //SELECT lastname, firstname, email, sex, address, city, postal_code, sub_status, tip.sex.fullname FROM tip.user
                    //INNER JOIN tip.sex ON tip.user.sex = tip.sex.name WHERE email = :email
                    $req->bindParam(':email', $this->email);
                    $req->execute();
                    if ($req) {
                        $result = $req->fetchAll(PDO::FETCH_ASSOC);
                    } else {
                        $result = '{"error":"Bad request"}';
                    }
                } catch (PDOException $e) {
                    $result = $e->getMessage();
                }
                return $result;
                break;
            case "send_mail":
                try {
                    $req = $this->pdo->prepare("SELECT lastname, sex, email, token FROM tip.user WHERE email = :email");
                    $req->bindParam(':email', $this->email);
                    $req->execute();
                    if ($req) {
                        $result_send_mail = $req->fetch(PDO::FETCH_ASSOC);
                    } else {
                        $result_send_mail = '{"error":"Bad Request"}';
                    }
                } catch (PDOException $e) {
                    $result_send_mail = $e->getMessage();
                }
                return $result_send_mail;
                break;
        }
        return null;
    }
}

