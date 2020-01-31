<?php

session_start();

class Logout
{

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
