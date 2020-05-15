<?php

require "../vendor/autoload.php";

use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Slim\App;
use \Slim\Container;

require "../src/routes/register/register.php";
require "../src/routes/update/update.php";
require "../src/routes/activation/model_act.php";
require "../src/routes/login/login.php";
require "../src/routes/research/research.php";
require "../src/routes/auth.php";
require "../src/routes/doc/doc.php";
require "../src/routes/send_mail_recovery/send_mail_recovery.php";
require "../src/routes/subscription/subscription.php";
require "../src/routes/delete/delete.php";
require "../src/routes/send_mail_subscription/send_mail_sub.php";
require "../src/config/research_mail.php";
require "../src/routes/register/model_add_ldap_username.php";
require "../src/routes/logout/logout.php";
require_once "../src/routes/recovery/recovery.php";
include "../src/config/Parsedown.php";
include "../src/config/ldap.php";

/**********************************************************************************************************************/

/**
 * GENERAL PART
 */

$configuration = [
    'settings' => [
        'displayErrorDetails' => true,
    ],
];

$c = new Container($configuration);

// Instantiate the Slim App class with a $c container (configuration)
$app = new App($c);

// Call the method getContainer with the instance of App and assign it to a local var
$container = $app->getContainer();

// Create the container indices functions
$container['pdo'] = function () {
    $db_data = parse_ini_file("../src/config/db.ini");
    $username = $db_data['user'];
    $password = $db_data['pass'];
    $host = $db_data['host'];
    $dbname = $db_data['dbname'];
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    return $pdo;
};
$container['mail'] = function () {
    $email_data = parse_ini_file("../src/config/php_mail.ini");
    $email_settings = array(
        'username' => $email_data['username'],
        'password' => $email_data['password']
    );
    return $email_settings;
};
$container['parsedown'] = function () {
    return $Parsedown = new Parsedown();
};
$container['ldap'] = function () {
    $ldap_data = parse_ini_file("../src/config/ldap.ini");
    $ldap_creds = array(
        'username' => $ldap_data['username'],
        'password' => $ldap_data['password'],
        'uri' => $ldap_data['uri']
    );
    return $ldap_creds;
};

/**********************************************************************************************************************/

/**
 * DOCUMENTATION PART
 */

/**
 * Route for showing API documentation.
 * @param Request $request
 * @param Response $response
 * @return Response
 */
$app->get('/api[/doc/{file}]', function (Request $request, Response $response, $args) {
    // Get the body of the response message
    $body = $response->getBody();

    // Check if $args is empty, we return the index.md page, that is the front page of the doc. Else, we return the /doc/$file
    if (empty($args)) {

        // Get the content of the index.md file
        $index = file_get_contents('../src/routes/doc/index.md');

        // Convert the content to the with Parsedown
        $result = $this->parsedown->text($index);

        // Write the result to the body
        $body->write($result);

        // Return $response with the body
        $response = $response->withStatus(200)->withBody($body);
    } else {
        $file = $args['file'];
        if ($file === "activation" || $file === "login" || $file === "register" || $file === "show_user" || $file === "subscription" || $file === "update" || $file === "send_email" || $file === "recovery" || $file === "delete" || $file === "logout") {

            // Instantiate the Doc class with the Parsedown instance
            $doc = new Doc($this->parsedown);

            // Call the returnContent method with $file and assign the return value to a local var
            $result = $doc->returnContent($file);

            // Write the result to the response body
            $body->write($result);

            // Return $response with the body
            $response = $response->withStatus(200)->withBody($body);
        } else {
            // Return a 404 Not Found
            $response = new \Slim\Http\Response(404);
            $response->write("Page not found");
        }
    }
    return $response;
});

/**********************************************************************************************************************/

/**
 * AUTH PART
 */

/**
 * Route for user account activation.
 * @param Request $request
 * @param Response $response
 * @return Response
 */
