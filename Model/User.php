<?php
require_once 'Database.php';
class User extends Database
{
    public string $table = 'users';
    public ?int $id;
    public ?string $name;
    public ?string $phone;
    public ?string $email;
    public ?string $password;
    public ?string $conPassword;
    public ?string $created;
    public ?string $rid;
    public ?string $role;
    public ?PDO $conn;

    public function __construct($id = null, $name = null, $phone = null, $email = null, $password = null, $conPassword =  null, $rid= null)
    {
        $this->conn = $this->connect();
        !is_null($id) ? $this->id = $id:null;
        !is_null($name) ? $this->name = $name:null;
        !is_null($phone) ? $this->phone = $phone:null;
        !is_null($email) ? $this->email = $email:null;
        !is_null($password) ? $this->password = $password:null;
        !is_null($conPassword) ? $this->conPassword = $conPassword:null;
        !is_null($rid) ? $this->rid = $rid:null;
    }

    public function insert($type='admin'): int
    {
        $this->password = password_hash($this->password, PASSWORD_DEFAULT);
        $query = "INSERT INTO $this->table (name, phone, email, password) VALUES (:name, :phone, :email, :password)";
        $params = array(
            ":name"=>$this->name,
            ":phone"=>$this->phone,
            ":email"=>$this->email,
            ":password"=>$this->password
        );
        $stmt =  $this->conn->prepare($query);
        return $stmt->execute($params)?201:500;
    }

    public function getById(): bool|int|array
    {
        $query = "SELECT * FROM $this->table WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id",$this->id);
        return $stmt->execute()? $stmt->fetch(PDO::FETCH_ASSOC):500;
    }

    public function getByPhone()
    {
        $query = "SELECT * FROM $this->table WHERE phone = :phone LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":phone",$this->phone);
        return $stmt->execute()?$stmt->fetch(PDO::FETCH_ASSOC):500;
    }

    public function update(): int
    {
        $query =  "UPDATE $this->table SET name = :name, phone = :phone, email = :email, role_id = :rid WHERE id=:id";
        $params = array(
            ":id" => $this->id,
            ":name" => $this->name,
            ":phone" => $this->phone,
            ":email" => $this->email,
            ":rid" => $this->rid,
        );
        $stmt = $this->conn->prepare($query);
        return $stmt->execute($params)?200: 500;
    }

    public function login(): int|array
    {
        $result = $this->getByPhone();
        if($result){
            if (password_verify($this->password, $result['password'])){
                return $result;
            }
            return 404;
        }return 404;
    }

    public function  getId(){
        if(isset($this->id)){
            return $this->id;
        }
        $query = "SELECT id FROM users WHERE name LIKE :name order by id asc LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $name = "%" . $this->name . "%";
        $stmt->bindParam(":name", $name);
        if ($stmt->execute()) {
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result) {
                return $result['id'];
            }
            return 400;
        }
        return 500;
    }

    function getDashboardData()
    {
        $message = array();

        // users
        $query = "SELECT count(*) as nUM from users where role_id = 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        extract($row);
        $message['masters'] = $nUM;

        $query = "SELECT count(*) as nUA from users where role_id = 2";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        extract($row);
        $message['admins'] = $nUA;

        $query = "SELECT count(*) as nUU from users where role_id =3";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        extract($row);
        $message['users'] = $nUU;
        // nodes
        $query = "SELECT count(*) as nN from nodes";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        extract($row);
        $message['nodes'] = $nN;

        // routes
        $query = "SELECT count(*) as nR from routes";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        extract($row);
        $message['routes'] = $nR;


        // contNApproved
        $query = "SELECT count(*) as nCNA from contribute_nodes WHERE state_id = 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        extract($row);
        $message['contNApprove'] = $nCNA;

        // contNReviewed
        $query = "SELECT count(*) as nCNR from contribute_nodes where state_id = 2";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        extract($row);
        $message['contNReview'] = $nCNR;

        // contNUnreviewed
        $query = "SELECT count(*) as nCN from contribute_nodes where state_id = 3";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        extract($row);
        $message['contNUnreview'] = $nCN;


        // contRApproved
        $query = "SELECT count(*) as nCRA from contribute_routes where state_id = 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        extract($row);
        $message['contRApprove'] = $nCRA;

        // contRReviewed
        $query = "SELECT count(*) as nCRR from contribute_routes where state_id = 2";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        extract($row);
        $message['contRReview'] = $nCRR;

        // contRTotal
        $query = "SELECT count(*) as nCR from contribute_routes where state_id = 3";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        extract($row);
        $message['contRUnreview'] = $nCR;

        return $message;
    }

    public function promote(){

        $query = "Update $this->table set role_id =  (role_id - 1) WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);
        return $stmt->execute()?200:500;

    }
}