<?php

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

class send_mail_recovery
{
    protected $email_settings;

    /**
     * Send_mail_recovery constructor.
     * @param $email_settings
     */
    public function __construct($email_settings)
    {
        $this->email_settings = $email_settings;
    }

    /**
     * Method used to check if the auth_token matches the one in the code.
     * @param $auth_token
     * @return bool
     */
    public function checkToken($auth_token)
    {
        if ($auth_token === "BVYTzsAKSeqYlmZKxTX6ZiSFoweGoD06") {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Method used to send an email to the user with a recovery link.
     * @param $lastname
     * @param $sex
     * @param $email
     * @param $token
     * @return bool
     */
    public function sendMail($lastname, $sex, $email, $token)
    {
        // Get the sex of the user. If it's man, it will show "Bonjour Monsieur" ; if it's woman, it'll show "Bonjour Madame"
        // and if it's "other", it'll just show "Bonjour"...;
        if ($sex == "man") {
            $sex_message = "Bonjour Monsieur " . $lastname;
        } elseif ($sex == "woman") {
            $sex_message = " Bonjour Madame " . $lastname;
        } else {
            $sex_message = "Bonjour";
        }

        // Encode the email and the key as URL
        $encoded_email = urlencode($email);
        $encoded_token = urlencode($token);

        // ADD THIS ONE IN THE MAIL WHEN PUTTING IN PROD !!
        //?email=" . $encoded_email . "&token=" . $encoded_token .

        // Set subject and body
        $subject = "Changement de votre mot de passe";
        $message = $sex_message . ",<br/>
        Nous avons reçu une demande de changement de mot de passe de votre part.<br/><br/>
        <b>Veuillez cliquer ou copier/coller le lien ci-dessous dans un navigateur et suivre les instructions à l'écran.</b><br/><br/>
        https://secureconnect.online/api/recovery?mail=" . $encoded_email . "&token=" . $encoded_token . "
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

            // Define sender and recipients settings
            $mail->setFrom('noreply@secureconnect.online', 'SecureConnect SA');
            //$mail->setFrom('notifications.storagehost@gmail.com', 'SecureConnect SA');
            $mail->addAddress($email);

            // Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $message;
            $mail->send();
            return true;
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer error: {$mail->ErrorInfo}";
            return false;
        }
    }
}
