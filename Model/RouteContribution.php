<?php

require_once 'Node.php';
require_once 'User.php';

class RouteContribution extends Database
{
    public ?int $id;
    public ?int $rid;
    public ?int $uid;
    public array|string $path;
    public ?int $sid;
    public string $table = "contribute_routes";

    public ?PDO $conn;

    public function __construct($id = NULL, $rid = NULL, $uid = NULL, $path = NULL, $sid = NULL)
    {
        $this->conn = $this->connect();
        !is_null($id) ? $this->id = $id : NULL;
        !is_null($rid) ? $this->rid = $rid : NULL;
        !is_null($uid) ? $this->uid = $uid : NULL;
        !is_null($path) ? $this->path = $path : NULL;
        !is_null($sid) ? $this->sid = $sid : NULL;
    }

    public function insert(): int
    {
        $query = "INSERT into $this->table  (route_id, user_id, path) values (:rid, :uid, :path)";
        $params = array(
            ":rid" => $this->rid,
            ":uid" => $this->uid,
            ":path" => $this->path
        );
        $stmt = $this->conn->prepare($query);
        return $stmt->execute($params) ? 201 : 500;
    }

    public function getById($detailed = false): int|array
    {
        $query = "SELECT 
                cr.id, 
                cr.path as crPath, 
                cr.route_id as rid, 
                cr.state_id as sid, 
                r.route_no as routeNo,
                r.path as nPath
                FROM $this->table cr
                JOIN routes r ON cr.route_id = r.id
                WHERE cr.id  = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        if ($stmt->execute()) {
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$result) {
                return 404;
            }
            if ($detailed) {
                extract($result);
                $this->path = $crPath;
                $result['id'] = $this->id;
                $result['crPath'] = $this->getPathWithCoordinates();
                $this->path = $nPath;
                $result['nPath'] = $this->getPathWithCoordinates();
            }
            return array($result);
        }
        return 500;
    }

    public function getByRid($detailed = false): int|array
    {
        $resultSet = array();
        $query = "SELECT 
                cr.id, cr.path, cr.route_id as rid 
                FROM $this->table cr
                WHERE cr.route_id  = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->rid);
        if ($stmt->execute()) {
            while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
                if ($detailed) {
                    $this->path = $result['path'];
                    $this->id = $result['id'];
                    $result['id'] = $this->id;
                    $result['path'] = $this->getPathWithCoordinates();
                }
                $resultSet[] = $result;
            }
            return $resultSet;
        }
        return 500;
    }

    public function getByUid($detailed = false): int|array
    {
        $resultSet =array();
        $query = "SELECT 
                cr.id, cr.path, cr.route_id as rid 
                FROM $this->table cr
                WHERE cr.user_id  = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->uid);
        if ($stmt->execute()) {
            while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
                if ($detailed) {
                    $this->path = $result['path'];
                    $this->id = $result['id'];
                    $result['id'] = $this->id;
                    $result['path'] = $this->getPathWithCoordinates();
                }
                $resultSet[] = $result;
            }
            return $resultSet;
        }
        return 500;
    }

    public function approve(): int
    {
        $result = $this->getById()[0];
        $path = $result['crPath'];
        $rid = $result['rid'];
        echo $rid;
        $query = "UPDATE routes SET path = :path where id = :rid";
        $stmt =  $this->conn->prepare($query);
        $param = array(
            "path"=>$path,
            "rid"=>$rid
        );
        $stmt->execute($param);
        $query = "UPDATE contribute_routes SET state_id = 1 where id = $this->id";
        $stmt =  $this->conn->prepare($query);
        return $stmt->execute()?200:500;
    }

    public function  review():int{
        $query = "UPDATE contribute_routes SET state_id = 2 where id = $this->id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute()?200:500;
    }

    function getPathWithCoordinates(): array
    {
        $newPath = array();
        $names = explode(", ", $this->path);
        foreach ($names as $name) {
            $node =  (new Node(name: $name))->getByName();
            $newPath[] = $node[0]??false;
        }
        return $newPath;
    }
}