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

    function create_user()
    {
        $name = $this->name;
        $phone = $this->phone;
        $email = $this->email;
        $password = $this->password;
        $password   = password_hash($password, PASSWORD_DEFAULT);
        $query = 'INSERT INTO users  
        SET
        name = :name,
        phone = :phone,
        email = :email,
        password=:password';
        $stmt = $this->conn->prepare($query);
        $this->name = htmlspecialchars(strip_tags($name));
        $this->phone = htmlspecialchars(strip_tags($phone));
        $this->email = htmlspecialchars(strip_tags($email));
        $this->password = htmlspecialchars(strip_tags($password));
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':phone', $this->phone);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':password', $this->password);
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    function update_user()
    {
        if ($this->user_exists($this->id)) {
            $query = 'UPDATE users u 
            SET 
            u.name = :name,
            u.phone = :phone,
            u.email = :email, 
            u.role_id = :role_id
            WHERE u.id = :id';
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $this->id);
            $stmt->bindParam(':name', $this->name);
            $stmt->bindParam(':phone', $this->phone);
            $stmt->bindParam(':email', $this->email);
            $stmt->bindParam(':role_id', $this->role_id);
            if ($stmt->execute()) {
                $response = array(
                    "code" => 200,
                    "message" => "User Updated."
                );
            } else {
                $response = array(
                    "code" => 500,
                    "message" => "User not Updated."
                );
            }
        } else {
            $response = array(
                "code" => 400,
                "message" => "User does not exist."
            );
        }
        echo json_encode($response);
    }

    function read_SingleUser($id)
    {
        if ($this->user_exists($id)) {
            $result  = $this->fetch_user_by_id($id);
            $user_arr = array(
                'id' => $result['id'],
                'name' => $result['name'],
                'phone' => $result['phone'],
                'email' => $result['email'],
                'password' => $result['password'],
                'created' => $result['created'],
                'role' => $result['role']
            );
            $response = array(
                "code" => 200,
                "message" => $user_arr
            );
        } else {
            $response = array(
                "code" => 400,
                "message" => "User does not exist"
            );
        }
        echo json_encode($response);
    }