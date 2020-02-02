<?php

/** This file contains the required methods
 * @author Cyril Buchs
 * @version 1.0
 */

session_start();

class Logout
{

    /**
     * Logout constructor.
     */
    public function __construct()
    {
        $result = "";
        if ($_SESSION['auth'] === true && !empty($_SESSION['email'])) {
            session_destroy();
            $result = '{"success":"User Logged Out"}';
        } else {
            $result =  '{"error":"User Not Logged"}';
        }
        return $result;
    }
}
