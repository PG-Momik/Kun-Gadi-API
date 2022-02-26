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

    function acceptContribution($id, $coordinate_id)
    {
        echo "eta";
        $query = "UPDATE contribute_nodes SET state_id = 1 where id =:id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        $query = "SELECT 
            cn.longitude as cnlng, 
            cn.latitude as cnlat, 
            n.longitude as nlat, 
            n.latitude as nlng,
            n.name
            FROM contribute_nodes cn
            JOIN nodes n on n.id = cn.coordinate_id
            WHERE 
            cn.id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $coords = $stmt->fetch(PDO::FETCH_ASSOC);

        $new_lon = ((float)$coords['cnlng'] + (float)$coords['nlng']) / 2;
        $new_lat = ((float)$coords['cnlat'] + (float)$coords['nlat']) / 2;

        $query = "UPDATE nodes SET longitude = :longitude, latitude = :latitude WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $coordinate_id);
        $stmt->bindParam(':longitude', $new_lat);
        $stmt->bindParam(':latitude', $new_lon);
        if ($stmt->execute()) {
            $response = array(
                "code" => 200,
                "message" => "Contribution Accepted."
            );
        } else {
            $response = array(
                "code" => 500,
                "message" => "Something went wrong."
            );
        }
        echo json_encode($response);
    }

    function acknowledgeContribution($id)
    {
        $query = "UPDATE contribute_nodes SET state_id = 2 where id =:id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
    }

    function delete_Contribution($id)
    {
        $query = 'DELETE  FROM ' . $this->table . ' WHERE id = :id';
        $stmt = $this->conn->prepare($query);
        $this->id = htmlspecialchars(strip_tags($id));
        $stmt->bindParam(':id', $this->id);
        if ($stmt->execute()) {
            $response = array(
                "code" => 200,
                "message" => "Contribution deleted."
            );
        } else {
            $response = array(
                "code" => 400,
                "message" => "Contribution not deleted."
            );
        }
        echo json_encode($response);
    }

    function read_XContributions($page)
    {
        $limit = 10;
        $start = ($page - 1) * $limit;
        $query = "SELECT cn.id, 
            n.name as node, 
            u.name as user,
            cn.longitude, 
            cn.latitude, 
            s.name as state,
            cn.coordinate_id as nid, 
            cn.user_id as uid,
            cn.state_id as sid, 
            cn.created
            FROM contribute_nodes cn
            JOIN nodes n on cn.coordinate_id = n.id
            JOIN users u on cn.user_id = u.id
            JOIN states s on cn.state_id = s.id
            ORDER BY cn.created DESC LIMIT " . $start . ", " . $limit;
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