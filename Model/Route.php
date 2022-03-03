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