<?php

class RouteContribution
{
    public $id;
    public $route_id;
    public $user_id;
    public $path;
    public $state_id;
    public $table = "contribute_routes";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    //works
    function add_contirbution($start, $end)
    {
        $this->route_id = null;
        $sql = "Select id from routes where start=:start AND end=:end LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':start', $start);
        $stmt->bindParam(':end', $end);
        if ($stmt->execute()) {
            if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $this->route_id = $row['id'];
            } else {
                $this->route_id = 1;
            }
        }

        $query = "INSERT INTO " . $this->table . " SET
        route_id = :route_id,
        user_id = :user_id,
        path = :path";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':route_id', $this->route_id);
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':path', $this->path);
        if ($stmt->execute()) {
            $response = array(
                "code" => 200,
                "message" => "Route Added."
            );
        } else {
            $response = array(
                "code" => 400,
                "message" => "Route not added."
            );
        }
        echo json_encode($response);
    }