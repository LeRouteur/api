<?php

class model_login
{
    protected $pdo;
    protected $result_login;

    /**
     * Model_login constructor.
     * @param PDO $pdo
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->result_login = "";
        if (!empty($_SESSION)) {
            $this->result_login = '{"error":"User Already Logged"}';
        }
    }

    /**
     * Method used to check the credentials provided by the user with the one stored in the DB.
     * @param $email
     * @param $pass
     * @return false|string
     */
    function checkCredentials($email, $pass)
    {
        try {
            $req = $this->pdo->prepare('SELECT tip.user.email, tip.user.password, tip.user.activation FROM tip.user WHERE email = :email');
            $req->execute(array(
                ':email' => $email
            ));
            $result = $req->fetch();
            //var_dump($result);
            $isPassCorrect = password_verify($pass, $result['password']);

            // Assign the value of the activation status
            $activation_status = $result['activation'];

            if (empty($result)) {
                $this->result_login = '{"error":"Username Or Password Is Incorrect"}';
            } else {
                if ($activation_status == 1) {
                    if ($isPassCorrect) {
                        $rand_num = rand(5, 2000);
                        $_SESSION['email'] = $result['email'];
                        $_SESSION['nID'] = $rand_num;
                        $_SESSION['auth'] = true;
                        $this->result_login = '{"success":"User Logged In"}';
                    } else {
                        $this->result_login = '{"error":"Username Or Password Is Incorrect"}';
                    }
                } else {
                    $this->result_login = '{"error":"Account Is Not Enabled"}';
                }
            }
        } catch (PDOException $e) {
            $this->result_login = json_encode($e->getMessage());
        }
        return $this->result_login;
    }
}



