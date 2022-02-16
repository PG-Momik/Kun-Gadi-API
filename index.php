<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Method: POST, READ, PUT, DELETE');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With');

require_once 'Model/Database.php';
require_once 'Model/User.php';
require_once 'Model/Route.php';
require_once 'Model/Coordinate.php';
require_once 'Model/NodeContribution.php';
require_once 'Model/RouteContribution.php';

$database = new Database();
$db  = $database->connect();

$user =  new User($db);
$node  = new Coordinate($db);
$route = new Route($db);
$n_contribution = new NodeContribution($db);
$r_contribution = new RouteContribution($db);

$url_components = explode("/", $_SERVER['REQUEST_URI']);
$method = $_SERVER['REQUEST_METHOD'];
$op = $_GET['op'];
$en = $_GET['en'];

switch ($en) {
    case 'user':
        break;
    case "node":
        break;
    case 'routes':
        break;
    case 'contribute_node':
        break;
    case 'contribute_route':
            break;
    default:
        break;
    }