<?php
class Coordinate{
    public $id;
    public $name;
    public $longitude; 
    public $latitude;
    private $table = 'nodes';


    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function add_Node(){
        $query = "INSERT INTO nodes  
        SET
        name = :name,
        longitude = :longitude,
        latitude = :latitude";
        $stmt = $this->conn->prepare($query);
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->longitude = htmlspecialchars(strip_tags($this->longitude));
        $this->latitude = htmlspecialchars(strip_tags($this->latitude));
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':longitude', $this->longitude);
        $stmt->bindParam(':latitude', $this->latitude);
        if ($stmt->execute()) {
            $response = array(
                "code" => 200,
                "message" => "Node Added."
            );
            echo json_encode($response);
        }else{
            $response = array(
                "code" => 400,
                "message" => "Node not added."
            );
            echo json_encode($response);
        } 
    }

    function read_SingleNodeById($id){
        if ($this->coordinate_exists($id)) {
            $result  = $this->fetch_coordinate_by_id($id);
            $coord_array = array(
                'id' => $result['id'],
                'name' => $result['name'],
                'longitude' => $result['longitude'],
                'latitude' => $result['latitude']
            );
            $response = array(
                "code" => 200,
                "message" => $coord_array
            );
            echo json_encode($response);
        }else{
            $response = array(
                "code" => 500,
                "message" => "No node with id: ".$id
            );
            echo json_encode($response);
        }
    }

    function read_SingleNodeByName($name){
        $name = htmlspecialchars(strip_tags($name));
        $query = "SELECT c.id, 
        c.name, 
        c.longitude, 
        c.longitude, 
        c.latitude 
        FROM nodes c 
        WHERE 
        c.name LIKE :name";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":name", $name);
        if($stmt->execute()){
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $coord_array = array(
                'id' => $result['id'],
                'name' => $result['name'],
                'longitude' => $result['longitude'],
                'latitude' => $result['latitude']
            );
            $response = array(
                "code" => 200,
                "message" => $coord_array
            );
            echo json_encode($response);
        }else{
            $response = array(
                "code" => 500,
                "message" => "No node with name: ".$name
            );
            echo json_encode($response);
        }
    }

    function read_AllNode(){
        $result = $this->readAllCoordinates();
        $num = $result->rowCount();
        if ($num) {
            $coord_array = array();
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                $coord_item = array(
                    'id' => $id,
                    'name' => $name,
                    'longitude' => $longitude,
                    'latitude' => $latitude
                );
                array_push($coord_array, $coord_item);
            }
            $response = array(
                "code" => 200,
                "message" => $coord_array
            );
            echo json_encode($response);
        } else {
            $response = array(
                "code" => 500,
                "message" => "No data"
            );
            echo json_encode($response);
        }
    }

    function update_Node($type = '')
    {
        if ($this->coordinate_exists($this->id)) {
                $query = 'UPDATE nodes c 
                SET 
                c.name = :name,
                c.longitude = :longitude,
                c.latitude = :latitude
                WHERE c.id = :id';

            $stmt = $this->conn->prepare($query);

            $this->id = htmlspecialchars(strip_tags($this->id));
            $stmt->bindParam(':id', $this->id);

            $this->name = htmlspecialchars(strip_tags($this->name));
            $stmt->bindParam(':name', $this->name);

            $this->longitude = htmlspecialchars(strip_tags($this->longitude));
            $stmt->bindParam(':longitude', $this->longitude);

            $this->latitude = htmlspecialchars(strip_tags($this->latitude));
            $stmt->bindParam(':latitude', $this->latitude);
            if ($stmt->execute()) {
                $response = array(
                    "code" => 200,
                    "message" => "Node updated."
                );
            }else {
                $response = array(
                    "code" => 400,
                    "message" => "Node not updated."
                );
            }
        }
        else{
            $response = array(
                "code" => 400,
                "message" => "Node does not exist."
            );
        }
        echo json_encode($response);
    }

    function delete_Node($id){
        if ($this->coordinate_exists($id)) {
            $query = 'DELETE  FROM ' . $this->table . ' WHERE id = :id';
            $stmt = $this->conn->prepare($query);
            $this->id = htmlspecialchars(strip_tags($id));
            $stmt->bindParam(':id', $this->id);
            if ($stmt->execute()) {
                $response = array(
                    "code" => 200,
                    "message" => "Node deleted."
                );
            echo json_encode($response);
            } else {
                $response = array(
                    "code" => 400,
                    "message" => "Node not deleted."
                );
            echo json_encode($response);
            }
        }

    }

    function coordinate_exists($id, $activity = null)
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
            if ($activity == "login") {
                return $row['id'];
            } else {
                return true;
            }
        }
        return false;
    }

    function fetch_coordinate_by_id($id)
    {
        $query = 'SELECT c.id, 
        c.name, 
        c.longitude, 
        c.longitude, 
        c.latitude 
        from nodes c 
        WHERE 
        c.id = :id';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return $row;
        }
        return false;
    }

    public function readAllCoordinates(){
        $query = 'SELECT * FROM '.$this->table.' ORDER BY id DESC' ;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    function read_XNode($page){
        $result = $this->readXCoordinates($page);
        $num = $result->rowCount();
        if ($num) {
            $coord_array = array();
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                $coord_item = array(
                    'id' => $id,
                    'name' => $name,
                    'longitude' => $longitude,
                    'latitude' => $latitude
                );
                array_push($coord_array, $coord_item);
            }
            $response = array(
                "code" => 200,
                "message" => $coord_array
            );
            echo json_encode($response);
        } else {
            $response = array(
                "code" => 500,
                "message" => "No data"
            );
            echo json_encode($response);
        }
    }