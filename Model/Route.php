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