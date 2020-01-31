<?php

require_once "model_register.php";
require "error_return.php";

class Register
{
    protected $db;
    protected $model;
    protected $email_settings = array();
    private $error_return;
    private $result;

    /**
     * Register constructor.
     * @param PDO $pdo
     * @param array $email_settings
     */
    public function __construct(PDO $pdo, array $email_settings)
    {
        $this->db = $pdo;
        $this->error_return = new ErrorReturn();
        $this->email_settings = $email_settings;
        $this->model = new Model_register($this->db, $this->email_settings);
        $this->result = array();
    }

    /**
     * Method used to receive the data and validate it.
     * @param $data
     * @return array
     */
    function getFormData($data)
    {
        if (!empty($data)) {
            $formData = [$data['lastname'], $data['firstname'], $data['mail'], $data['sex'], $data['address'], $data['zip'], $data['city'], $data['password'], $data['password_conf']];
            //var_dump($formData);

            // Get the password and the password confirmation from the array and assign it to local var
            $password = $formData[7];
            $password_conf = $formData[8];

            // Compare the two strings
            if ($password == $password_conf) {
                $final_password = $password;

                $uppercase = preg_match('@[A-Z]@', $final_password);
                $lowercase = preg_match('@[a-z]@', $final_password);
                $number    = preg_match('@[0-9]@', $final_password);
                //$specialChars = preg_match('@[^\w]@', $password);

                if ($uppercase && $lowercase && $number && strlen($final_password) >= 8) {
                    // Password is valid
                    // Remove password and password conf from the array, and insert the valid password in it
                    array_splice($formData, 8);

                    // Add the correct password in the array (it will be hashed)
                    $formData[] = $final_password;
                } else {
                    $this->result = $this->error_return->returnError("pass_req");
                }

            } else {
                // If password isn't correct, delete the array and print error
                $this->result = $this->error_return->returnError("password");
            }

            // Trim all spaces
            if (!empty($formData)) {
                $trimedLastName = trim($formData[0]);
                $trimedFirstName = trim($formData[1]);
                $trimedEmail = trim($formData[2]);
                $trimedSex = trim($formData[3]);
                $trimedAddress = trim($formData[4]);
                $trimedZip = trim($formData[5]);
                $trimedCity = trim($formData[6]);
                $trimedFormData = array(
                    $trimedLastName,
                    $trimedFirstName,
                    $trimedEmail,
                    $trimedSex,
                    $trimedAddress,
                    $trimedZip,
                    $trimedCity
                );
            } else {
                $this->result = $this->error_return->returnError("trim");
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

            // Lower email address (email cannot have any upper case letter)
            $email = $formData[2];
            $email = strtolower($email);
            $caseFormData[] = $email;

            // Lower sex
            $sex = $formData[3];
            $sex = strtolower($sex);
            $caseFormData[] = $sex;

            // Lower complete address (without ZIP)
            // Also clear comma(s) in address and city
            $address = $trimedFormData[4];
            $address = str_replace(',', '', $address);
            $address = strtolower($address);
            $address = ucwords($address);
            $caseFormData[] = $address;

            // Add ZIP code in the array
            $caseFormData[] = $trimedFormData[5];

            // Add city in the array
            $city = $trimedFormData[6];
            $city = str_replace(',', '', $city);
            $city = strtolower($city);
            $city = ucwords($city);
            $caseFormData[] = $city;
        }

        $password = filter_var($formData[7], FILTER_SANITIZE_STRING);

        // Hash the password before inserting it in the DB
        $hashedPass = password_hash($password, PASSWORD_DEFAULT);

        // The password is added at index 7
        $caseFormData[] = $hashedPass;

        $caseFormData[] = $formData[8];

        // Check if vars are empty
        if (empty($caseFormData[0]) || empty($caseFormData[1]) || empty($caseFormData[2]) || empty($caseFormData[3]) ||
            empty($caseFormData[4]) || empty($caseFormData) || empty($caseFormData[5]) || empty($caseFormData[6]) ||
            empty($caseFormData[7]) || empty($caseFormData[8])) {
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

        // Validate email
        $validEmail = $caseFormData[2];
        if (filter_var($validEmail, FILTER_VALIDATE_EMAIL, FILTER_SANITIZE_EMAIL)) {
            $validFormData[] = $validEmail;
        } else {
            $this->result = $this->error_return->returnError("email");
        }

        // Validate sex (only 3 possibilities)
        $sex = $caseFormData[3];
        if ($sex == "man" || $sex == "woman" || $sex == "other") {
            $validFormData[] = $sex;
        } else {
            $this->result = $this->error_return->returnError("sex");
        }

        $validAddress = $caseFormData[4];
        if (preg_match('/[A-Za-z0-9\-,.]+/', $validAddress)) {
            $validFormData[] = $validAddress;
        } else {
            $this->result = $this->error_return->returnError("address");
        }
        // Validate zip code
        $validPostalCode = $caseFormData[5];
        if (filter_var($validPostalCode, FILTER_VALIDATE_INT, array("options" => array("min_range" => 1000, "max_range" => 9999)))) {
            $validFormData[] = $validPostalCode;
        } else {
            $this->result = $this->error_return->returnError("zip");
        }
        // Validate city
        $validCity = $caseFormData[6];
        if (filter_var($validCity, FILTER_SANITIZE_STRING)) {
            $validCity = preg_replace('/\d+/u', '', $validCity);
            $validFormData[] = $validCity;
        } else {
            $this->result = $this->error_return->returnError("city");
        }

        //Add the hashed password in the array
        $validFormData[] = $caseFormData[7];

        $validFormData[] = $caseFormData[8];

        //var_dump($validFormData);

        return $validFormData;
    }

    public function returnError()
    {
        if (!empty($this->result)) {
            return $this->result;
        }
        return null;
    }

    /**
     * Method used to check if the user can be registered in the DB. It checks returns values of methods from the model,
     * and returns a status that would be received by the front controller.
     * @param array $validFormData
     * @return bool
     */
    public function registerStatus(array $validFormData)
    {
        // Check if mail already exists in the DB
        $response_db = $this->model->checkEmailExistence($validFormData);
        if ($response_db) {
            // Email does not exist
            return true;
        } else {
            // Email exists
            return false;
        }
    }
}





