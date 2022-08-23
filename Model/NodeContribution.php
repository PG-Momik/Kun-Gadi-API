<?php

require_once 'Node.php';
require_once 'User.php';

class NodeContribution extends Database
{
    public ?int $id;
    public ?int $cid;
    public ?int $uid;
    public ?string $lng;
    public ?string $lat;
    public ?int $sid;
    public ?string $node;
    public ?string $user;
    public ?float $errorLng;
    public ?float $errorLat;

    public string $table = "contribute_nodes";


    public ?PDO $conn;

    public function __construct($id = null, $cid = null, $uid = null, $lat = null, $lng = null, $sid = null, $node = null, $user = null)
    {
        $this->conn = $this->connect();
        !is_null($id) ? $this->id = $id:null;
        !is_null($cid) ? $this->cid = $cid:null;
        !is_null($uid) ? $this->uid = $uid:null;
        !is_null($lat) ? $this->lat = $lat:null;
        !is_null($lng) ? $this->lng = $lng:null;
        !is_null($sid) ? $this->sid = $sid:null;
        !is_null($node) ? $this->node = $node:null;
        !is_null($user) ? $this->user = $user:null;
    }

    public function insert():int
    {
        $query = "INSERT INTO $this->table (coordinate_id, user_id, longitude, latitude) VALUES (:cid, :uid,  :lng, :lat)";
        $stmt = $this->conn->prepare($query);
        $params = array(
            ":cid" => $this->cid,
            ":uid" => $this->uid,
            ":lng" => $this->lng,
            ":lat" => $this->lat,
        );
        return $stmt->execute($params)?201:500;
    }

    public function getById(): bool|array
    {
        $query = "SELECT cn.id, 
                    cn.coordinate_id AS cid, 
                    cn.user_id AS uid, 
                    cn.longitude AS newLng, 
                    cn.latitude AS newLat, 
                    n.longitude AS oldLng, 
                    n.latitude AS oldLat, 
                    cn.errorLat, 
                    cn.errorLng,
                    cn.state_id AS sid, 
                    cn.created AS created, 
                    n.name AS node, 
                    u.name AS user 
                    FROM $this->table cn 
                    JOIN nodes n ON cn.coordinate_id = n.id 
                    JOIN users u ON cn.user_id = u.id 
                    WHERE cn.id  = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        return $stmt->execute()?$stmt->fetchAll(PDO::FETCH_ASSOC):500;
    }

    public function getByCid(): bool|array
    {
        $query = "SELECT cn.id, 
                    cn.coordinate_id AS cid, 
                    cn.user_id AS uid, 
                    cn.longitude AS newLng, 
                    cn.latitude AS newLat, 
                    cn.state_id AS sid, 
                    cn.created AS created, 
                    n.name AS node, 
                    u.name AS user 
                    FROM $this->table cn 
                    JOIN nodes n ON cn.coordinate_id = n.id 
                    JOIN users u ON cn.user_id = u.id 
                    WHERE cn.coordinate_id  = :cid";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':cid', $this->cid);
        return $stmt->execute()?$stmt->fetchAll(PDO::FETCH_ASSOC):500;
    }

    public function getByUid(): bool|array
    {
        $query = "SELECT cn.id, 
                    cn.coordinate_id AS cid, 
                    cn.user_id AS uid, 
                    cn.longitude AS newLng, 
                    cn.latitude AS newLat, 
                    cn.state_id AS sid, 
                    cn.created AS created, 
                    n.name AS node, 
                    u.name AS user 
                    FROM $this->table cn 
                    JOIN nodes n ON cn.coordinate_id = n.id 
                    JOIN users u ON cn.user_id = u.id 
                    WHERE cn.user_id  = :uid";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':uid', $this->uid);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result ?? false;
    }

    public function getBySid(): bool|array
    {
        $query = "SELECT cn.id, 
                    cn.coordinate_id AS cid, 
                    cn.user_id AS uid, 
                    cn.longitude AS newLng, 
                    cn.latitude AS newLat, 
                    cn.state_id AS sid, 
                    cn.created AS created, 
                    n.name AS node, 
                    u.name AS user 
                    FROM $this->table cn 
                    JOIN nodes n ON cn.coordinate_id = n.id
                    JOIN users u ON cn.user_id = u.id
                    WHERE cn.state_id  = :sid";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':sid', $this->sid);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result ?? false;
    }

