<?php

require_once 'Database.php';
require_once 'Node.php';

class Route extends Database
{
    public ?int $id;
    public array|string $path;
    public ?string $start;
    public ?string $end;
    public ?string $routeNo;
    public ?string $searchableNode;
    public string $table = "routes";

    public ?PDO $conn;

    public function __construct(
        $id = NULL,
        $path = NULL,
        $start = NULL,
        $end = NULL,
        $routeNo = NULL,
        $searchableNode = NULL
    ) {
        $this->conn = $this->connect();
        !is_null($id) ? $this->id = $id : NULL;
        !is_null($path) ? $this->path = $path : NULL;
        !is_null($start) ? $this->start = $start : NULL;
        !is_null($end) ? $this->end = $end : NULL;
        !is_null($routeNo) ? $this->routeNo = $routeNo : NULL;
        !is_null($searchableNode) ? $this->searchableNode = $searchableNode : NULL;
    }

    public function insert($nodes): int
    {
        $size = sizeof($nodes);
        $pathArr = array();
        $arrOfId = array();
        $endIndex = $size - 1;
        foreach ($nodes as $node) {
            if (!$node->exists()) {
                $node->insert();
            }
            $arrOfId[] = $node->getId();
            $pathArr[] = $node->name;
        }
        $path = implode(', ', $pathArr);

        $query = "INSERT INTO routes (path, start, end, route_no) VALUES (:path, :start, :end, :routeNo)";
        $params = array(
            ':path' => $path,
            ':start' => $arrOfId[0],
            ':end' => $arrOfId[$endIndex],
            ':routeNo' => $this->routeNo
        );

        $stmt = $this->conn->prepare($query);
        return $stmt->execute($params) ? 201 : 500;
    }

