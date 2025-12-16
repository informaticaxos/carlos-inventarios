<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once '../config/Database.php';
require_once '../controllers/CierreInventarioController.php';

$database = new Database();
$db = $database->getConnection();

$controller = new CierreInventarioController($db);

$request_method = $_SERVER["REQUEST_METHOD"];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode('/', $uri);

if ($request_method === 'GET') {
    if (isset($uri[4]) && is_numeric($uri[4])) {
        $controller->getById($uri[4]);
    } else {
        $controller->getAll();
    }
} elseif ($request_method === 'POST') {
    $controller->create();
} elseif ($request_method === 'PUT') {
    if (isset($uri[4]) && is_numeric($uri[4])) {
        $controller->update($uri[4]);
    } else {
        http_response_code(400);
        echo json_encode(array("message" => "Invalid ID."));
    }
} elseif ($request_method === 'DELETE') {
    if (isset($uri[4]) && is_numeric($uri[4])) {
        $controller->delete($uri[4]);
    } else {
        http_response_code(400);
        echo json_encode(array("message" => "Invalid ID."));
    }
} else {
    http_response_code(405);
    echo json_encode(array("message" => "Method not allowed."));
}
?>
