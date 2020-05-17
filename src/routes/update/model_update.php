<?php

/** This file contains the required methods to update the user data.
 * @author Cyril Buchs
 * @version 1.3
 */

class model_update
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Method used to update the fields in the DB with the new entry from the user.
     * @param $data
     * @return bool|null
     */
    public function updateFields($data)
    {
        if (!empty($_SESSION['email'])) {
            try {
                $req = $this->pdo->prepare('UPDATE tip.user SET lastname = :lastname, firstname = :firstname, sex = :sex, address = :address, city = :city, postal_code = :postal_code WHERE email = :email');
                $req->execute(array(
                    ':lastname' => $data[0],
                    ':firstname' => $data[1],
                    ':sex' => $data[2],
                    ':address' => $data[3],
                    ':city' => $data[5],
                    ':postal_code' => $data[4],
                    ':email' => $_SESSION['email']
                ));
                if ($req) {
                    return '{"success":"User Updated"}';
                } else {
                    return '{"error":"Contact Administrator"}';
                }
            } catch (PDOException $e) {
                echo $e->getMessage();
            }
        } else {
            return '{"error":"User Not Logged"}';
        }
        return null;
    }
}