    public function getById($detailed = false)
    {
        $query = "SELECT 
                r.id, r.path, n.name as start, m.name as end, r.route_no as routeNo 
                FROM routes r
                join nodes n on n.id = r.start 
                join nodes m on m.id = r.end
                WHERE r.id  = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        if ($stmt->execute()) {
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$result) {
                return 404;
            }
            if ($detailed) {
                extract($result);
                $this->path = $path;
                $result['id'] = $this->id;
                $result['path'] = $this->getPathWithCoordinates();
                $result["start"] = (new Node(name: $start))->getByName()[0];
                $result["end"] = (new Node(name: $end))->getByName()[0];
            }
            return array($result);
        }
        return 500;
    }

    public function getByRouteNo($detailed = false)
    {
        $query = "SELECT 
                r.id, r.path, n.name as start, m.name as end, r.route_no as routeNo 
                FROM routes r
                join nodes n on n.id = r.start 
                join nodes m on m.id = r.end
                WHERE r.route_no  = :routeNo LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':routeNo', $this->routeNo);
        if ($stmt->execute()) {
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$result) {
                return 404;
            }
            if ($detailed) {
                extract($result);
                $this->path = $path;
                $result['path'] = $this->getPathWithCoordinates();
                $result["start"] = (new Node(name: $start))->getByName()[0];
                $result["end"] = (new Node(name: $end))->getByName()[0];
            }
            return array($result);
        }
        return 500;
    }

    public function getBySingleNode($detailed = false): int|array
    {
        $resultSet = array();
        $select = "SELECT r.id, r.path, n.name as start, m.name as end, route_no as routeNo FROM routes r";
        $join1 = " JOIN nodes n ON n.id = r.start";
        $join2 = " JOIN nodes m ON m.id = r.end";
        $where = " WHERE r.path LIKE :name";
        $query = $select . $join1 . $join2 . $where;

        unset($select, $join1, $join2, $where);

        $name = "%" . $this->searchableNode . "%";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':name', $name);
        if($stmt->execute()){
             while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {

                 if ($detailed) {
                     extract($result);
                     $this->path = $path;
                     $result['path'] = $this->getPathWithCoordinates();
                     $result['start'] = (new Node(name: $start))->getByName()[0];
                     $result['end'] = (new Node(name: $end))->getByName()[0];
                 }
                 $resultSet[] = $result;
             }
            return $resultSet;
        }
        return 500;

    }

    public function getByTwoNodes($detailed = false): bool|array
    {
        $result = $this->directSearch($this->start, $this->end);
        if (!$result) {
            $result = $this->indirectSearch($this->start, $this->end);
            if (!$result) {
                $result = $this->advanceSearch($this->start, $this->end);
                if (!$result) {
                    return false;
                }
            }
        }
        if ($detailed) {
            $resultSet = array();
            $this->path = $result['path'];
            $resultSet['path'] = $this->getPathWithCoordinates();
            $size =  sizeof($resultSet['path']);
            $resultSet['start'] = $resultSet['path'][0];
            $resultSet['end'] = $resultSet['path'][($size-1)];
            return array($resultSet);
        }

        $pathArr = explode(', ', $result['path']);
        return array(array(
            "path" => $result['path'],
            "start" => $pathArr[0],
            "end" => $pathArr[(sizeof($pathArr) - 1)]
        ));
    }

    public function update($id, $nodes, $routeNo = 0): int
    {
        $size = sizeof($nodes);
        $pathArr = array();
        $arrOfId = array();
        $endIndex = $size - 1;
        foreach ($nodes as $node) {
            if ($node->exists()) {
                $arrOfId[] = $node->getId();
                $node->update();
            } else {
                $arrOfId[] = 0;
                $node->insert();
            }
            $pathArr[] = $node->name;
        }
        $path = implode(', ', $pathArr);

        $query = "UPDATE routes SET path=:path, start=:start, end=:end, route_no=:routeNo WHERE id =:id";
        $params = array(
            ':id' => $id,
            ':path' => $path,
            ':start' => $arrOfId[0],
            ':end' => $arrOfId[$endIndex],
            ':routeNo' => $routeNo
        );
        $stmt = $this->conn->prepare($query);
        return $stmt->execute($params)?200:500;
    }

    function getPathWithCoordinates(): array
    {
        $newPath = array();
        $names = explode(", ", $this->path);
        foreach ($names as $name) {
            $newPath[] = (new Node(name: $name))->getByName()[0];
        }
        return $newPath;
    }

    function directSearch($from, $to)
    {
        $query = "SELECT r.path  FROM routes r 
        JOIN nodes n ON r.start = n.id
        JOIN nodes m ON r.end = m.id 
        WHERE n.name LIKE :frm AND m.name LIKE :too LIMIT 1";
        $from = "%" . $from . "%";
        $to = "%" . $to . "%";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':frm', $from);
        $stmt->bindParam(':too', $to);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    function indirectSearch($from, $to)
    {
        $query = "SELECT r.path FROM routes r 
        WHERE r.path LIKE :frm AND r.path LIKE :too
        LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $from = "%" . $from . "%";
        $to = "%" . $to . "%";
        $stmt->bindParam(':frm', $from);
        $stmt->bindParam(':too', $to);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    function advanceSearch($from, $to): bool|array
    {
        $arr1 = array();
        $query1 = "SELECT path 
        FROM routes 
        WHERE path LIKE :frm 
        AND path NOT LIKE :too";
        $temp_from = "%" . $from . "%";
        $temp_to = "%" . $to . "%";
        $stmt1 = $this->conn->prepare($query1);
        $stmt1->bindParam(':frm', $temp_from);
        $stmt1->bindParam(':too', $temp_to);
        $stmt1->execute();
        while ($row = $stmt1->fetch(PDO::FETCH_ASSOC)) {
            extract($row);
            $arr1[] = $path;
        }

        $arr2 = array();
        $query2 = "SELECT path 
        FROM routes 
        WHERE path LIKE :too 
        AND path NOT LIKE :frm";
        $temp_from = "%" . $from . "%";
        $temp_to = "%" . $to . "%";
        $stmt2 = $this->conn->prepare($query2);
        $stmt2->bindParam(':frm', $temp_from);
        $stmt2->bindParam(':too', $temp_to);
        if ($stmt2->execute()) {
            while ($row = $stmt2->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                $arr2[] = $path;
            }
        }

        foreach ($arr1 as $rawPath1) {
            $arrayzedPath1 = explode(", ", $rawPath1);
            $temp_p1 = $arrayzedPath1;
            $index_of_from = array_search($from, $arrayzedPath1);
            foreach ($arr2 as $rawPath2) {
                $arrayzedPath2 = explode(", ", $rawPath2);
                $temp_p2 = $arrayzedPath2;
                $index_of_to = array_search($to, $arrayzedPath2);
                if (count(array_intersect($arrayzedPath1, $arrayzedPath2)) === 0) {
                } else {
                    $common_elements = array();
                    $intersect_elements = array_intersect($arrayzedPath1, $arrayzedPath2);
                    foreach ($intersect_elements as $key => $value) {
                        $common_elements[] = $value;
                    }
                    $single_common = $common_elements[0];
                    $index_of_com_on_p1 = array_search($single_common, $temp_p1);
                    $index_of_com_on_p2 = array_search($single_common, $temp_p2);
                    if ($index_of_com_on_p1 > $index_of_from) {
                        $temp_p1 = array_slice($temp_p1, $index_of_from, ($index_of_com_on_p1 + 1));
                    } else {
                        $temp_p1 = array_slice($temp_p1, $index_of_com_on_p1, ($index_of_from - 1));
                        $temp_p1 = array_reverse($temp_p1);
                    }
                    $temp_p2 = array_slice($temp_p2, $index_of_to, ($index_of_com_on_p2 + 1));
                    if ($index_of_com_on_p2 >= $index_of_to) {
                        $temp_p2 = array_reverse($temp_p2);
                    }
                    $main_array = array_unique(array_merge($temp_p1, $temp_p2));
                    return array('path' => implode(', ', $main_array));
                }
            }
        }
        return false;
    }


}