$app->get('/api/auth/activation/email={log}&token={key}', function (Request $request, Response $response, $args) {

    // Assign $args values to local vars
    $log = $args['log'];
    $key = $args['key'];

    // Instantiate the Model_act class with PDO instance
    $activation = new Model_act($this->pdo);

    // Call the activateAccount method with $log and $key and assign the return value to a local var
    $response_db = $activation->activateAccount($log, $key);

    // Check the value of $response_db. If it contains an error, we return the error with a 400. Else, we return the result with a 200 (Activation successful OR already enabled !)
    if ($response_db === '{"error":"Bad Request"}') {
        $response = $response->withStatus(400)->withJson($response_db);
    } else {
        $response = $response->withStatus(200)->withJson($response_db);
    }
    return $response;
});

/**
 * Route for user login.
 * @param Request $request
 * @param Response $response
 * @return Response
 */
$app->post('/api/auth/login', function (Request $request, Response $response) {
    $data = $request->getParsedBody();

    // Check the size of the parsed data array. If it equals 2, we continue. Else, we return a 400.
    if (count($data) === 2) {

        // Check if the $data contains mail and password index. If yes, we continue. Else, we return a 400.
        if ($data['mail'] && $data['password']) {

            // Instantiate the Login class with PDO instance
            $login = new Login($this->pdo);

            // Call the getFormData method with $data (request data) and assign the return value to a local var
            $response_db = $login->getFormData($data);

            // If $response_db equals '{"error":"Username Or Password Is Incorrect"}', we return a 401. Else, we return a 200
            if ($response_db === '{"error":"Username Or Password Is Incorrect"}') {
                $response = $response->withStatus(401)->withJson($response_db);
            } else {
                $response = $response->withStatus(200)->withJson($response_db);
            }
        } else {
            $response = $response->withStatus(400)->withJson('{"error":"Bad Request"}');
        }
    } else {
        $response = $response->withStatus(400)->withJson('{"error":"Bad Request"}');
    }
    return $response;
});

/**
 * Route for user e-mail sending (password recovery).
 * @param Request $request
 * @param Response $response
 * @return Response
 */
$app->post('/api/auth/sendmail', function (Request $request, Response $response) {
    $data = $request->getParsedBody();

    // Check if the array of parsed data is empty, and assign the values to local vars. Else, return a 400
    if (!empty($data) && $data['mail'] && $data['auth_token']) {
        $email = $data['mail'];
        $auth_token = $data['auth_token'];

        // Instantiate the Sned_mail_recovery class with e-mail settings
        $send_mail = new Send_mail_recovery($this->mail);

        // Call the method checkToken with auth_token
        $status = $send_mail->checkToken($auth_token);

        // Check the value of $status. If it's true, it means that the tokens are matching, we continue processing. If not, we return a 400
        if ($status) {

            // Instantiate the Research class with PDO instance, e-mail to research and auth_token
            $research = new Research($this->pdo, $email, $auth_token);

            // Call the method search with the "send_mail" parameter and assign the return value to a local var
            $research_data = $research->search("send_mail");

            // Call the sendMail method with $research_data['lastname'], $research_data['sex'], $research_data['email'], $research_data['token'] and assign the return value to a local var
            $response_db = $send_mail->sendMail($research_data['lastname'], $research_data['sex'], $research_data['email'], $research_data['token']);

            // Check the $response_db value. If it's true, we return a 200. Else, we return a 500
            if ($response_db) {
                $response = $response->withStatus(200)->withJson('{"success":"Mail Sent"}');
            } else {
                $response = $response->withStatus(500)->withJson('{"error":"Contact Administrator"}');
            }
        } else {
            $response = $response->withStatus(400)->withJson('{"error":"Bad Request"}');
        }

    } else {
        $response = $response->withStatus(400)->withJson('{"error":"Bad Request"}');
    }
    return $response;
});

/**
 * Route for logout.
 * @param Request $request
 * @param Response $response
 * @return Response
 */
$app->get('/api/auth/logout', function (Request $request, Response $response) {
    // Logout the user
	$logout = new Logout();
	if (strpos($logout, "error")) {
        $response = $response->withStatus(400)->withJson($logout);
    } else {
	    $response = $response->withStatus(200)->withJson($logout);
    }
	return $response;
});

