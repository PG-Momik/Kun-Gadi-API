<?php

require_once 'Database.php';

class Node extends Database
{

    public ?string $id;
    public ?string $name;
    public ?string $lng;
    public ?string $lat;
    public string $table = "nodes";


    public ?PDO $conn;

    public function __construct($id = NULL, $name = NULL, $lat = NULL, $lng = NULL)
    {
        $this->conn = $this->connect();
        !is_null($id) ? $this->id = $id : NULL;
        !is_null($name) ? $this->name = $name : NULL;
        !is_null($lat) ? $this->lat = $lat : NULL;
        !is_null($lng) ? $this->lng = $lng : NULL;
    }

    public function insert(): int
    {
        $query = "INSERT INTO nodes (name, longitude, latitude) VALUES (:name, :lng, :lat)";
        $stmt = $this->conn->prepare($query);
        $params = array(
            ":name" => $this->name,
            ":lng" => $this->lng,
            ":lat" => $this->lat,
        );
        return $stmt->execute($params) ? 201 : 500;
    }

    public function getById()
    {
        $query = "SELECT * FROM nodes WHERE id  = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        return $stmt->execute()?$stmt->fetchAll(PDO::FETCH_ASSOC):500;
    }

    public function getByName()
    {
        $name = "%" . $this->name . "%";
        $query = "SELECT * FROM nodes WHERE name  like :name LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->execute();
        return $stmt->execute()?$stmt->fetchAll(PDO::FETCH_ASSOC):500;
    }

    public function update(): int
    {
        $query = "UPDATE nodes SET 
        name = :name,
        longitude = :lng,
        latitude = :lat
        WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $params = array(
            ":id" => $this->id,
            ":name" => $this->name,
            ":lng" => $this->lng,
            ":lat" => $this->lat,
        );
        return $stmt->execute($params) ? 200 : 500;
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
        return $count >= 1 ?true:false;
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
                $this->id = $result['id'];
                return $result['id'];
            }
            return false;
        }
    }
}