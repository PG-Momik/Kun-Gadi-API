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

    //works
    function read_SingleContribution($id)
    {
        $query = "SELECT * FROM " . $this->table . " WHERE id = " . $id;
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

    //works
    function read_UserContribution($id)
    {
        $query = "SELECT c.created, n.name as start, m.name as end, r.path as o_path, c.path as n_path, c.state_id FROM contribute_routes c JOIN routes r on c.route_id = r.id JOIN nodes n on n.id = r.start JOIN nodes m on m.id = r.end";

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

    function read_RouteContribution($route_id)
    {
        $query = "SELECT * FROM " . $this->table . " WHERE route_id = " . $route_id;
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

    function checkState($id)
    {
        $query = "SELECT state_id FROM " . $this->table . " WHERE  id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $state = $row['state_id'];
        return $state;
    }

    function read_XContributions($page)
    {
        $limit = 10;
        $start = ($page - 1) * $limit;
        $query = "SELECT cr.id, 
        cr.user_id, 
        cr.state_id,
        r.id as route_id,
        r.start as sid,
        r.end as eid,
        r.path as o_path,
        cr.path as n_path, 
        cr.created,
        u.name as user,
        m.name as start,
        n.name as end
        FROM contribute_routes cr
        JOIN routes r on cr.route_id = r.id
        JOIN users u on cr.user_id = u.id
        JOIN nodes m on r.start = m.id
        JOIN nodes n on r.end = n.id
        ORDER BY cr.created DESC LIMIT " . $start . ", " . $limit;
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

    function acknowledgeContribution($id)
    {
        $query = "UPDATE contribute_routes SET state_id = 2 where id =:id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
    }

    function acceptContribution($id, $route_id)
    {
        $query = "UPDATE contribute_routes SET state_id = 1 where id =:id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        $query = "SELECT path from contribute_routes WHERE 
        id =:id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $sug_path = $stmt->fetch(PDO::FETCH_ASSOC);

        echo $route_id;
        $query = "UPDATE routes SET 
        path = :path 
        WHERE id = :id ";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $route_id);
        $stmt->bindParam(':path', $sug_path['path']);
        if ($stmt->execute()) {
            $response = array(
                "code" => 200,
                "message" => "Contribution Accepted."
            );
        }
        echo json_encode($response);
    }
}