<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Method: POST, READ, PUT, DELETE');
header(
    'Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With'
);

$op = $_GET['op'];
$en = $_GET['en'];
require_once 'Model/User.php';
require_once 'Model/Node.php';
require_once 'Model/Route.php';
require_once 'Model/NodeContribution.php';
require_once 'Model/RouteContribution.php';

switch ($en) {
    case "users":
        $user = json_decode(file_get_contents("php://input"));
        switch ($op) {
            case "create":
                $user = cleanAndObjectify(user: $user);
                $result = $user->insert();
                output($result);
                break;
            case "byId":
                $user = cleanAndObjectify(user: $user);
                $result = $user->getById();
                output($result);
                break;
            case "byPhone":
                $user = cleanAndObjectify(user: $user);
                $result = $user->getByPhone();
                output($result);
                break;
            case "getSome":
                $params = json_decode(file_get_contents("php://input"));
                $limit = $params->limit ?? 15;
                $page = $params->page ?? 1;
                $col = $params->col ?? 'id';
                $asc = $params->asc ?? 'asc';
                $result = (new User())->getSome($limit, $page, $col, $asc);
                output($result);
                break;
            case "getAll":
                $result = (new User())->getAll();
                output($result);
                break;
            case "delete":
                $user = cleanAndObjectify(user: $user);
                $result = $user->delete();
                output($result);
                break;
            case "update":
                $user = cleanAndObjectify(user: $user);
                $result = $user->update();
                output($result);
                break;
            case "login":
                $user = cleanAndObjectify(user: $user);
                $result = $user->login();
                output($result);
                break;
            case "getDashboardData":
                $user = cleanAndObjectify(user: $user);
                if ($user->id < 3) {
                    $result = $user->getDashboardData();
                    output($result);
                    return;
                }
                error400();
                break;
            case "promote":
                $user = cleanAndObjectify($user);
                $result = $user->promote();
                output($result);
                break;
            default:
                error400();
                break;
        }
        break;
    case "nodes":
        $node = json_decode(file_get_contents("php://input"));
        switch ($op) {
            case "create":
                $node = cleanAndObjectify(node: $node);
                $result = $node->insert();
                output($result);
                break;
            case "byId":
                $node = cleanAndObjectify(node: $node);
                $result = $node->getById();
                output($result);
                break;
            case "byName":
                $node = cleanAndObjectify(node: $node);
                $result = $node->getByName();
                output($result);
                break;
            case "getSome":
                $params = json_decode(file_get_contents("php://input"));
                $limit = $params->limit ?? 15;
                $page = $params->page ?? 1;
                $col = $params->col ?? 'id';
                $asc = $params->asc ?? 'asc';
                $result = (new Node())->getSome($limit, $page, $col, $asc);
                output($result);
                break;
            case "getAll":
                $result = (new Node())->getAll();
                output($result);
                break;
            case "update":
                $node = cleanAndObjectify(node: $node);
                $result = $node->update();
                output($result);
                break;
            case "delete":
                $node = cleanAndObjectify(node: $node);
                $result = $node->delete();
                output($result);
                break;
            default:
                error400();
                break;
        }
        break;
    case "routes":
        $raw = json_decode(file_get_contents("php://input"));
        $route = $raw;
        switch ($op) {
            case "create":
                $route = cleanAndObjectify(route: $route);
                $path = $raw->path;
                $nodes = array();
                foreach ($path as $item) {
                    $n = new Node(name: $item->name, lat: $item->lat, lng: $item->lng);
                    $nodes[] = $n;
                }
                $result = $route->insert($nodes);
                output($result);
                break;
            case "byId":
                $route = cleanAndObjectify(route: $route);
                $result = $route->getById(true);
                output($result);
                break;
            case "byRouteNo":
                $route = cleanAndObjectify(route: $route);
                $result = $route->getByRouteNo(true);
                output($result);
                break;
            case "byName":
                $route = cleanAndObjectify(route: $route);
                $result = $route->getBySingleNode(true);
                output($result);
                break;
            case "byFromAndTo":
                $route = cleanAndObjectify(route: $route);
                $result = $route->getByTwoNodes(true);
                output($result);
                break;
            case "getSome":
                $params = json_decode(file_get_contents("php://input"));
                $limit = $params->limit ?? 15;
                $page = $params->page ?? 1;
                $col = $params->col ?? 'id';
                $asc = $params->asc ?? 'asc';
                $result = (new Route())->getSome($limit, $page, $col, $asc);
                output($result);
                break;
            case "update":
                $path = $raw->path;
                $nodes = array();
                foreach ($path as $item) {
                    $n = new Node(name: $item->name, lat: $item->lat, lng: $item->lng);
                    $nodes[] = $n;
                }

                $route = new Route();
                $result = $route->update(id: $raw->id, nodes: $nodes, routeNo: $raw->routeNo);
                output($result);
                break;
            case "delete":
                $route = cleanAndObjectify(route: $route);
                $result = $route->delete();
                output($result);
                break;
            default:
                error400();
                break;
        }
        break;
    case "contributeNode":
        $cNode = json_decode(file_get_contents("php://input"));
        switch ($op) {
            case "create":
                $cNode = cleanAndObjectify(cNode: $cNode);
                $result = $cNode->insert();
                output($result);
                break;
            case "byId":
                $cNode = cleanAndObjectify(cNode: $cNode);
                $result = $cNode->getById();
                output($result);
                break;
            case "byCId":
                $cNode = cleanAndObjectify(cNode: $cNode);
                $result = $cNode->getByCid();
                output($result);
                break;
            case "byUId":
                $cNode = cleanAndObjectify(cNode: $cNode);
                $result = $cNode->getByUid();
                output($result);
                break;
            case "bySId":
                $cNode = cleanAndObjectify(cNode: $cNode);
                $result = $cNode->getBySid();
                output($result);
                break;
            case "byName":
                $cNode = cleanAndObjectify(cNode: $cNode);
                $result = $cNode->getByName();
                output($result);
                break;
            case "byUser":
                $cNode = cleanAndObjectify(cNode: $cNode);
                $result = $cNode->getByUser();
                output($result);
                break;
            case "review":
                $cNode = cleanAndObjectify(cNode: $cNode);
                $result = $cNode->review();
                output($result);
                break;
            case "approve":
                $cNode = cleanAndObjectify(cNode: $cNode);
                $result = $cNode->approve();
                output($result);
                break;
            case "getSome":
                $params = json_decode(file_get_contents("php://input"));
                $limit = $params->limit ?? 15;
                $page = $params->page ?? 1;
                $col = $params->col ?? 'id';
                $asc = $params->asc ?? 'asc';
                $result = (new NodeContribution())->getSome($limit, $page, $col, $asc);
                output($result);
                break;
            case "delete":
                $cNode = cleanAndObjectify(cNode: $cNode);
                $result = $cNode->delete();
                output($result);
                break;
            default:
                error400();
                break;
        }
        break;
    case "contributeRoute":
        $raw = json_decode(file_get_contents("php://input"));
        $cRoute = $raw;
        switch ($op) {
            case "create":
                $cRoute = cleanAndObjectify(cRoute: $cRoute);
                $cRoute->path = htmlspecialchars(strip_tags($raw->path));
                $result = $cRoute->insert();
                output($result);
                break;
            case "byId":
                $cRoute = cleanAndObjectify(cRoute: $cRoute);
                $result = $cRoute->getById(true);
                output($result);
                break;
            case "byRId":
                $cRoute = cleanAndObjectify(cRoute: $cRoute);
                $result = $cRoute->getByRid();
                output($result);
                break;
            case "byUId":
                $cRoute = cleanAndObjectify(cRoute: $cRoute);
                $result = $cRoute->getByUid();
                output($result);
                break;
            case "getSome":
                $params = json_decode(file_get_contents("php://input"));
                $limit = $params->limit ?? 15;
                $page = $params->page ?? 1;
                $col = $params->col ?? 'id';
                $asc = $params->asc ?? 'asc';
                $result = (new RouteContribution())->getSome($limit, $page, $col, $asc);
                output($result);
                break;
            case "review":
                $cRoute = cleanAndObjectify(cRoute: $cRoute);
                $result = $cRoute->review();
                output($result);
                break;
            case "approve":
                $cRoute = cleanAndObjectify(cRoute: $cRoute);
                $result = $cRoute->approve();
                output($result);
                break;
            case "delete":
                $cRoute = cleanAndObjectify(cRoute: $cRoute);
                $result = $cRoute->delete();
                output($result);
                break;
            default:
                error400();
                break;
        }
        break;
    default:
        error400("Invalid Entity");
        break;
}

