<?php

/** This file contains the required methods to receive and validate the data received from the POST request.
 * @author Cyril Buchs
 * @version 2.5
 */

session_start();

require_once "model_update.php";

class update
{
    protected $email;
    protected $model;
    protected $error_return;
    protected $fields;
    protected $db_arg;
    protected $result;
    private $auth_token;
    private $user_token;

    /**
     * update constructor.
     * @param PDO $pdo
     * @param $auth_token
     */
    public function __construct(PDO $pdo, $auth_token)
    {
        $this->result = array();
        if (!array_key_exists('auth', $_SESSION)) {
            $this->result = '{"error":"User Not Logged"}';
        } elseif (!array_key_exists('email', $_SESSION)) {
            $this->result = '{"error":"User Not Logged"}';
        } elseif (array_key_exists('email', $_SESSION)) {
            $this->email = $_SESSION['email'];
        }
        $this->db_arg = $pdo;
        $this->model = new Model_update($pdo);
        $this->error_return = new ErrorReturn();
        $this->auth_token = "9Gglo9hUi4jHJshNoSP0DamslO4EW5kM";
        $this->user_token = $auth_token;
    }

    /**
     * Method used to validate the auth_token.
     * @return bool|string
     */
    public function checkToken()
    {
        if ($this->auth_token === $this->user_token) {
            return true;
        } else {
            return '{"error":"Bad Request"}';
        }
    }

    /**
     * This function get the POST data. If it's empty, it will die. But, if not (user modified infos from the page), it will call another
     * function to validate the data.
     * @param $data
     * @return array|false|string
     */
    public function getFormDataInfos($data)
    {
        if (!empty($data)) {
            $formDataInfos = [$data["lastname"], $data["firstname"], $data["sex"], $data["address"], $data["zip"], $data["city"], $data['ldap_username']];

            // Trim all spaces
            if (!empty($formDataInfos)) {
                $trimedLastName = trim($formDataInfos[0]);
                $trimedFirstName = trim($formDataInfos[1]);
                $trimedSex = trim($formDataInfos[2]);
                $trimedAddress = trim($formDataInfos[3]);
                $trimedZip = trim($formDataInfos[4]);
                $trimedCity = trim($formDataInfos[5]);
                $trimedFormData = array(
                    $trimedLastName,
                    $trimedFirstName,
                    $trimedSex,
                    $trimedAddress,
                    $trimedZip,
                    $trimedCity
                );
            } else {
                $this->error_array = $this->error_return->returnError("trim");
            }
            // Give an array of unwanted chars
            $unwanted_array = array('Š' => 'S', 'š' => 's', 'Ž' => 'Z', 'ž' => 'z', 'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A', 'Æ' => 'A', 'Ç' => 'C', 'È' => 'E', 'É' => 'E',
                'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ø' => 'O', 'Ù' => 'U',
                'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ý' => 'Y', 'Þ' => 'B', 'ß' => 'Ss', 'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'æ' => 'a', 'ç' => 'c',
                'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i', 'ð' => 'o', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o',
                'ö' => 'o', 'ø' => 'o', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ý' => 'y', 'þ' => 'b', 'ÿ' => 'y');

            // Create a new array who will store the validated values
            $caseFormData = array();

            // Lower firstname and name, and put first word in upper case
            $lastname = $trimedFormData[0];
            $lastname = strtolower($lastname);
            $lastname = ucwords($lastname);

            // Clear the accentuation
            $lastname = strtr($lastname, $unwanted_array);
            $caseFormData[] = $lastname;

            $firstname = $trimedFormData[1];
            $firstname = strtolower($firstname);
            $firstname = ucwords($firstname);

            // Clear the accentuation
            $firstname = strtr($firstname, $unwanted_array);
            $caseFormData[] = $firstname;

            // Lower sex
            $sex = $formDataInfos[2];
            $sex = strtolower($sex);
            $caseFormData[] = $sex;

            // Lower complete address (without ZIP)
            // Also clear comma(s) in address and city
            $address = $trimedFormData[3];
            $address = str_replace(',', '', $address);
            $address = strtolower($address);
            $address = ucwords($address);
            $caseFormData[] = $address;

            // Add ZIP code in the array
            $caseFormData[] = $trimedFormData[4];

            // Add city in the array
            $city = $trimedFormData[5];
            $city = str_replace(',', '', $city);
            $city = strtolower($city);
            $city = ucwords($city);
            $caseFormData[] = $city;

            // Add LDAP username (no validation)
            $ldap_username = $formDataInfos[6];

            // Check if vars are empty
            if (empty($caseFormData[0]) || empty($caseFormData[1]) || empty($caseFormData[2]) || empty($caseFormData[3]) ||
                empty($caseFormData[4]) || empty($caseFormData[5])) {
                $this->result = $this->error_return->returnError("empty");
            }

            $validLastname = $caseFormData[0];
            if (filter_var($validLastname, FILTER_SANITIZE_STRING)) {
                $validLastname = preg_replace('/\d+/u', '', $validLastname);
                $validFormData[] = $validLastname;
            } else {
                $this->result = $this->error_return->returnError("lastname");
            }
            $validFirstname = $caseFormData[1];
            if (filter_var($validFirstname, FILTER_SANITIZE_STRING)) {
                $validFirstname = preg_replace('/\d+/u', '', $validFirstname);
                $validFormData[] = $validFirstname;
            } else {
                $this->result = $this->error_return->returnError("firstname");
            }

            // Validate sex (only 3 possibilities)
            $sex = $formDataInfos[2];
            if ($sex == "man" || $sex == "woman" || $sex == "other") {
                $validFormData[] = $sex;
            } else {
                $this->result = $this->error_return->returnError("sex");
            }

            $validAddress = $caseFormData[3];
            if (preg_match('/[A-Za-z0-9\-,.]+/', $validAddress)) {
                $validFormData[] = $validAddress;
            } else {
                $this->result = $this->error_return->returnError("address");
            }
            // Validate zip code
            $validPostalCode = $caseFormData[4];
            if (filter_var($validPostalCode, FILTER_VALIDATE_INT, array("options" => array("min_range" => 1000, "max_range" => 9999)))) {
                $validFormData[] = $validPostalCode;
            } else {
                $this->result = $this->error_return->returnError("zip");
            }
            // Validate city
            $validCity = $caseFormData[5];
            if (filter_var($validCity, FILTER_SANITIZE_STRING)) {
                $validCity = preg_replace('/\d+/u', '', $validCity);
                $validFormData[] = $validCity;
            } else {
                $this->result = $this->error_return->returnError("city");
            }

            // Add LDAP username
            $validFormData[] = $ldap_username;

            var_dump($validFormData);

            return $validFormData;

        } else {
            die();
        }
    }

    public function updateFields($data)
    {
        if (!empty($data)) {
            $result = $this->model->updateFields($data);
            return $result;
        }
        return null;
    }

    public function returnError()
    {
        if (!empty($this->result)) {
            return $this->result;
        }
        return null;
    }
}
