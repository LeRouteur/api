<?php

class ErrorReturn
{
    function returnError($error)
    {
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
            case "pass_req":
		$result = '{"error":"Password Does Not Meet The Requirements"}';
		break;
            case "empty":
                $result = '{"error":"Bad Request"}';
                break;
        }
        return $result;
    }
}
