<?php

class ErrorReturn
{
    function returnError($error)
    {
        /**$result = array();
         * switch ($error) {
         * case "password":
         * $result['password'] = '{"error":"Password Do Not Match."}';
         * break;
         * case "trim":
         * $result['trim'] = '{"error":"Contact Administrator"}';
         * break;
         * case "firstname":
         * $result['firstname'] = '{"error":"Firstname Invalid"}';
         * break;
         * case "lastname":
         * $result['lastname'] = '{"error":"Lastname Invalid"}';
         * break;
         * case "email":
         * $result['email'] = '{"error":"Email Invalid"}';
         * break;
         * case "sex":
         * $result['sex'] = '{"error":"Sex Invalid."}';
         * break;
         * case "address":
         * $result['address'] = '{"error":"Address Invalid"}';
         * break;
         * case "zip":
         * $result['zip'] = '{"error":"Postal Code Invalid"}';
         * break;
         * case "city":
         * $result['city'] = '{"error":"City Invalid"}';
         * break;
         * case "empty":
         * $result['empty'] = '{"error":"Bad Request"}';
         * break;
         * }
         * return $result;*/
        $result = "";
        switch ($error) {
            case "password":
                $result = '{"error":"Password Does Not Match."}';
                break;
            case "trim":
                $result = '{"error":"Contact Administrator"}';
                break;
            case "firstname":
                $result = '{"error":"Firstname Invalid"}';
                break;
            case "lastname":
                $result = '{"error":"Lastname Invalid"}';
                break;
            case "email":
                $result = '{"error":"Email Invalid"}';
                break;
            case "sex":
                $result = '{"error":"Sex Invalid."}';
                break;
            case "address":
                $result = '{"error":"Address Invalid"}';
                break;
            case "zip":
                $result = '{"error":"Postal Code Invalid"}';
                break;
            case "city":
                $result = '{"error":"City Invalid"}';
                break;
            case "empty":
                $result = '{"error":"Bad Request"}';
                break;
        }
        return $result;
    }
}