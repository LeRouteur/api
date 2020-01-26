<?php

require "research_status.php";

class Ldap
{
    protected $result;
    protected $result_check_user;
    protected $validFormData;
    private $ldap_creds;
    private $result_delete;
    private $auth_token;
    private $result_user_add;
    private $pdo;

    /**
     * Ldap constructor.
     * @param array $ldap_creds
     * @param $validFormData
     * @param PDO $pdo
     */
    public function __construct(array $ldap_creds, PDO $pdo, $validFormData)
    {
        $this->ldap_creds = $ldap_creds;
        $this->validFormData = $validFormData;
        $this->auth_token = "aQcOuZnmxjJZ0Y8L3aZ0Xv3WYxbVT4Bo";
        $this->pdo = $pdo;
    }

    /**
     * Method used to add and user to an LDAP annuary.
     * @return bool|string
     */
    public function addUser()
    {
        $lastname = strtolower($this->validFormData[0]);
        $firstname = strtolower($this->validFormData[1]);

        $username = $firstname . $lastname;
        $display_name = ucwords($firstname) . " " . ucwords($lastname);

        $unhashed_pass = $this->validFormData[8];

        //$adduserAD["cn"] = $username;
        $adduserAD["givenname"] = ucwords($firstname);
        $adduserAD["sn"] = ucwords($lastname);
        $adduserAD["sAMAccountName"] = $username;
        $adduserAD['userPrincipalName'] = $this->validFormData[2];
        $adduserAD["displayname"] = $display_name;
        $adduserAD["userPassword"] = $unhashed_pass;
        $adduserAD['postalCode'] = $this->validFormData[5];
        // Add city
        $adduserAD['l'] = $this->validFormData[6];
        // Add street address
        $adduserAD['streetAddress'] = $this->validFormData[4];

        // $upn_status will be at true if the user does not exist by it's UPN, and at false if the user exist
        $upn_status = $this->testInsertion();

        if ($upn_status) {

            // Returns true if user does not exist by it's sAM, false if user exists
            $user_exist = $this->checkUser("no");

            if (!$user_exist) {
                $lastname_new = strtolower($this->validFormData[0]);
                $firstname_new = strtolower($this->validFormData[1]);

                // While user exist, change the username (only if email is different)
                $i = 1;

                while (!$user_exist) {
                    $req = false;
                    $username_new = $firstname_new . $lastname_new . $i;
                    $user_status = $this->checkUser($username_new);

                    // If status is true, it means that the user does not exist.
                    if ($user_status) {
                        // User does not exists, OK for creation. Return false
                        //$adduserAD_new["cn"] = $username_new;
                        $adduserAD_new["givenname"] = ucwords($firstname);
                        $adduserAD_new["sn"] = ucwords($lastname);
                        $adduserAD_new["sAMAccountName"] = $username_new;
                        $adduserAD_new['userPrincipalName'] = $this->validFormData[2];
                        $adduserAD_new["displayname"] = $display_name;
                        $adduserAD_new["userPassword"] = $unhashed_pass;
                        $adduserAD_new['postalCode'] = $this->validFormData[5];
                        // Add city
                        $adduserAD_new['l'] = $this->validFormData[6];
                        // Add street address
                        $adduserAD_new['streetAddress'] = $this->validFormData[4];

                        // Check if sAM already exist in the file. If not, let things process. Else, return an error.
                        /**if (strpos(file_get_contents("../src/config/user.txt"), $adduserAD_new['sAMAccountName']) !== false) {
                            // File content the sAM, we return an error.
                            $this->result = '{"error":"User Already Registered"}';
                        } else {*/

                            $handle = fopen('../src/config/user.txt', 'a+');
                            if (flock($handle, LOCK_EX)) {
                                //fwrite($handle, $adduserAD_new['cn'] . ",");
                                fwrite($handle, $adduserAD_new['givenname'] . ",");
                                fwrite($handle, $adduserAD_new['sn'] . ",");
                                fwrite($handle, $adduserAD_new['sAMAccountName'] . ",");
                                fwrite($handle, $adduserAD_new['userPrincipalName'] . ",");
                                fwrite($handle, $adduserAD_new['displayname'] . ",");
                                fwrite($handle, $adduserAD_new['userPassword'] . ",");
                                fwrite($handle, $adduserAD_new['postalCode'] . ",");
                                fwrite($handle, $adduserAD_new['l'] . ",");
                                $req = fwrite($handle, $adduserAD_new['streetAddress'] . PHP_EOL);
                                flock($handle, LOCK_UN);
                            } else {
                                echo "Could not lock the file !";
                            }

                            if (!$req) {
                                //echo "yeet";
                                $this->result = '{"error":"Contact Administrator"}';
                            } else {
                                fclose($handle);
                                $this->result = $username_new;
                            }
                        //}
                        break;
                    } else {
                        // User exists, increasing the number that will be put after the name.
                        $user_exist = false;
                        $i++;
                    }
                }
            } else {

                // Check if sAM already exist in the file. If not, let things process. Else, return an error.
                /**if (strpos(file_get_contents("../src/config/user.txt"), $adduserAD['sAMAccountName']) !== false) {
                    // File content the sAM, we return an error.
                    $this->result = '{"error":"User Already Registered"}';
                } else {*/

                    $handle = fopen('../src/config/user.txt', 'a+');
                    if (flock($handle, LOCK_EX)) {
                        PHP_EOL;
                        //fwrite($handle, $adduserAD['cn'] . ",");
                        fwrite($handle, $adduserAD['givenname'] . ",");
                        fwrite($handle, $adduserAD['sn'] . ",");
                        fwrite($handle, $adduserAD['sAMAccountName'] . ",");
                        fwrite($handle, $adduserAD['userPrincipalName'] . ",");
                        fwrite($handle, $adduserAD['displayname'] . ",");
                        fwrite($handle, $adduserAD['userPassword'] . ",");
                        fwrite($handle, $adduserAD['postalCode'] . ",");
                        fwrite($handle, $adduserAD['l'] . ",");
                        $req_base = fwrite($handle, $adduserAD['streetAddress'] . PHP_EOL);

                        if (!$req_base) {
                            // Insertion unsuccessful, we should prevent the user
                            $this->result = '{"error":"Contact Administrator"}';
                        } else {
                            fclose($handle);
                            $this->result = $username;
                        }
                    } else {
                        echo "Could not lock the file !";
                    }
                //}

            }
        } else {
            $this->result = '{"error":"UPN Should Be Unique"}';
        }

        return $this->result;
    }

