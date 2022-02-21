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