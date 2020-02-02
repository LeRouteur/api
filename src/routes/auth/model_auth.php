<?php

/** This class contains the required method to check the authentication token for password resetting.
 * @author Cyril Buchs
 * @version 1.6
 */

class model_auth
{
    protected $pdo;
    protected $result_db;
    protected $local_token;

    /**
     * model_auth constructor.
     * @param PDO $pdo
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->result_db = array(
            'authorized' => false,
            'error' => ""
        );
        $this->local_token = "";
    }

    /**
     * Method used to compare the token from the user and the one from the DB.
     * @param $token
     * @param $email
     * @param $length
     * @return array|string
     */
    public function authenticateUser($token, $email, $length)
    {
        if ($length === 40) {
            $this->local_token = $token;
            try {
                $req = $this->pdo->prepare("SELECT token FROM tip.user WHERE email = :email");
                $req->bindParam(':email', $email);
                $req->execute();

                if ($req) {
                    $result = $req->fetch();
                    if ($this->local_token === $result['token']) {
                        $this->result_db['authorized'] = true;
                    } else {
                        $this->result_db['error'] = '{"error":"Bad Token"}';
                    }
                }

            } catch (PDOException $e) {
                $this->result_db['error'] = '{"error":' . $e->getMessage() . '}';
            }
        }
        return $this->result_db;
    }
}