/**********************************************************************************************************************/

/**
 * USER PART
 */

/**
 * Route for user data research.
 * @param Request $request
 * @param Response $response
 * @return Response
 */
$app->get('/api/user/request/email={email}&auth_token={auth_token}', function (Request $request, Response $response, $args) {

    // Check if args are empty
    if (!empty($args) && $args['email'] && $args['auth_token']) {

        // Assign value of $args to local vars
        $email = $args['email'];
        $auth_token = $args['auth_token'];

        // Instantiate Research with PDO instance, e-mail to research and auth_token
        $research = new Research($this->pdo, $email, $auth_token);

        // Call the checkToken method and assign the return value to a local var
        $check_token = $research->checkToken();

        // If $check_token equals true it means that the token is OK. If not, we send an error with a 400 status
        if ($check_token) {

            // Call the search method and assign the return value to a local var
            $response_db = $research->search("user");

            // If $response_db equals '{"error":"Bad request"}', it means that there is an error with the request.
            // Return the error with a 400 status
            if ($response_db === '{"error":"Bad request"}') {
                $response = $response->withStatus(400)->withJson('{"error":"Bad request"}');
            } else {
                $response = $response->withStatus(200)->withJson($response_db);
            }
        } else {
            $response = $response->withStatus(400)->withJson('{"error":"Bad request"}');
        }
    } else {
        // Return error because e-mail to research or auth_token is invalid
        $response = $response->withStatus(400)->withJson('{"error":"Bad request"}');
    }

    return $response;
});

/**
 * Route for user creation.
 * @param Request $request
 * @param Response $response
 * @return Response
 */
$app->post('/api/user/add', function (Request $request, Response $response) {
    $data = $request->getParsedBody();

    // Instantiate the Register class with PDO instance and mail credentials
    $register = new Register($this->pdo, $this->mail);

    // Call the getFormData method with $data and assign the return value to a local var
    $validFormData = $register->getFormData($data);

    // Get the errors and assign it to a local var
    $errors = $register->returnError();

    // Check if there are errors from the returnError function. If so, we return a 400 with the errors. Else, we continue processing
    if (empty($errors)) {
        // No errors, user does not exist
        $check_existence = $register->registerStatus($validFormData);
        //$check_existence = true;

        if ($check_existence) {

            // User inserted in the DB, we can now add it to the text file for LDAP insertion

            // Instantiate the Ldap class with LDAP creds, PDO instance and validFormData
            $ldap = new Ldap($this->ldap, $this->pdo, $validFormData);

            // Call the addUser method and assign the return value to a local var
            $result_ldap = $ldap->addUser();

            // If $result_ldap contains error, it means that the user exists OR there is a communication error between the API and the LDAP server
            // We so return a 400 with the error. The user is deleted from the DB.
            // Else, we continue processing
            if (strpos($result_ldap, "error")) {
                // Delete user from the DB
                $delete = new Delete($this->pdo, "", "");
                $delete_status = $delete->deleteDbUser($validFormData[2]);

                if (strpos($delete_status, "error")) {
                    $response = $response->withStatus(500)->withJson($delete_status);
                } else {
                    // Return the error with a 400
                    $response = $response->withStatus(400)->withJson($result_ldap);
                }
            } else {
                //var_dump($result_ldap);
                // Add the LDAP username in the created field of the DB
                $add_username = new Model_add_ldap_username($this->pdo, $result_ldap, $validFormData[2]);
                $result_db = $add_username->addUsername();

                if ($result_db) {
                    // Username inserted
                    // User is successfully created, return a 201 with a gentle happy message :)
                    $response = $response->withStatus(201)->withJson('{"success":"User Created"}');
                } else {
                    $response = $response->withStatus(500)->withJson('{"error":"Contact Administrator"}');
                }
            }
        } else {
            // Email exist, user already registered
            $response = $response->withStatus(200)->withJson('{"error":"User Already Registered"}');
        }
    } else {
        // If there are errors, show it with a 400 status code
        $response = $response->withStatus(400)->withJson($errors);
    }

    return $response;
});

