<?php
/**
 * This file contains the required methods to check the token provided by the user with the one that is stored in the DB.
 * @author Cyril Buchs
 * @version 1.1
 */

class Model_act
{
    protected $pdo;

    /**
     * Model_act constructor.
     * @param PDO $pdo
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Method to check if the key provided by the user matches the one that is stored in the DB.
     * If yes, it will update the status and enable the account.
     * If no, an error message will be returned to the user.
     * @param $email
     * @param $key
     * @return false|string
     */
    public function activateAccount($email, $key)
    {
        $result = "";
        // Get the key corresponding to the email provided
        $stmt = $this->pdo->prepare("SELECT activation_key, activation FROM tip.user WHERE email like :email");
        if ($stmt->execute(
                array(
                    ':email' => $email,
                )
            ) && $row = $stmt->fetch()) {
            $key_db = $row['activation_key']; // Get the key from the DB
            $activation_status = $row['activation']; // Get the activation status from the DB (the value is either 0 or 1)
        }

        // Testing the value of $activation_status.
        // If the value in the column of the activation status is 1 (account already enabled), we redirect to another page.
        if ($activation_status == '1') {
            $result .= '{"error":"Already Enabled"}';
            // We start the comparisons.
        } else {
            if ($key == $key_db) {
                // The program should then update the activation status in the DB
                $stmt = $this->pdo->prepare('UPDATE tip.user SET activation = 1 WHERE email = :email');
                $stmt->bindParam(':email', $email);
                $stmt->execute();

                // If the keys are the same, we redirect to another page.
                $result .= '{"success":"Activation Successful"}';

                // And finally, if the two are different, we redirect to an error page.
            } else {
                $result .= '{"error":"Bad Request"}';
            }
        }
        return $result;
    }

}