<?php

/** This file contains the required methods to return the data from the documentation. */

class doc
{
    private $result;
    private $Parsedown;

    /**
     * Doc constructor.
     * @param Parsedown $Parsedown
     */
    public function __construct(Parsedown $Parsedown)
    {
        $this->result = "";
        $this->Parsedown = $Parsedown;
    }

    /**
     * Method used to get the content of a MD file and return it as text with Parsedown.
     * @param $url
     * @return string
     */
    public function returnContent($url)
    {
        switch ($url) {
            case "activation":
                $activation = file_get_contents("../src/routes/doc/activation.md");
                $this->result = $this->Parsedown->text($activation);
                break;
            case "login":
                $login = file_get_contents("../src/routes/doc/login.md");
                $this->result = $this->Parsedown->text($login);
                break;
            case "register":
                $register = file_get_contents("../src/routes/doc/register.md");
                $this->result = $this->Parsedown->text($register);
                break;
            case "show_user":
                $show_user = file_get_contents("../src/routes/doc/show_user.md");
                $this->result = $this->Parsedown->text($show_user);
                break;
            case "subscription":
                $subscription = file_get_contents("../src/routes/doc/subscription.md");
                $this->result = $this->Parsedown->text($subscription);
                break;
            case "update":
                $update = file_get_contents("../src/routes/doc/update.md");
                $this->result = $this->Parsedown->text($update);
                break;
            case "send_email":
                $email_recovery = file_get_contents("../src/routes/doc/send_email.md");
                $this->result = $this->Parsedown->text($email_recovery);
                break;
            case "recovery":
                $recovery = file_get_contents("../src/routes/doc/recovery.md");
                $this->result = $this->Parsedown->text($recovery);
                break;
            case "delete":
                $delete = file_get_contents("../src/routes/doc/delete.md");
                $this->result = $this->Parsedown->text($delete);
                break;
            case "logout":
                $logout = file_get_contents("../src/routes/doc/logout.md");
                $this->result = $this->Parsedown->text($logout);
                break;
        }
        return $this->result;
    }
}