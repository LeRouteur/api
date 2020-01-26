<?php


class research_status
{
    protected $pdo;
    private $result;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function search($ldap_username)
    {
        try {
            $req = $this->pdo->prepare("SELECT sub_status FROM tip.user WHERE ldap_username = :ldap_username");
            $req->bindParam(':ldap_username', $ldap_username);
            $req->execute();
            if ($req) {
                $this->result = $req->fetchAll(PDO::FETCH_ASSOC);
            } else {
                $this->result = '{"error":"User Does Not Exist"}';
            }
        } catch (PDOException $e) {
            $this->result = '{"error":' . $e->getMessage() . '}';
        }
        return $this->result;
    }
}