/**
 * Route for user information update.
 * @param Request $request
 * @param Response $response
 * @return Response
 */
// Flemme de commenter, personne va le lire de toute façon...
$app->post('/api/user/update', function (Request $request, Response $response) {
    $data = $request->getParsedBody();
    if (!$data['auth_token']) {
        $response = $response->withStatus(400)->withJson('{"error":"Bad Request"}');
    } else {
        $auth_token = $data['auth_token'];
        $update = new Update($this->pdo, $auth_token);
        $token_status = $update->checkToken($data);
        if ($token_status) {
            $validFormData = $update->getFormDataInfos($data);
            $errors = $update->returnError();
            if (empty($errors)) {
                $response_db = $update->updateFields($validFormData);
                $response = $response->withStatus(200)->withJson($response_db);
            } else {
                $response = $response->withStatus(400)->withJson($errors);
            }
        }
    }
    return $response;
});

/**
 * Route for user password recovery.
 * @param Request $request
 * @param Response $response
 * @return Response
 */
$app->post('/api/user/recovery/email={mail}&token={token}', function (Request $request, Response $response, $args) {
    $email = $args['mail'];
    $user_token = $args['token'];

    // Get the parsed response body
    $data = $request->getParsedBody();

    // Check if $data is empty and if keys exists
    if (!empty($data) && $data['password'] && $data['password_conf'] && $data['auth_token']) {
        $password = $data['password'];
        $password_conf = $data['password_conf'];
        $auth_token = $data['auth_token'];

        // Check if the two password are matching
        if ($password === $password_conf) {

            // Create a new instance of the Auth class with the instance of PDO given to the constructor
            $auth = new Auth($this->pdo);

            // Checking if the token is the different tokens are corresponding, and storing the result in an array
            $auth_settings = $auth->checkToken($user_token, $email, $auth_token);

            // Check if there is an error, and return it to the user
            if ($auth_settings['error']) {
                $response = $response->withStatus(200)->withJson($auth_settings['error']);
            } else {
                // We continue processing the recovery
                $recovery = new Recovery($this->pdo, $auth_settings);
                $response_yeet = $recovery->getFormDataRecovery($email, $password);
                if ($response_yeet === '{"error":"Contact Administrator"}') {
                    $response = $response->withStatus(500)->withJson($response_yeet);
                } else {
                    $response = $response->withStatus(200)->withJson($response_yeet);
                }
            }
        } else {
            $response = $response->withStatus(401)->withJson('{"error":"Password Does Not Match"}');
        }
    } else {
        $response = $response->withStatus(400)->withJson('{"error":"Bad Request"}');
    }

    return $response;
});

/**
 * Route for user subscription status changing.
 * @param Request $request
 * @param Response $response
 * @return Response
 */
