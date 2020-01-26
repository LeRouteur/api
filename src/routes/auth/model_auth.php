<?php


class model_auth
{
    protected $pdo;
    protected $result_db;
    protected $local_token;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->result_db = array(
            'authorized' => false,
            'error' => ""
        );
        $this->local_token = "";
    }

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
        } elseif ($length === 60) {
            $this->local_token = $token;
            try {
                $req = $this->pdo->prepare("SELECT token_login FROM tip.user WHERE email = :email");
                $req->bindParam(':email', $email);
                $req->execute();

                if ($req) {
                    $result = $req->fetch();
                    if ($this->local_token === $result['token_login']) {
                        $this->result_db['authorized'] = true;
                    } else {
                        $this->result_db['error'] = '{"error":"Bad Token"}';
                    }
                }

            } catch (PDOException $exception) {
                $this->result_db = '{"error":' . $exception->getMessage() . '}';
            }
        }

        return $this->result_db;
    }
}