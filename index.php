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

$url_components = explode("/", $_SERVER['REQUEST_URI']);
$method = $_SERVER['REQUEST_METHOD'];
$op = $_GET['op'];
$en = $_GET['en'];

$database = new Database();
$db  = $database->connect();

$user =  new User($db);
$node  = new Coordinate($db);
$route = new Route($db);
$n_contribution = new NodeContribution($db);
$r_contribution = new RouteContribution($db);


switch ($en) {
    case 'user':
        switch ($op) {
            case "registerUser":
                break;
        
            case "loginUser":
                break;
        
            case "getAllUsers":
                $user->read_AllUser();
                break;
            case "getXUsers":
                break;
        
            case "getById":
                break;
        
            case "updateUser":
                break;
        
            case "promoteUser":
                break;
        
            case "deleteUser":
                break;
        
            case "getIdFromPhone";
                break;
        
            default:
                break;
        }
        break;
    case "node":
        switch($operation){
            case "C":
                break;
            case "R1";
                break;
            case "R":
                break;
            case "U":
                break;
            case "D":
                break;
            default:
                break;
        }
        break;
    case 'routes':
        switch($operation){
            case "C":
                break;
            case "R1";
                break;
            case "R":
                break;
            case "U":
                break;
            case "D":
                break;
            default:
                break;
        }
        break;
    case 'contribute_node':
        switch($operation){
            case "C":
                break;
            case "R1";
                break;
            case "R":
                break;
            case "U":
                break;
            case "D":
                break;
            default:
                break;
        }
        break;
    case 'contribute_route':
        switch($operation){
            case "C":
                break;
            case "R1";
                break;
            case "R":
                break;
            case "U":
                break;
            case "D":
                break;
            default:
                break;
        }
            break;
    default:
        break;
    }