$app->post('/api/user/subscription', function (Request $request, Response $response) {
    // Get the request body and parse it, then assign it to a local var
    $data = $request->getParsedBody();

    // Check if the request body isn't empty, and assign the values to local vars
    if (!empty($data) && $data['status'] && $data['auth_token'] && $data['mail'] && $data['username']) {
        $status = $data['status'];
        $auth_token = $data['auth_token'];
        $email = $data['mail'];
        // LDAP username
        $username = $data['username'];

        // Instantiate the Subscription class with PDO instance and auth_token
        $subscription = new Subscription($this->pdo, $auth_token);
        $result = $subscription->checkToken();

        // Check the value of $result. If it's true, it means that the tokens are matching, we continue processing. Else,
        // we return a 400.
        if ($result) {

            // Call the method validateData with status and email, and assign the result to a local var
            $response_db = $subscription->validateData($status, $email);

            // Check the value of $response_db. If it contains true at the index changed, we continue processing. Else,
            // we return the errors to the user
            if ($response_db['changed']) {

                // Instantiate the Ldap class with LDAP creds, PDO instance and an empty string as validFormData
                $ldap = new Ldap($this->ldap, $this->pdo, "");

                // Call the addUserToGroup method with LDAP username, and assign the result to a local var
                //$result_ldap = $ldap->addUserToGroup($username);
                $result_ldap = $ldap->addUserToGroup($username);

                // Check if $result_ldap contains error. If yes, we return an HTTP response with a 400 and the error. If not, we continue processing
                if (strpos($result_ldap, "error")) {
                    $response = $response->withStatus(400)->withJson($result_ldap);
                } elseif ($result_ldap = true) {

                    // Instantiate the Research_mail class with PDO instance
                    $research = new Research_mail($this->pdo);

                    // Call the method search with email as parameter and assign the result to a local var (array)
                    $result_mail = $research->search($email);

                    // Instantiate the Send_mail_sub class with mail credentials
                    $mail = new Send_mail_sub($this->mail);

                    // Call the sendMail method with lastname, sex, sub_status and email, and assign the result to a local var
                    $response_mail = $mail->sendMail($result_mail[0]['lastname'], $result_mail[0]['sex'], $result_mail[0]['sub_status'], $email);

                    // Check the value of $response_mail. If it's true, we return a 200 with a success message. Else, we return a 500 (internal error, mail cannot be sent)
                    if ($response_mail) {
                        $response = $response->withStatus(200)->withJson('{"success":"Status Updated"}');
                    } else {
                        $response = $response->withStatus(500)->withJson('{"error":"Contact Administrator"}');
                    }
                } else {
                    $response = $response->withStatus(500)->withJson($result_ldap);
                }
                // Check if there is an error from the controller, and return it with a 400
            } elseif (!empty($response_db['error'])) {
                $response = $response->withStatus(400)->withJson($response_db['error']);
                // Check if there is an error from the model, and return it with a 500 (critical error)
            } elseif (!empty($response_db['error_db'])) {
                $response = $response->withStatus(500)->withJson($response_db['error_db']);
            }
            // Tokens are not matching, we return a 400
        } else {
            $response = $response->withStatus(400)->withJson('{"error":"Bad Request"}');
        }
        // POST data is empty or incomplete, we return a 400
    } else {
        $response = $response->withStatus(400)->withJson('{"error":"Bad Request"}');
    }
    return $response;
});

/**
 * Route for user deletion.
 * @param Request $request
 * @param Response $response
 * @return Response
 */
$app->post('/api/user/delete', function (Request $request, Response $response) {
    $data = $request->getParsedBody();

    // Assign parsed values to local vars
    if (!empty($data) && $data['username'] && $data['auth_token']) {
        $username = $data['username'];
        $auth_token = $data['auth_token'];
        $null_data = array();

        // Instantiate the Delete class with PDO instance, username and auth_token as parameters
        $delete = new Delete($this->pdo, $username, $auth_token);

        // Call the method deleteUser and assign the return value to a local var
        $result = $delete->deleteUser();

        // Check if user was successfully deleted ($result). If yes, we continue processing the deletion in the LDAP annuary
        // If not, we crache un message d'erreur with a 400 status code.
        if ($result) {

            // Instantiate the Ldap class with LDAP credentials and an empty array
            $ldap = new Ldap($this->ldap, $this->pdo, $null_data);

            // Call the deleteUser method with username and auth_token (double verification)
            $response_db = $ldap->deleteUser($username, $auth_token);

            // Check the status of the deletion ($response_db)
            // If it contains an error, return it with a 400 status.
            // If not, return the response with a 200 OK.
            if ($response_db === '{"error":"Bad Request"}' || $response_db === '{"error":"User Does Not Exist"}') {
                $response = $response->withStatus(400)->withJson($response_db);
            } else {
                $response = $response->withStatus(200)->withJson($response_db);
            }
        } else {
            $response = $response->withStatus(500)->withJson($result);
        }
    }
    return $response;
});

/**********************************************************************************************************************/

/**
 * Running the app
 */

$app->run();
