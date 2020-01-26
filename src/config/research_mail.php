<?php


class research_mail
{
    protected $pdo;
    private $result;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function search($email)
    {
        try {
            $req = $this->pdo->prepare('SELECT lastname, sex, sub_status FROM tip.user WHERE email = :email');
            $req->bindParam(':email', $email);
            $req->execute();
            if ($req) {
                $this->result = $req->fetchAll();
            } else {
                // Uh oh, something went wrong...
                $this->result = false;
            }
        } catch (PDOException $e) {
            $this->result = '{"error":"' . $e->getMessage() . '"}';
        }
        return $this->result;
    }
}