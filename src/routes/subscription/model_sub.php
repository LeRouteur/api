<?php


class model_sub
{
    protected $pdo;
    protected $result_db;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->result_db = array(
            'changed' => false,
            'error_db' => ""
        );
    }

    public function updateStatus($new_status, $email)
    {
        try {
            $req = $this->pdo->prepare("UPDATE tip.user SET sub_status = :sub_status WHERE email = :email");
            $req->execute(array(
                ':sub_status' => $new_status,
                ':email' => $email
            ));
            if ($req) {
                $this->result_db['changed'] = true;
            } else {
                $this->result_db['error_db'] = '{"error":"Contact Administrator"}';
            }
        } catch (PDOException $e) {
            $this->result_db['error_db'] = '{"error":' . $e->getMessage() . '}';
        }
        return $this->result_db;
    }
}