    /**
     * Method used to check if it's possible to create a new user with the given parameters.
     * Returns true when insertion was successful (UPN is unique).
     * Returns false when insertion was not successful (UPN already exist).
     * @return bool|null
     */
    private function testInsertion()
    {
        // LDAP variables
        $ldap_username = $this->ldap_creds['username'];
        $ldap_password = $this->ldap_creds['password'];
        $ldapuri = $this->ldap_creds['uri'];

        // Connexion LDAP
        $link_id = ldap_connect($ldapuri);
        if ($link_id) {
            ldap_set_option($link_id, LDAP_OPT_PROTOCOL_VERSION, 3);
            ldap_bind($link_id, $ldap_username, $ldap_password);

            $upn = $this->validFormData[2];

            // We check if the username is already registered
            $base_dn = 'cn=test,OU=Users-VPN,DC=secureconnect,DC=online';

            $adduserAD_new["sAMAccountName"] = 'test';
            $adduserAD_new['userPrincipalName'] = $upn;
            $adduserAD_new["objectClass"] = "user";
            $adduserAD_new["displayName"] = 'test';

            $req = ldap_add($link_id, $base_dn, $adduserAD_new);

            if ($req === true) {
                // User does not exist by it's UPN, we delete it and we then return true.
                ldap_delete($link_id, $base_dn);
                ldap_close($link_id);
                return true;
            } else {
                // User exists, we return false.
                return false;
            }
        } else {
            $this->result = '{"error":"Cannot Connect To Ldap Server"}';
        }
        return null;
    }

    /**
     * Method used to check if the username already exists in the annuary.
     * @param $username_new
     * @return bool|string
     */
    private function checkUser($username_new)
    {
        // LDAP variables
        $ldap_username = $this->ldap_creds['username'];
        $ldap_password = $this->ldap_creds['password'];
        $ldapuri = $this->ldap_creds['uri'];

        // Connexion LDAP
        $link_id = ldap_connect($ldapuri);
        if ($link_id) {
            ldap_set_option($link_id, LDAP_OPT_PROTOCOL_VERSION, 3);
            ldap_set_option($link_id, LDAP_OPT_REFERRALS, 0);

            ldap_bind($link_id, $ldap_username, $ldap_password);

            $lastname = strtolower($this->validFormData[0]);
            $firstname = strtolower($this->validFormData[1]);

            $username = $firstname . $lastname;

            /**
             * No new username, search for the basic (validFormData)
             */
            if ($username_new === "no") {
                // We check if the username is already registered

                $base_dn = 'OU=Users-VPN,DC=secureconnect,DC=online';
                $filter = "(&(objectCategory=person)(objectClass=user)(sAMAccountName=$username))";
                $req = ldap_search($link_id, $base_dn, $filter);
                $result = ldap_get_entries($link_id, $req);

                if (empty($result[0]['samaccountname'][0])) {
                    ldap_close($link_id);
                    $this->result_check_user = true;
                } else {
                    ldap_close($link_id);
                    $this->result_check_user = false;
                }

                /**
                 * Ask to check if new username exists
                 */
            } else {
                $base_dn = 'OU=Users-VPN,DC=secureconnect,DC=online';
                $filter = "(&(objectCategory=person)(objectClass=user)(sAMAccountName=$username_new))";
                $req = ldap_search($link_id, $base_dn, $filter);

                $result = ldap_get_entries($link_id, $req);
                //var_dump($result[0]['samaccountname'][0]);

                if (empty($result[0]['samaccountname'][0])) {
                    ldap_close($link_id);
                    $this->result_check_user = true;
                } else {
                    ldap_close($link_id);
                    $this->result_check_user = false;
                }
            }

        } else {
            $this->result = '{"error":"Cannot Connect To Ldap Server"}';
        }

        return $this->result_check_user;
    }

