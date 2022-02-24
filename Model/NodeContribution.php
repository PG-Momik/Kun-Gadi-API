<?php

class NodeContribution
{
    public $id;
    public $coordinate_id;
    public $user_id;
    public $longitude;
    public $latitude;
    public $state_id;
    public $table = "contribute_nodes";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    function add_contirbution()
    {
        $query = "INSERT INTO " . $this->table . " SET
        coordinate_id = :coordinate_id,
        user_id = :user_id,
        longitude = :longitude,
        latitude = :latitude";
        $stmt = $this->conn->prepare($query);
        $this->coordinate_id = htmlspecialchars(strip_tags($this->coordinate_id));
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        $this->longitude = htmlspecialchars(strip_tags($this->longitude));
        $this->latitude = htmlspecialchars(strip_tags($this->latitude));
        $stmt->bindParam(':coordinate_id', $this->coordinate_id);
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':longitude', $this->longitude);
        $stmt->bindParam(':latitude', $this->latitude);
        if ($stmt->execute()) {
            $response = array(
                "code" => 200,
                "message" => "Node Added."
            );
            echo json_encode($response);
        } else {
            $response = array(
                "code" => 400,
                "message" => "Node not added."
            );
            echo json_encode($response);
        }
    }

    function read_AllContributions()
    {
        $query = "SELECT * FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $contributions_array = array();
        if ($stmt->execute()) {
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                array_push($contributions_array, $row);
            }
            $response = array(
                "code" => 200,
                "message" => $contributions_array
            );
        } else {
            $response = array(
                "code" => 400,
                "message" => "No data."
            );
        }
        echo json_encode($response);
    }

    function read_SingleContribution($id, $admin)
    {
        $query = "SELECT  cr.id, 
        cr.coordinate_id, 
        cr.user_id,
        cr.longitude as n_lng, 
        cr.latitude as n_lat,
        cr.state_id, 
        cr.created,
        c.name as name,
        c.longitude as o_lat,
        c.latitude as o_lng
        FROM contribute_nodes cr 
        JOIN nodes c on cr.coordinate_id = c.id
        WHERE cr.id =" . $id;
        $stmt = $this->conn->prepare($query);
        $contributions_array = array();
        if ($stmt->execute()) {
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                array_push($contributions_array, $row);
            }
            $response = array(
                "code" => 200,
                "message" => $contributions_array
            );
            if ($admin && $response["message"][0]['state_id'] == 3) {
                $this->acknowledgeContribution($id);
            }
        } else {
            $response = array(
                "code" => 400,
                "message" => "No data."
            );
        }
        echo json_encode($response);
    }
    function read_UserContribution($user_id)
    {
        $query = "SELECT c.id, n.name as name, c.longitude as n_lat, c.latitude as n_lng, c.created, c.state_id, n.latitude as o_lat, n.longitude as o_lng FROM contribute_nodes c JOIN nodes n on c.coordinate_id = n.id";
        $stmt = $this->conn->prepare($query);
        $contributions_array = array();
        if ($stmt->execute()) {
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                array_push($contributions_array, $row);
            }
            $response = array(
                "code" => 200,
                "message" => $contributions_array
            );
        } else {
            $response = array(
                "code" => 400,
                "message" => "No data."
            );
        }
        echo json_encode($response);
    }
