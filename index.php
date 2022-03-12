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
        #User Operation Switching
        switch ($op) {
            case "registerUser":
                $data  = json_decode(file_get_contents("php://input"));
                $user->name  = $data->name;
                $user->phone  = $data->phone;
                $user->email  = $data->email;
                $user->password  = $data->password;
                $user->con_password  = $data->con_password;
                $user->register();
                break;

            case "loginUser":
                $data  = json_decode(file_get_contents("php://input"));
                $user->phone = $data->phone;
                $user->password = $data->password;
                $user->login();
                break;

            case "getAllUsers":
                $user->read_AllUser();
                break;
            case "getXUsers":
                if (!isset($_GET['page'])) {
                    $page = 1;
                } else {
                    $page = $_GET['page'];
                }
                $user->read_XUser($page);
                break;
            case "getById":
                $data  = json_decode(file_get_contents("php://input"));
                $user->id  = $data->id;
                $user->read_SingleUser($user->id);
                break;
            case "updateUser":
                $data  = json_decode(file_get_contents("php://input"));
                $user->id  = $data->id;
                $user->name  = $data->name;
                $user->phone  = $data->phone;
                $user->email  = $data->email;
                $user->role_id  = $data->role_id;
                $user->update_user();
                break;

            case "promoteUser":
                $data  = json_decode(file_get_contents("php://input"));
                $user->id  = $data->id;
                $user->promote_user($user->id);
                break;
            case "deleteUser":
                $data  = json_decode(file_get_contents("php://input"));
                $user->id  = $data->id;
                if ($user->deleteUser($user->id)) {
                    header('200 OK', true, 200);
                    $response = array(
                        "code" => 200,
                        "message" => "User deleted."
                    );
                }
                echo json_encode($response);
                break;

            case "getIdFromPhone";
                $data = json_decode(file_get_contents("php://input"));
                $user->phone = $data->phone;
                $user->getIdFromPhone($user->phone);
                break;

            default:
                $response = array(
                    "code" => 400,
                    "message" => "Undefined Operation"
                );
                echo json_encode($response);
                break;
        }
        //End of User Operation
        break;
    case 'node':
        switch ($op) {
            case "addNode":
                $data  = json_decode(file_get_contents("php://input"));
                $node->name  = $data->name;
                $value  = $data->coordinates;
                $coords = array();
                $coords = explode(", ", $value);
                $node->longitude = $coords[0];
                $node->latitude = $coords[1];
                $node->add_Node();
                break;
            case "getById":
                $data  = json_decode(file_get_contents("php://input"));
                $node->id = $data->id;
                $node->read_SingleNodeById($node->id);
                break;
            case "getByName":
                $data  = json_decode(file_get_contents("php://input"));
                $node->name = $data->name;
                $node->read_SingleNodeByName($node->name);
                break;
            case "getAllNode":
                $node->read_AllNode();
                break;
            case "getXNode":
                if (!isset($_GET['page'])) {
                    $page = 1;
                } else {
                    $page = $_GET['page'];
                }
                $node->read_XNode($page);
                break;
            case "updateNode":
                $data  = json_decode(file_get_contents("php://input"));
                $node->id = $data->id;
                $node->name  = $data->name;
                $node->longitude =  $data->longitude;
                $node->latitude =  $data->latitude;
                $node->update_Node();
                break;
            case "deleteNode":
                $data  = json_decode(file_get_contents("php://input"));
                $node->id  = $data->id;
                $node->delete_Node($node->id);
                break;
            default:
                echo "no operation eta";
                break;
        }
        break;
        # End of node operations

    case 'routes':
        # code...
        switch ($op) {
            case "addRoute":
                $route =  json_decode(file_get_contents("php://input"));
                $route->add_Route();
                break;
            case "getAllRoute":
                $route->read_RouteAll();
                break;
            case "getXRoute":
                if (!isset($_GET['page'])) {
                    $page = 1;
                } else {
                    $page = $_GET['page'];
                }
                $route->read_XRoute($page);
                break;
            case "getById":
                $data =  json_decode(file_get_contents("php://input"));
                $route->id = $data->id;
                $route->read_routeById($route->id);
                break;
            case "getByRouteNum":
                $data =  json_decode(file_get_contents("php://input"));
                $route->route_no = $data->route_no;
                $route->read_routeByNo($route->route_no);
                break;
            case "getByStart":
                $data =  json_decode(file_get_contents("php://input"));
                $route->start = $data->start;
                $route->read_routeByStart($route->start);
                break;
            case "getByEnd":
                $data =  json_decode(file_get_contents("php://input"));
                $route->start = $data->start;
                $route->read_routeByStart($route->start);
                break;

            case "getToNode":
                //ali different wala this
                $data =  json_decode(file_get_contents("php://input"));
                $route->node = $data->node;
                $route->read_routeToNode($route->node);
                break;

            case "getPathCoords":
                $data =  json_decode(file_get_contents("php://input"));
                $route->path = $data->path;
                $route->read_nodesByPath($route->path);
                break;
            case "getByFromAndTo":
                $data =  json_decode(file_get_contents("php://input"));
                $route->start = $data->from;
                $route->end = $data->to;
                $route->read_routeByFnT($data->from, $data->to);
                break;
            case "updateRoute":
                $data =  json_decode(file_get_contents("php://input"));
                $route->id = $data->id;
                $route->path = $data->path;
                $route->start = $data->start;
                $route->end = $data->end;
                $route->route_no = $data->route_no;
                $route->update_route($route->id);
                break;
            case "deleteRoute":
                $data =  json_decode(file_get_contents("php://input"));
                $route->id = $data->id;
                $route->delete_Route($route->id);
                break;
            case "advanceSearch":
                $data = json_decode(file_get_contents("php://input"));
                $route->getPathAdvanceSearch($data->from, $data->to);
                break;
            case "getPathCoordinates":
                $data = json_decode(file_get_contents("php://input"));
                $route->getCoordinatesFromPath($data->path);
                // echo $data->path;
                break;
            default:
                break;
        }
        #End od route operation
        break;

    case 'contribute_node':
        switch ($op) {
            case "addContribution":
                $data  = json_decode(file_get_contents("php://input"));
                $n_contribution->coordinate_id = htmlspecialchars(strip_tags($data->coordinate_id));
                $n_contribution->user_id = htmlspecialchars(strip_tags($data->user_id));
                $n_contribution->longitude = htmlspecialchars(strip_tags($data->longitude));
                $n_contribution->latitude = htmlspecialchars(strip_tags($data->latitude));
                $n_contribution->add_contirbution();
                break;
            case "readAllContributions":
                $n_contribution->read_AllContributions();
                # code...
                break;
            case "readXContributions":
                if (!isset($_GET['page'])) {
                    $page = 1;
                } else {
                    $page = $_GET['page'];
                }
                $n_contribution->read_XContributions($page);
                # code...
                break;
            case "readContributionsByUser":
                $data  = json_decode(file_get_contents("php://input"));
                $n_contribution->user_id = htmlspecialchars(strip_tags($data->user_id));
                $n_contribution->read_UserContribution($n_contribution->user_id);

                # code...
                break;
            case "readContributionsByNode":
                $data  = json_decode(file_get_contents("php://input"));
                $n_contribution->coordinate_id = htmlspecialchars(strip_tags($data->coordinate_id));
                $n_contribution->read_NodeContribution($n_contribution->coordinate_id);
                # code...
                break;
            case "readContributionsById":
                $data  = json_decode(file_get_contents("php://input"));
                $n_contribution->id = htmlspecialchars(strip_tags($data->id));
                $n_contribution->read_SingleContribution($n_contribution->id, $data->admin);
                # code...
                break;
            case "acknowledgeContribution":
                $data  = json_decode(file_get_contents("php://input"));
                $n_contribution->id = htmlspecialchars(strip_tags($data->id));
                $n_contribution->coordinate_id = htmlspecialchars(strip_tags($data->coordinate_id));
                $active_uid = htmlspecialchars(strip_tags($data->active_uid));
                $n_contribution->acknowledgeContribution($n_contribution->id, $n_contribution->coordinate_id, $active_uid);
                # code...
                break;
            case "acceptContribution":
                $data  = json_decode(file_get_contents("php://input"));
                $n_contribution->id = $data->id;
                $n_contribution->coordinate_id = $data->coordinate_id;
                $n_contribution->acceptContribution($n_contribution->id, $n_contribution->coordinate_id);
                # code...
                break;
            case "deleteContribution":
                $data  = json_decode(file_get_contents("php://input"));
                $n_contribution->id = htmlspecialchars(strip_tags($data->id));
                $n_contribution->delete_Contribution($n_contribution->id);
                break;
            default:
                # code...
                break;
        }
        break;

    case 'contribute_route':
        switch ($op) {
            case "addContribution":
                $data  = json_decode(file_get_contents("php://input"));
                $r_contribution->user_id = ($data->user_id);
                $r_contribution->path = ($data->path);
                $r_contribution->add_contirbution($data->start, $data->end);
                break;
            case "readAllContributions":
                $r_contribution->read_AllContributions();
                break;
            case "read_XContributions":
                if (!isset($_GET['page'])) {
                    $page = 1;
                } else {
                    $page = $_GET['page'];
                }
                $r_contribution->read_XContributions($page);
                break;
            case "getSingleContribution":
                $data  = json_decode(file_get_contents("php://input"));
                $r_contribution->id = htmlspecialchars(strip_tags($data->id));
                $r_contribution->read_SingleContribution($r_contribution->id);
                break;
            case "readContributionsByUser":
                $data  = json_decode(file_get_contents("php://input"));
                $r_contribution->user_id = htmlspecialchars(strip_tags($data->user_id));
                $r_contribution->read_UserContribution($r_contribution->user_id);
                break;
            case "readContributionsByRoute":
                $data  = json_decode(file_get_contents("php://input"));
                $r_contribution->route_id = htmlspecialchars(strip_tags($data->route_id));
                $r_contribution->read_RouteContribution($r_contribution->route_id);
                break;
            case "acknowledgeContribution":
                $data  = json_decode(file_get_contents("php://input"));
                $r_contribution->id = htmlspecialchars(strip_tags($data->id));
                $r_contribution->acknowledgeContribution($r_contribution->id);
                break;
            case "acceptContribution":
                $data  = json_decode(file_get_contents("php://input"));
                $r_contribution->id = htmlspecialchars(strip_tags($data->id));
                $r_contribution->route_id = htmlspecialchars(strip_tags($data->route_id));
                $r_contribution->acceptContribution($r_contribution->id, $r_contribution->route_id);
                break;
            case "deleteContribution":
                $data  = json_decode(file_get_contents("php://input"));
                $r_contribution->id = htmlspecialchars(strip_tags($data->id));
                $r_contribution->delete_Contribution($r_contribution->id);
                break;
            default:
                $response = array(
                    "code" => 500,
                    "message" => "Cannot perform operation: " . $op . " on entity: " . $en
                );
                echo json_encode($response);
                break;
        }
        break;
    default:
        break;
}
