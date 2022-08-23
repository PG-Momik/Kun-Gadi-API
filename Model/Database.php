<?php

class Database
{
    private string $host = "localhost";
    private string $db_name = "kun_gadi";
    private string $username = "root";
    private string $password = "";
    private ?PDO $conn;

    public function connect(): ?PDO
    {
        $this->conn = null;
        try {
            $this->conn = new PDO(
                'mysql:host=' . $this->host .
                ';dbname=' . $this->db_name,
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo "Connection error:" . $e->getMessage();
        }
        return $this->conn;
    }

    public function getSome($limit = 15, $page = 1, $col = 'id', $asc = 'asc'): bool|int|array
    {
        $query = "SELECT count(*) as count from $this->table;";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $count = array("count" => $result['count']);
        $offset = $limit * $page - $limit;

        switch ($this->table) {
            case "users":
                $query = "SELECT 
                            u.id, 
                            u.name, 
                            u.phone, 
                            u.email, 
                            u.password, 
                            u.role_id as rid, 
                            r.name as role 
                            FROM users u 
                            JOIN roles r on u.role_id =  r.id
                            ORDER BY :col :asc LIMIT $limit OFFSET $offset";
                break;
            case "nodes":
                $query = "SELECT * FROM $this->table ORDER BY :col :asc LIMIT $limit OFFSET $offset";
                break;
            case "routes":
                $query = "SELECT r . id, r . path,r.route_no as routeNo, r . start as sid, r . end as eid, n . name as start, m . name as end
                        FROM routes r
                        JOIN nodes n ON r . start = n . id
                        JOIN nodes m ON r . end = m . id
                        ORDER BY :col :asc LIMIT $limit OFFSET $offset";
                break;
            case "contribute_nodes":
                $query = "SELECT cn.id, 
                        n.name as node, 
                        u.name as user,
                        cn.longitude as newLng, 
                        cn.latitude as newLat,
                        n.longitude as oldLng,
                        n.latitude as oldLat,
                        cn.errorLat, 
                        cn.errorLng, 
                        s.name as state,
                        cn.coordinate_id as nid, 
                        cn.user_id as uid,
                        cn.state_id as sid, 
                        cn.created
                        FROM contribute_nodes cn
                        JOIN nodes n on cn.coordinate_id = n.id
                        JOIN users u on cn.user_id = u.id
                        JOIN states s on cn.state_id = s.id
                        ORDER BY :col :asc LIMIT $limit OFFSET $offset";
                break;
            case "contribute_routes":
                $query="SELECT cr.id,
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
                        ORDER BY :col :asc LIMIT $limit OFFSET $offset";
                break;
        }

        $params = array(
            ':col' => $col,
            ':asc' => $asc,
        );
        $stmt = $this->conn->prepare($query);
        if ($stmt->execute($params)) {
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $result[] = $count;
            return $result;
        }
        return 500;
    }

    public function getAll(): int|bool|array
    {
        $query = "SELECT * FROM $this->table";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute() ? $stmt->fetchAll(PDO::FETCH_ASSOC) : 500;
    }

    public function delete(): int
    {
        $query = "DELETE FROM $this->table WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        return $stmt->execute() ? 202 : 500;
    }
}