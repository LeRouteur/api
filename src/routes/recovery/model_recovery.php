<?php


class model_recovery
{
    protected $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

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