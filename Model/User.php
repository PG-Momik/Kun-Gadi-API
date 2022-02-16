<?php
class User
{
    private $conn;
    private $table = 'users';
    public $id;
    public $name;
    public $phone;
    public $email;
    public $password;
    public $con_password;
    public $created;
    public $role_id;
    public $role;
    public $role_created;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    //USER STUFF-------------
    function login()
    {
        if ($this->login_validation()) {
            $this->id = $this->phone_exists($this->phone, "login");
            if (!empty($this->id)) {
                $row = $this->fetch_user($this->id);
                //hash login password to verify
                // $this->password = password_hash($this->password, PASSWORD_DEFAULT);
                if (password_verify($this->password, $row['password'])) {
                    $response = array(
                        "code" => 200,
                        "message" => "Login Successful."
                    );
                } else {
                    $response = array(
                        "code" => 400,
                        "message" => "Invalid password."
                    );
                }
            } else {
                $response = array(
                    "code" => 400,
                    "message" => "Phone number does not exist."
                );
            }
        } else {
            $response = array(
                "code" => 500,
                "message" => "Something went wrong."
            );
        }
        echo json_encode($response);
    }

    function register()
    {
        if ($this->registration_validation()) {
            if ($this->create_user()) {
                $response = array(
                    "code" => 200,
                    "message" => "Registered"
                );
            }
        } else {
            $response = array(
                "code" => 400,
                "message" => "Registration Failed"
            );
        }
        echo json_encode($response);
    }