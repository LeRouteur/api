<?php
/**
 * This file contains the required functions to insert valid form data in the database.
 * @author Cyril Buchs
 * @version 1.7
 */

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

$key = md5(microtime(TRUE) * 100000);
$token = sha1(microtime(TRUE) * 100000);

class Model_register
{
    protected $pdo;
    protected $email_settings;

    public function __construct(PDO $pdo, array $email_settings)
    {
        $this->pdo = $pdo;
        $this->email_settings = $email_settings;
    }

    /**
     * Method to check if mail given by the user already exists in the database. If so, it will return an error message.
     * @param $validFormData
     * @return bool|null
     */
    function checkEmailExistence($validFormData)
    {
        // Get the email from the valid form data array
        $email = $validFormData[2];
        try {
            $req = $this->pdo->prepare('SELECT email FROM tip.user WHERE email = :email');
            $req->execute(
                array(
                    ':email' => $email,
                )
            );

            // Check if request rows are higher than 0. If yes, it means that the email exists
            if ($req->rowCount() > 0) {
                array_splice($validFormData, 0);
                return false;
                // Email does not exist, so account creating is OK
            } else {
                $this->postFields($validFormData);
                return true;
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
        return null;
    }

    /**
     * Method used to insert data in the DB.
     * @param $validFormData
     * @return bool|null
     */
    public function postFields($validFormData)
    {
        global $token;
        //global $token_login;
        try {
            $req = $this->pdo->prepare('INSERT INTO tip.user(lastname, firstname, email, sex, address, city, postal_code, password, sub_status, activation_key, activation, token) VALUES (:lastname, :firstname, :email, :sex, :address, :city, :postal_code, :password, :sub_status, :activation_key, :activation, :token)');
            //'INSERT INTO tip.user(lastname, firstname, email, sex, address, city, postal_code, password, sub_status, activation_key, activation, token, token_login) VALUES (:lastname, :firstname, :email, :sex, :address, :city, :postal_code, :password, :sub_status, :activation_key, :activation, :token, :token_login)'
            $req->execute(
                array(
                    ':lastname' => $validFormData[0],
                    ':firstname' => $validFormData[1],
                    ':email' => $validFormData[2],
                    ':sex' => $validFormData[3],
                    ':address' => $validFormData[4],
                    ':postal_code' => $validFormData[5],
                    ':city' => $validFormData[6],
                    ':password' => $validFormData[7],

                    // Set a default subscription status at 0, who means no subscription active
                    ':sub_status' => 0,

                    // Set a default activation key at 0 (it will be changed with the next function)
                    ':activation_key' => 0,

                    // Set a default account status at 0, who means account disabled
                    ':activation' => 0,

                    // Create a token for password changing when logged on with sha1 microtime
                    ':token' => $token,

                    // Set the LDAP username
                    //':ldap_username' => $ldap_username
                )
            );
            if ($req) {
                $this->accountConfirmation($validFormData);
                $this->sendMail($validFormData);
                return true;
            } else {
                return false;
            }
        } catch
        (PDOException $e) {
            echo json_encode($e->getMessage());
        }
        return null;
    }

    /**
     * Method used to change the activation key in the DB after user insertion.
     * @param $validFormData
     */
    function accountConfirmation($validFormData)
    {
        // Create the required vars for account confirmation
        global $key;
        $email = $validFormData[2];

        // Insert the key in the DB
        try {
            $stmt = $this->pdo->prepare('UPDATE tip.user SET activation_key = :key WHERE email = :email');
            $stmt->execute(
                array(
                    ':key' => $key,
                    ':email' => $email,
                )
            );
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    /**
     * Method that will send an email to user when the insert pass is successful.
     * @param $validFormData
     */
    function sendMail($validFormData)
    {
        // Define some local vars
        $lastname = $validFormData[0];
        $email = $validFormData[2];
        global $key;

        // Get the sex of the user. If it's man, it will show "Bonjour Monsieur" ; if it's woman, it'll show "Bonjour Madame"
        // and if it's "other", it'll just show "Bonjour"...
        $sex = $validFormData[3];
        if ($sex == "man") {
            $sex_message = "Bonjour Monsieur " . $lastname;
        } elseif ($sex == "woman") {
            $sex_message = " Bonjour Madame " . $lastname;
        } else {
            $sex_message = "Bonjour";
        }

        // Encode the email and the key as URL
        $encoded_email = urlencode($email);
        $encoded_key = urlencode($key);

        // Set subject and body
        $subject = "Création de votre compte";
        $message = $sex_message . ",<br/>
        Nous vous confirmons la réception de votre enregistrement sur le site Web de SecureConnect Online, votre
        solution de sécurité informatique.<br/><br/>
        <b>Votre compte requiert une activation.</b><br/><br/>
	    Merci de bien vouloir cliquer sur ce lien ou de le copier/coller dans un navigateur afin de l'activer :
	    <br/><br/>
        https://secureconnect.online/api/auth/activation/email=" . $encoded_email . "&token=" . $encoded_key . "
        <br/>
        <br/>       
        ---------------<br/>
        Ceci est un mail généré automatiquement, merci de ne pas y répondre.
        ";

        $username_mail = $this->email_settings['username'];
        $password_mail = $this->email_settings['password'];

        // Create new PHPMailer
        $mail = new PHPMailer(true);

        try {
            // Define server settings
            $mail->CharSet = 'UTF-8';
            $mail->isSMTP();
            $mail->Host = 'mail.infomaniak.com';
            $mail->SMTPAuth = true;
            $mail->Username = $username_mail;
            $mail->Password = $password_mail;
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;
            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );

            // Define sender and recipients settings
            $mail->setFrom('noreply@secureconnect.online', 'SecureConnect SA');
            //$mail->setFrom('notifications.storagehost@gmail.com', 'SecureConnect SA');
            $mail->addAddress($email);

            // Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $message;
            $mail->send();
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer error: {$mail->ErrorInfo}";
        }
    }
}
