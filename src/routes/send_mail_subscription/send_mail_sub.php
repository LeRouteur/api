<?php

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

class send_mail_sub
{
    protected $email_settings;

    /**
     * Send_mail_sub constructor.
     * @param $email_settings
     */
    public function __construct($email_settings)
    {
        $this->email_settings = $email_settings;
    }

    /**
     * Method used to send an email to the user with a recovery link.
     * @param $lastname
     * @param $sex
     * @param $sub_status
     * @param $email
     * @return bool
     */
    public function sendMail($lastname, $sex, $sub_status, $email)
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

        if ($sub_status === '1') {
            $sub_message = "Service VPN + support continu";
        } elseif ($sub_status === '2') {
            $sub_message = "Service VPN et blocage de publicité + support continu";
        } elseif ($sub_status === '3') {
            $sub_message = "Service VPN, blocage de publicité et gélocalisation IP + support continu";
        }

        // Calculate date of renewal
        setlocale(LC_TIME, "fr_FR");
        $renewal_date = strftime("%d.%m.%Y", strtotime('+ 1 year'));

        // Set subject and body
        $subject = "Votre nouvel abonnement";
        $message = $sex_message . ',<br/>
        Nous vous remercions de votre commande auprès de SecureConnect Online, votre solution de sécurité informatique.<br/><br/>
        <b>Votre abonnement est à présent le suivant :</b><br/><br>
        <b>' . $sub_message . '</b><br/><br/>
        Votre abonnement devra être renouvelé le ' . $renewal_date . '.
        <br/>
        <br/>
        ---------------<br/>
        Ceci est un mail généré automatiquement, merci de ne pas y répondre.
        ';

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