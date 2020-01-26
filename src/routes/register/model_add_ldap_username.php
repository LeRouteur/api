<?php


class model_add_ldap_username
{
    protected $pdo;
    protected $ldap_username;
    protected $email;

    public function __construct(PDO $pdo, $ldap_username, $email)
    {
        $this->pdo = $pdo;
        $this->ldap_username = $ldap_username;
        $this->email = $email;
    }

    public function addUsername()
    {
        try {
            $req = $this->pdo->prepare('UPDATE tip.user SET ldap_username = :ldap_username WHERE email = :email');
            //'INSERT INTO tip.user(lastname, firstname, email, sex, address, city, postal_code, password, sub_status, activation_key, activation, token, token_login) VALUES (:lastname, :firstname, :email, :sex, :address, :city, :postal_code, :password, :sub_status, :activation_key, :activation, :token, :token_login)'
            $req->bindParam(':ldap_username', $this->ldap_username);
            $req->bindParam(':email', $this->email);
            $req->execute();

            if ($req) {
                return true;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            echo json_encode($e->getMessage());
        }
        return null;
    }


}