    public function getByName(): bool|array
    {
        $this->cid = $this->getCid();
        return $this->getByCid();
    }

    public function getByUser(): bool|array
    {
        $this->uid = $this->getUid();
        return $this->getByUid();
    }

    public function review(): int
    {
        $query = "SELECT 
            cn.longitude as newLng, 
            cn.latitude as newLat,
            n.longitude as oldLng, 
            n.latitude as oldLat,
            n.name
            FROM contribute_nodes cn
            JOIN nodes n on n.id = cn.coordinate_id
            WHERE 
            cn.id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();
        $coords = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->errorLat = $this->getError($coords['newLat'], $coords['oldLat']);
        $this->errorLng = $this->getError($coords['newLng'], $coords['oldLng']);
        $this->errorLat = substr($this->errorLat, 0, 7);
        $this->errorLng = substr($this->errorLng, 0, 7);
        $query = "UPDATE $this->table SET state_id = 2, errorLat =:errLat, errorLng  =:errLng WHERE id=:id";
        $stmt = $this->conn->prepare($query);
        $params = array(
            ":id" => $this->id,
            ":errLat" => $this->errorLat,
            "errLng" => $this->errorLng
        );
        return $stmt->execute($params)?200:500;
    }

    public function approve(): int
    {
        $query = "SELECT 
            cn.longitude as newLng, 
            cn.latitude as newLat,
            cn.state_id as sid,
            n.id as cid,
            n.longitude as oldLng, 
            n.latitude as oldLat,
            n.name
            FROM contribute_nodes cn
            JOIN nodes n on n.id = cn.coordinate_id
            WHERE 
            cn.id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();
        $coords = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->cid = (int)$coords['cid'];
        $this->sid =  (int)$coords['sid'];

        if($this->sid == 1){
            return 200;
        }
        $newLng = ((float)$coords['newLng'] + (float)$coords['oldLng']) / 2;
        $newLat = ((float)$coords['newLat'] + (float)$coords['oldLat']) / 2;

        $query = "UPDATE nodes SET longitude=:lng, latitude=:lat WHERE id=:id";
        $stmt = $this->conn->prepare($query);
        $params = array(
            ":id" => $this->cid,
            ":lng" => $newLng,
            ":lat" => $newLat
        );
        if ($stmt->execute($params)) {
            $query = "UPDATE $this->table SET state_id = 1 WHERE id=:id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":id", $this->id);
            return $stmt->execute()?200:500;
        }
        return 500;
    }

    public function getCid()
    {
        if (isset($this->cid)) {
            return $this->cid;
        }
        $node = new Node(name: $this->node);
        return $node->getId();
    }

    public function getUid()
    {
        if (isset($this->uid)) {
            return $this->uid;
        }
        $user = new User(name: $this->user);
        return $user->getId();
    }

    public function exists(): bool
    {
        $stmt = '';

        if (isset($this->name)) {
            $name = "%" . $this->name . "%";
            $query = "SELECT COUNT(*) FROM nodes WHERE name LIKE :name";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':name', $name);
        }

        if (isset($this->id)) {
            $query = "SELECT COUNT(*) FROM nodes WHERE id  = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $this->id);
        }

        $stmt->execute();
        $count = $stmt->fetchColumn();
        return $count >= 1 ?? false;
    }

    public function getId()
    {
        if (isset($this->id)) {
            return $this->id;
        }
        $query = "SELECT id FROM nodes WHERE name LIKE :name";
        $stmt = $this->conn->prepare($query);
        $name = "%" . $this->name . "%";
        $stmt->bindParam(":name", $name);
        if ($stmt->execute()) {
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result) {
                return $result['id'];
            }
            return false;
        } return false;
    }

    public function getError(float $newX, float $oldX): float
    {
        return (string)abs(($newX - $oldX) / $newX);
    }


}