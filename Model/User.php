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

    function read_AllUser()
    {
        $result = $this->readAllUsers();
        $num = $result->rowCount();
        if ($num) {
            $user_arr = array();
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                $user_item = array(
                    'id' => $id,
                    'name' => $name,
                    'phone' => $phone,
                    'email' => $email,
                    'password' => $password,
                    'role_id' => $rid,
                    'role' => $role
                );
                array_push($user_arr, $user_item);
            }
            $response = array(
                "code" => 200,
                "message" => $user_arr
            );
        } else {
            $response = array(
                "code" => 500,
                "message" => "No data"
            );
        }
        echo json_encode($response);
    }

    function deleteUser($id)
    {
        if ($this->user_exists($id)) {
            $query = 'DELETE  FROM ' . $this->table . ' WHERE id = :id';
            $stmt = $this->conn->prepare($query);
            $this->id = htmlspecialchars(strip_tags($id));
            $stmt->bindParam(':id', $this->id);
            if ($stmt->execute()) {
                return true;
            } else {
                return false;
            }
        }
    }

    function read_XUser($page)
    {
        $limit = 10;
        $start = ($page - 1) * $limit;
        $query = 'SELECT 
        u.id, 
        u.name, 
        u.phone, 
        u.email, 
        u.password, 
        u.role_id as rid, 
        r.name as role 
        FROM users u 
        JOIN roles r on u.role_id =  r.id
        ORDER BY u.created ASC LIMIT ' . $start . ', ' . $limit;
        $stmt = $this->conn->prepare($query);
        if ($stmt->execute()) {
            $user_arr = array();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                $user_item = array(
                    'id' => $id,
                    'name' => $name,
                    'phone' => $phone,
                    'email' => $email,
                    'password' => $password,
                    'role_id' => $rid,
                    'role' => $role
                );
                array_push($user_arr, $user_item);
            }
            $response = array(
                "code" => 200,
                "message" => $user_arr
            );
        } else {
            $response = array(
                "code" => 500,
                "message" => "No data"
            );
        }
        echo json_encode($response);
    }

    function registration_validation()
    {
        $errors = [];
        if (empty($this->name)) {
            $error[] = "Name is required.";
        }
        if (!preg_match("/^[a-zA-Z-' ]*$/", $this->name)) {
            $error[] = "Invalid Name.";
        }
        if (empty($this->phone)) {
            $error[] = "Phone is required.";
        }
        if ($this->phone_exists($this->phone)) {
            $errors[] = "Sorry that Username is already is taken";
        }
        if (empty($this->email)) {
            $error[] = "Email is required.";
        }
        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $error[] = "Invalid email format.";
        }
        if (strlen($this->password) < 8) {
            $errors[] = "Your Password cannot be less then 8 characters";
        }
        if ($this->password != $this->con_password) {
            $errors[] = "The password was not confirmed correctly";
        }
        if (!empty($errors)) {
            return false;
        } else {
            return true;
        }
    }

    function user_exists($id)
    {
        $query = 'SELECT id 
        FROM ' . $this->table . '  
        WHERE 
        id = :id';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return true;
        }
        return false;
    }

    function login_validation()
    {
        $errors = [];
        if (empty($this->phone)) {
            $error[] = "Phone is required.";
        }
        if (strlen($this->password) < 8) {
            $errors[] = "Your Password cannot be less then 8 characters";
        }
        if (!empty($errors)) {
            return false;
        } else {
            return true;
        }
    }

    function fetch_user($id)
    {
        $query = 'SELECT name, password
        FROM ' . $this->table . '  
        WHERE 
        id = :id';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return $row;
        }
        return false;
    }

    function fetch_user_by_id($id)
    {
        $query = 'SELECT u.id, 
        u.name, 
        u.phone, 
        u.email,
        u.created,
        u.password, 
        r.id as rid, 
        r.name as role from users u 
        Join roles r on u.role_id = r.id   
        WHERE 
        u.id = :id';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return $row;
        }
        return false;
    }

    function readAllUsers()
    {
        $query = 'SELECT u.id, 
        u.name, 
        u.phone, 
        u.email, 
        u.password, 
        r.name as role from users u 
        Join roles r on u.role_id = r.id 
        order by u.id DESC';

        $stmt = $this->conn->prepare($query);

        if ($stmt->execute()) {
            return $stmt;
        }
        return false;
    }

    function promote_User($id)
    {
        $query = "SELECT role_id FROM users WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        if ($this->user_exists($id)) {
            $stmt->execute();
            $result = $stmt;
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                if ($role_id == "1") {
                    $response = array(
                        "code" => 200,
                        "message" => "User cannot be promoted further",
                    );
                } else {
                    $new_role = $role_id - 1;
                    $query = "UPDATE users set role_id=:new WHERE id=:id";
                    $stmt = $this->conn->prepare($query);
                    $stmt->bindParam(':id', $this->id);
                    $stmt->bindParam(':new', $new_role);
                    if ($stmt->execute()) {
                        $response = array(
                            "code" => 200,
                            "message" => "User promoted",
                        );
                    }
                }
            }
            echo json_encode($response);
        } else {
            $response = array(
                "code" => 500,
                "message" => "User does not exist",
            );
            echo json_encode($response);
        }
    }

    function getIdFromPhone($phone)
    {
        $query = "SELECT id from users where phone =:phone";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':phone', $phone);
        if ($stmt->execute()) {
            if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $response = array(
                    "code" => 200,
                    "message" => $row['id']
                );
                echo json_encode($response);
            }
        }
    }

}
