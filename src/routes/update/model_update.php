<?php


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

    /**
     * Method used to retrieve the fields from the DB.
     * @param $session_email
     * @return array|false|string
     */
    /**public function getFields($session_email)
     * {
     * try {
     * $fields = array();
     * $conn = $this->pdo;
     * $queries = [
     * 'lastname' => "SELECT tip.user.lastname FROM tip.user WHERE email = '$session_email'",
     * 'firstname' => "SELECT tip.user.firstname FROM tip.user WHERE email = '$session_email'",
     * 'email' => "SELECT tip.user.email FROM tip.user WHERE email = '$session_email'",
     * 'address' => "SELECT tip.user.address FROM tip.user WHERE email = '$session_email'",
     * 'postal_code' => "SELECT tip.user.postal_code FROM tip.user WHERE email = '$session_email'",
     * 'city' => "SELECT tip.user.city FROM tip.user WHERE email = '$session_email'",
     * 'gender' => "SELECT tip.user.sex, tip.sex.fullname FROM tip.user INNER JOIN tip.sex ON tip.user.sex = tip.sex.name WHERE tip.user.email = '$session_email'"
     * ];
     *
     * foreach ($queries as $name => $query) {
     * $result = $conn->query($query);
     * foreach ($result->fetchAll(PDO::FETCH_ASSOC) as $field) {
     * $fields[$name] = $field;
     * }
     * }
     * return $fields;
     * } catch (PDOException $e) {
     * return json_encode($e->getMessage());
     * }
     * }
     * }*/
}