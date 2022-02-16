<?php

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


switch ($en) {
    case 'user':
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