    /**
     * Method used to add an user to the correct VPN group.
     * En français : la fonction utilisée ici, ldap_mod_add, se place du point de vue du groupe. On lui ajoute un attribut member, qui correspond au membre à ajouter.
     * @param $username
     * @return string
     */
    public function addUserToGroup($username)
    {
        // LDAP variables
        $ldap_username = $this->ldap_creds['username'];
        $ldap_password = $this->ldap_creds['password'];
        $ldapuri = $this->ldap_creds['uri'];

        // Connexion LDAP
        $link_id = ldap_connect($ldapuri);
        if ($link_id) {
            ldap_set_option($link_id, LDAP_OPT_PROTOCOL_VERSION, 3);
            ldap_set_option($link_id, LDAP_OPT_REFERRALS, 0);

            ldap_bind($link_id, $ldap_username, $ldap_password);

            $research = new Research_status($this->pdo);
            $status = $research->search($username);

            // On se place au niveau du groupe, pas de l'utilisateur !

            if ($status[0]['sub_status'] === '0') {
                $this->result_user_add = '{"error":"User Has No Active Subscription"}';
            } elseif ($status[0]['sub_status'] === '1') {
                $dn = 'cn=VPNG1,OU=Groups,OU=Users-VPN,DC=secureconnect,DC=online';
                $group_info['member'] = 'cn=' . $username . ',OU=Users-VPN,DC=secureconnect,DC=online';
                $req = ldap_mod_add($link_id, $dn, $group_info);
                if ($req) {
                    $this->result_user_add = true;
                } else {
                    $this->result_user_add = '{"error":"User Already In Group"}';
                }
            } elseif ($status[0]['sub_status'] === '2') {
                $dn = 'cn=VPNG2,OU=Groups,OU=Users-VPN,DC=secureconnect,DC=online';
                $group_info['member'] = 'cn=' . $username . ',OU=Users-VPN,DC=secureconnect,DC=online';
                $req = ldap_mod_add($link_id, $dn, $group_info);
                if ($req) {
                    $this->result_user_add = true;
                } else {
                    $this->result_user_add = '{"error":"User Already In Group"}';
                }
            } elseif ($status[0]['sub_status'] === '3') {
                $dn = 'cn=VPNG3,OU=Groups,OU=Users-VPN,DC=secureconnect,DC=online';
                $group_info['member'] = 'cn=' . $username . ',OU=Users-VPN,DC=secureconnect,DC=online';
                $req = ldap_mod_add($link_id, $dn, $group_info);
                if ($req) {
                    $this->result_user_add = true;
                } else {
                    $this->result_user_add = '{"error":"User Already In Group"}';
                }
            } elseif ($status[0]['sub_status'] === '4') {
                $this->result_user_add = '{"error":"You Are Already An Administrator"}';
            } else {
                $this->result_user_add = $status;
            }
        }
        return $this->result_user_add;
    }

    /**
     * Method used to delete an user in the AD by it's username.
     * @param $username
     * @param $auth_token
     * @return bool|string
     */
    public function deleteUser($username, $auth_token)
    {
        // LDAP variables
        $ldap_username = $this->ldap_creds['username'];
        $ldap_password = $this->ldap_creds['password'];
        $ldapuri = $this->ldap_creds['uri'];

        // Connexion LDAP
        $link_id = ldap_connect($ldapuri);
        if ($link_id) {
            ldap_set_option($link_id, LDAP_OPT_PROTOCOL_VERSION, 3);
            ldap_set_option($link_id, LDAP_OPT_REFERRALS, 0);

            ldap_bind($link_id, $ldap_username, $ldap_password);

            // Check auth_token
            if ($this->auth_token === $auth_token) {
                // Deletion of the user from the AD
                $base_dn = 'cn=' . $username . ',OU=Users-VPN,DC=secureconnect,DC=online';
                $req = ldap_delete($link_id, $base_dn);
                if ($req) {
                    $this->result_delete = '{"success":"User Deleted"}';
                } else {
                    $this->result_delete = '{"error":"User Does Not Exist"}';
                }
                ldap_close($link_id);
            } else {
                $this->result_delete = '{"error":"Bad Request"}';
            }
        }
        return $this->result_delete;
    }
}