function error400($message = "Illegal operation"): void
{
    $response = array(
        "code" => 404,
        "message" => $message
    );
    echo json_encode($response);
}

function error404(): void
{
    $response = array(
        "code" => 404,
        "message" => "Resource not found."
    );
    echo json_encode($response);
}

function error500(): void
{
    $response = array(
        "code" => 500,
        "message" => "Internal Server Error."
    );
    echo json_encode($response);
}

function success($message): void
{
    $response = array(
        "code" => 200,
        "message" => $message
    );
    echo json_encode($response);
}

function output($result): void
{
    switch ($result) {
        case 404:
        case false:
            error404();
            break;
        case 200:
            success(array("Update successful."));
            break;
        case 201:
            success(array("Insert successful"));
            break;
        case 202:
            success(array("Deletion successful."));
            break;
        case 500:
            error500();
            break;
        default:
            success($result);
            break;
    }
}

function cleanAndObjectify(
    $user = null,
    $node = null,
    $route = null,
    $cNode = null,
    $cRoute = null
): User|Node|RouteContribution|Route|NodeContribution|bool {
    $data = false;
    $object = '';
    if (!is_null($user)) {
        $data = $user;
        $object = new User();
    }
    if (!is_null($node)) {
        $data = $node;
        $object = new Node();
    }
    if (!is_null($route)) {
        $data = $route;
        $object = new Route();
    }
    if (!is_null($cNode)) {
        $data = $cNode;
        $object = new NodeContribution();
    }
    if (!is_null($cRoute)) {
        $data = $cRoute;
        $object = new RouteContribution();
    }
    if ($data) {
        foreach ($data as $key => $value) {
            if ($key != 'path') {
                $object->$key = htmlspecialchars(strip_tags($value));
            }
        }
    }
    return $object;
}

