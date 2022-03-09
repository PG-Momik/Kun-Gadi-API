<?php
class Route
{
    public $id;
    public $path;
    public $start;
    public $node;
    public $end;
    public $table = 'routes';

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function add_Route()
    {
        $query = "INSERT INTO routes
        SET
        path = :path,
        start = :start,
        end = :end,
        route_no = :route_no";
        $stmt = $this->conn->prepare($query);
        $this->path = htmlspecialchars(strip_tags($this->path));
        $this->start = htmlspecialchars(strip_tags($this->start));
        $this->end = htmlspecialchars(strip_tags($this->end));
        $this->route_no = htmlspecialchars(strip_tags($this->route_no));
        $stmt->bindParam(':path', $this->path);
        $stmt->bindParam(':start', $this->start);
        $stmt->bindParam(':end', $this->end);
        $stmt->bindParam(':route_no', $this->route_no);

        if ($stmt->execute()) {
            $response = array(
                "code" => 200,
                "message" => "Route Added.",
            );
            echo json_encode($response);
        } else {
            $response = array(
                "code" => 400,
                "message" => "Route not added.",
            );
            echo json_encode($response);
        }
    }

    public function read_RouteAll()
    {
        $query = 'SELECT * FROM routes';
        $stmt = $this->conn->prepare($query);
        if ($stmt->execute()) {
            $route_array = array();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                $route_item = array(
                    'id' => $id,
                    'path' => $path,
                    'start' => $start,
                    'end' => $end,
                    'route_no' => $route_no,
                );
                array_push($route_array, $route_item);
            }
            $response = array(
                "code" => 200,
                "message" => $route_array,
            );
            echo json_encode($response);
        } else {
            $response = array(
                "code" => 500,
                "message" => "No data",
            );
            echo json_encode($response);
        }
    }

    public function read_RouteById($id = null)
    {
        $query = "SELECT r.id, r.path, r.route_no, n1.name as start, n2.name as end
        FROM routes r
        JOIN nodes n1 on r.start = n1.id
        JOIN nodes n2 on r.end = n2.id
        WHERE r.id = :id";
        $stmt = $this->conn->prepare($query);
        $this->id = htmlspecialchars(strip_tags($id));
        $stmt->bindParam(':id', $id);
        if ($stmt->execute()) {
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $route_array = array(
                'id' => $result['id'],
                'path' => $result['path'],
                'start' => $result['start'],
                'end' => $result['end'],
                'route_no' => $result['route_no'],
            );
            $response = array(
                "code" => 200,
                "message" => $route_array,
            );
        } else {
            $response = array(
                "code" => 500,
                "message" => "No data found.",
            );
        }
        echo json_encode($response);
    }

    public function read_RouteByNo($num)
    {
        $query = "SELECT r.id, r.path, r.route_no,
        n1.name as start,
        n2.name as end
        FROM routes r
        JOIN nodes n1 on r.start = n1.id
        JOIN nodes n2 on r.end = n2.id
        WHERE route_no =  :num";
        $stmt = $this->conn->prepare($query);
        $num = htmlspecialchars(strip_tags($num));
        $stmt->bindParam(':num', $num);
        $stmt->execute();
        $result = $stmt;
        $num = $result->rowCount();
        if ($num) {
            $route_array = array();
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                $route_item = array(
                    'id' => $id,
                    'route_no' => $route_no,
                    'path' => $path,
                    'start' => $start,
                    'end' => $end,
                );
                array_push($route_array, $route_item);
            }
            $response = array(
                "code" => 200,
                "message" => $route_array,
            );
        } else {
            $response = array(
                "code" => 500,
                "message" => "No data",
            );
        }
        echo json_encode($response);
    }

    public function read_RouteByStart($start)
    {
        $query = 'SELECT r.id, r.path, r.route_no,
        n1.name as start,
        n2.name as end
        FROM routes r
        JOIN nodes n1 on r.start = n1.id
        JOIN nodes n2 on r.end = n2.id
        WHERE n1.name LIKE :start';
        $stmt = $this->conn->prepare($query);
        $start = htmlspecialchars(strip_tags($start));
        $stmt->bindParam(':start', $start);
        $stmt->execute();
        $result = $stmt;
        $num = $result->rowCount();
        if ($num) {
            $route_array = array();
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                $route_item = array(
                    'id' => $id,
                    'route_no' => $route_no,
                    'path' => $path,
                    'start' => $start,
                    'end' => $end,
                );
                array_push($route_array, $route_item);
            }
            $response = array(
                "code" => 200,
                "message" => $route_array,
            );
        } else {
            $response = array(
                "code" => 400,
                "message" => "No data found.",
            );
        }
        echo json_encode($response);
    }

    public function read_RouteByEnd($end)
    {
        $query = 'SELECT r.id, r.path, r.route_no,
        n1.name as start,
        n2.name as end
        FROM routes r
        JOIN nodes n1 on r.start = n1.id
        JOIN nodes n2 on r.end = n2.id
        WHERE n2.name LIKE :end';
        $stmt = $this->conn->prepare($query);
        $end = htmlspecialchars(strip_tags($end));
        $stmt->bindParam(':end', $end);
        $stmt->execute();
        $result = $stmt;
        $num = $result->rowCount();
        if ($num) {
            $route_array = array();
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                $route_item = array(
                    'id' => $id,
                    'route_no' => $route_no,
                    'path' => $path,
                    'start' => $start,
                    'end' => $end,
                );
                array_push($route_array, $route_item);
            }
            $response = array(
                "code" => 200,
                "message" => $route_array,
            );
        } else {
            $response = array(
                "code" => 400,
                "message" => "No data found.",
            );
        }
        echo json_encode($response);
    }

    public function read_routeToNode($node)
    {
        //get rows where node is mentioned
        $query = "SELECT r.id, r.path, r.route_no
        FROM routes r
        JOIN nodes n1 on r.start = n1.id
        JOIN nodes n2 on r.end = n2.id
        WHERE r.path LIKE :node";
        $stmt = $this->conn->prepare($query);
        $node = htmlspecialchars(strip_tags($node));
        $node = "%" . $node . "%";
        $stmt->bindParam(":node", $node);
        $stmt->execute();
        $result = $stmt;
        $num_of_related_path = $result->rowCount();
        $route_numbers = array();
        $related_paths = array();
        $all_related_nodes = array();
        $cheet_sheet = array();

        if ($num_of_related_path) {
            $info_array['complete_paths'] = array();
            $info_array['divided_paths'] = array();
            $i = 0;
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                $info_array['complete_paths'][$i] = array();
                $info_array['divided_paths'][$i] = array();
                array_push($info_array['complete_paths'][$i], $path);
                array_push($related_paths, $path);
                array_push($route_numbers, (string)$route_no);
                $info_array['complete_paths'][$i] = $this->path_string_to_array($info_array['complete_paths'][$i]);
                $i++;
            }

            // basically explodes paths, 
            // then unionizes exploded nodes and then returns them
            $all_related_nodes = $this->path_string_to_array($related_paths);
            //uses returned node mentioned above to make a select query
            $fullquery = $this->generate_query($all_related_nodes);
            $stmt2 = $this->conn->prepare($fullquery);
            $stmt2->execute();
            $result2 = $stmt2;
            $num_of_related_nodes = $result2->rowCount();
            for ($i = 0; $i < $num_of_related_nodes; $i++) {
                $row2 = $result2->fetch(PDO::FETCH_ASSOC);
                array_push($cheet_sheet, $row2);
            }
            $i = 0;
            for ($i = 0; $i < sizeof($info_array['complete_paths']); $i++) {
                for ($j = 0; $j < sizeof($info_array['complete_paths'][$i]); $j++) {
                    $one_place = $info_array['complete_paths'][$i][$j];
                    $index = array_search($one_place, array_column($cheet_sheet, 'name'));
                    $info_array['complete_paths'][$i][$j] = $cheet_sheet[$index];
                }
            }

            //making array unique
            $array1_keys = array_keys($route_numbers);
            $unique = array_keys(array_unique($route_numbers));
            $duplicate = array_diff($array1_keys, $unique);
            foreach ($duplicate as $index) {
                $route_numbers[$index] =  $route_numbers[$index] . "repeated" . $index;
            }
            $response = array();
            //making key response a key value pair, where key is route_no.
            for ($i = 0; $i < sizeof($info_array['complete_paths']); $i++) {
                $response[$route_numbers[$i]] = $info_array['complete_paths'][$i];
            }
            echo json_encode(
                array(
                    "code" => 200,
                    "message" => $response
                )
            );
        }
    }

    public function update_route($id)
    {
        if ($this->route_exists($id)) {
            $query = 'UPDATE routes 
            SET 
            path = :path,
            start = :start,
            end = :end,
            route_no = :route_no
            WHERE id = :id';

            $stmt = $this->conn->prepare($query);

            $this->id = htmlspecialchars(strip_tags($this->id));
            $stmt->bindParam(':id', $this->id);

            $this->path = htmlspecialchars(strip_tags($this->path));
            $stmt->bindParam(':path', $this->path);

            $this->start = htmlspecialchars(strip_tags($this->start));
            $stmt->bindParam(':start', $this->start);

            $this->end = htmlspecialchars(strip_tags($this->end));
            $stmt->bindParam(':end', $this->end);

            $this->route_no = htmlspecialchars(strip_tags($this->route_no));
            $stmt->bindParam(':route_no', $this->route_no);

            if ($stmt->execute()) {
                $response = array(
                    "code" => 200,
                    "message" => "Route updated."
                );
            } else {
                $response = array(
                    "code" => 400,
                    "message" => "Route not updated."
                );
            }
        } else {
            $response = array(
                "code" => 400,
                "message" => "Route does not exist."
            );
        }
        echo json_encode($response);
    }

    public function delete_Route($id)
    {
        if ($this->route_exists($id)) {
            $query = 'DELETE  FROM ' . $this->table . ' WHERE id = :id';
            $stmt = $this->conn->prepare($query);
            $this->id = htmlspecialchars(strip_tags($id));
            $stmt->bindParam(':id', $id);
            if ($stmt->execute()) {
                $response = array(
                    "code" => 200,
                    "message" => "Route deleted."
                );
            } else {
                $response = array(
                    "code" => 400,
                    "message" => "Route not deleted."
                );
            }
        } else {
            $response = array(
                "code" => 500,
                "message" => "Route does not exist."
            );
        }
        echo json_encode($response);
    }

    public function readAllRoutes()
    {
        $query = 'SELECT * FROM ' . $this->table . ' ORDER BY id DESC';
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function route_exists($id)
    {
        $query = 'SELECT id
        FROM ' . $this->table . '
        WHERE
        id = :id';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return true;
        } else {
            return false;
        }
    }

    public function fetch_route_by_id($id)
    {
        $query = 'SELECT path, start , end
        from routes
        WHERE
        id = :id';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return $row;
        }
        return false;
    }

    public function generate_query($nodes)
    {
        $sql_p1 = "SELECT id, name, longitude, latitude FROM nodes where ";
        $sql_p2 = null;
        foreach ($nodes as $node) {
            $sql_p2 = $sql_p2 . "name LIKE '" . $node . "' || ";
        }

        $sql_p2 = substr($sql_p2, 0, -3);
        return $sql_p1 . $sql_p2;
    }

    public function generate_divided_path($path, $node)
    {
        $node = str_replace("%", "", $node);
        $i = 0;
        $divided_path = array();
        for ($i = 0; $i < sizeof($path); $i++) {
            if ($path[$i]['name'] == $node) {
                echo $i . "<br>";
            }
        }
    }

    public function path_string_to_array($paths)
    {
        $combination = null;
        foreach ($paths as $path) {
            $combination = $path . " ," . $combination;
        }
        $combination = substr($combination, 0, -2);
        $nodes = explode(",", $combination);
        $nodes = array_values(array_unique($nodes));
        for ($i = 0; $i < sizeof($nodes); $i++) {
            $nodes[$i] = str_replace(" ", "", $nodes[$i]);
        }
        return $nodes;
    }

    public function read_nodesByPath($path)
    {
        $data = array();
        $nodes = explode(", ", $path);
        $nodes = array_values(array_unique($nodes));
        $sql_p1 = "SELECT name, longitude, latitude FROM nodes where ";
        $sql_p2 = null;
        foreach ($nodes as $node) {
            $sql_p2 = $sql_p2 . "name LIKE '" . $node . "' || ";
        }
        $sql_p2 = substr($sql_p2, 0, -3);
        $fullquery = $sql_p1 . $sql_p2;
        $stmt = $this->conn->prepare($fullquery);
        $fullquery = $sql_p1 . $sql_p2;
        $stmt = $this->conn->prepare($fullquery);
        $stmt->execute();
        $result = $stmt;
        $num = $result->rowCount();
        for ($i = 0; $i < $num; $i++) {
            $row = $result->fetch(PDO::FETCH_ASSOC);
            array_push($data, $row);
        }
        echo json_encode(
            array(
                "code" => 200,
                "message" => $data
            )
        );
    }

    public function read_XRoute($page)
    {
        $limit = 10;
        $start = ($page - 1) * $limit;
        $query = 'SELECT r.id, r.path, r.start as sid, r.end as eid, n.name as start, m.name as end
        FROM routes r
        JOIN nodes n ON r.start = n.id
        JOIN nodes m ON r.end = m.id
        ORDER BY id DESC LIMIT ' . $start . ', ' . $limit;
        $stmt = $this->conn->prepare($query);
        if ($stmt->execute()) {
            $route_array = array();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                $route_item = array(
                    'id' => $id,
                    'path' => $path,
                    'sid' => $sid,
                    'eid' => $eid,
                    'start' => $start,
                    'end' => $end,
                );
                array_push($route_array, $route_item);
            }
            $response = array(
                "code" => 200,
                "message" => $route_array,
            );
            echo json_encode($response);
        } else {
            $response = array(
                "code" => 500,
                "message" => "No data",
            );
            echo json_encode($response);
        }
    }

    function getCoordinatesFromPath($path)
    {
        $this->getCoordinatesFromArrayOfId($this->getArrayOfIdFromPath($path));
    }

    function getArrayOfIdFromPath($path)
    {
        $arrayOfID = array();
        $nodes = null;
        $size = 0;
        // // if(sizeof($path) == 1){
        //     $nodes = explode(", ", $path[0]);
        //     $size = sizeof($nodes);
        //     $nodes = array_values(array_unique($nodes));
        // }else{
        $nodes = explode(", ", $path);
        $size =  sizeof($nodes);
        // }
        for ($i = 0; $i < $size; $i++) {
            $query =  "SELECT id FROM nodes WHERE name = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $nodes[$i]);
            if ($stmt->execute()) {
                $result = $stmt;
                $row = $result->fetch(PDO::FETCH_ASSOC);
                // extract($row);
                array_push($arrayOfID, $row['id']);
            } else {
            }
        }
        return $arrayOfID;
    }

    function getCoordinatesFromArrayOfId($array_of_node_id)
    {
        $array_of_nodes = array();
        for ($i = 0; $i < sizeof($array_of_node_id); $i++) {
            $query =  "SELECT name, 
            longitude as lat, 
            latitude as lng 
            FROM nodes 
            WHERE id = :id Limit 1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $array_of_node_id[$i]);
            if ($stmt->execute()) {
                $result = $stmt;
                $row = $result->fetch(PDO::FETCH_ASSOC);
                array_push($array_of_nodes, $row);
            } else {
                $response = array(
                    "code" => "500",
                    "message" => "Something went wrong."
                );
                return false;
            }
        }
        $response = array(
            "code" => 200,
            "message" => $array_of_nodes
        );
        echo json_encode($response);
    }

    function getPathDirectly($from, $to)
    {
        $path_array = array();
        $query = "SELECT r.path FROM routes r 
        JOIN nodes n ON r.start = n.id
        JOIN nodes m ON r.end = m.id 
        WHERE n.name LIKE :frm AND m.name LIKE :too
        LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $from  = "%" . $from . "%";
        $to  = "%" . $to;
        $stmt->bindParam(':frm', $from);
        $stmt->bindParam(':too', $to);
        if ($stmt->execute()) {
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                array_push($path_array, $path);
            }
            return $path_array;
        } else {
            return false;
        }
    }

    function getPathIndirectly($from, $to)
    {
        $path_array = array();
        $query = "SELECT r.path FROM routes r 
        WHERE r.path LIKE :frm AND r.path LIKE :too
        LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $from  = "%" . $from . "%";
        $to  = "%" . $to . "%";
        $stmt->bindParam(':frm', $from);
        $stmt->bindParam(':too', $to);
        if ($stmt->execute()) {
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                array_push($path_array, $path);
            }
            return $path_array;
        } else {
            return false;
        }
    }

    function getPathAdvanceSearch($from, $to)
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
        if ($stmt1->execute()) {
            while ($row = $stmt1->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                array_push($arr1, $path);
            }
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
                array_push($arr2, $path);
            }
        }

        foreach ($arr1 as $rawPath1) {
            $arrayzedPath1 = explode(", ", $rawPath1);
            $temp_p1 = $arrayzedPath1;
            $index_of_from = array_search($from, $arrayzedPath1);
            $index_of_com_on_p1 = null;
            foreach ($arr2 as $rawPath2) {
                $arrayzedPath2 = explode(", ", $rawPath2);
                $temp_p2 = $arrayzedPath2;
                $index_of_to = array_search($to, $arrayzedPath2);
                $index_of_com_on_p1 = null;
                if (count(array_intersect($arrayzedPath1, $arrayzedPath2)) === 0) {
                } else {
                    $common_elements = array();
                    $intersect_elements = array_intersect($arrayzedPath1, $arrayzedPath2);
                    $c_size  = sizeof($intersect_elements);
                    foreach ($intersect_elements as $key => $value) {
                        array_push($common_elements, $value);
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
                    if ($index_of_com_on_p2 < $index_of_to) {
                        $temp_p2 = array_slice($temp_p2, $index_of_to, ($index_of_com_on_p2 + 1));
                    } else {
                        $temp_p2 = array_slice($temp_p2, $index_of_to, ($index_of_com_on_p2  + 1));
                        $temp_p2 = array_reverse($temp_p2);
                    }
                    $main_array = (array_unique(array_merge($temp_p1, $temp_p2)));
                    return $main_array;
                }
            }
        }
    }

    public function read_routeByFnT($from, $to)
    {
        $path_array = array();
        $path_array = $this->getPathDirectly($from, $to);
        if ($path_array == null) {

            $path_array = $this->getPathIndirectly($from, $to);

            if ($path_array == null) {

                $path_array = $this->getPathAdvanceSearch($from, $to);
                if ($path_array == null) {
                    $response = array(
                        "code" => "400",
                        "message" => "Cannot Determine Path"
                    );
                } else {
                    $path_array = array_values($path_array);
                    $array_id = $this->getArrayOfIdFromPathPhone($path_array);
                    $this->getCoordinatesFromArrayOfId($array_id);
                }
            } else {
                $array_id = $this->getArrayOfIdFromPathPhone($path_array);
                $this->getCoordinatesFromArrayOfId($array_id);
            }
        } else {
            $array_id = $this->getArrayOfIdFromPathPhone($path_array);
            $this->getCoordinatesFromArrayOfId($array_id);
        }
    }


    function getArrayOfIdFromPathPhone($path)
    {
        $arrayOfID = array();
        $nodes = null;
        $size = 0;
        //explode
        $size = sizeof($path);
        $nodes = array_values(array_unique($path));

        for ($i = 0; $i < $size; $i++) {
            $query =  "SELECT id FROM nodes WHERE name LIKE :id";
            $stmt = $this->conn->prepare($query);
            $nodes[$i] = '%' . $nodes[$i] . '%';
            $stmt->bindParam(':id', $nodes[$i]);
            if ($stmt->execute()) {
                $result = $stmt;
                $row = $result->fetch(PDO::FETCH_ASSOC);
                // extract($row);
                array_push($arrayOfID, $row['id']);
            } else {
            }
        }
        return $arrayOfID;
    }
}