<?php

/** This file contains the required methods
 * @author Cyril Buchs
 * @version 1.1
 */

class Logout
{
    private $result;

    /**
     * Logout constructor.
     */
    public function __construct()
    {
        $this->result = "";
        $logout_result = $this->logout();
        return $logout_result;
    }

    private function logout()
    {
        if ($_SESSION['auth'] === true && !empty($_SESSION['email'])) {
            session_destroy();
            $result = '{"success":"User Logged Out"}';
        } else {
            $result = '{"error":"User Not Logged"}';
        }
        return $result;
    }
}
