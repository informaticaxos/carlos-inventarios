<?php

// API Routes for CierreInventario CRUD operations
// Base URL: https://nestorcornejo.com/carlos-inventarios/api/cierre_inventario

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

// GET /api/cierre_inventario - Obtener todos los cierres de inventario
// Ejemplo en Postman: GET https://nestorcornejo.com/carlos-inventarios/api/cierre_inventario
// Respuesta: JSON con array de cierres de inventario

// GET /api/cierre_inventario/{id} - Obtener un cierre específico
// Ejemplo en Postman: GET https://nestorcornejo.com/carlos-inventarios/api/cierre_inventario/1
// Respuesta: JSON con datos del cierre

// POST /api/cierre_inventario - Crear un nuevo cierre de inventario
// Ejemplo en Postman: POST https://nestorcornejo.com/carlos-inventarios/api/cierre_inventario
// Headers: Content-Type: application/json
// Body (raw JSON):
// {
//     "fk_id_producto": 1,
//     "fecha": "2024-01-15",
//     "cantidad": 100.50
// }

// PUT /api/cierre_inventario/{id} - Actualizar un cierre de inventario
// Ejemplo en Postman: PUT https://nestorcornejo.com/carlos-inventarios/api/cierre_inventario/1
// Headers: Content-Type: application/json
// Body (raw JSON):
// {
//     "fk_id_producto": 1,
//     "fecha": "2024-01-15",
//     "cantidad": 150.75
// }

// DELETE /api/cierre_inventario/{id} - Eliminar un cierre de inventario
// Ejemplo en Postman: DELETE https://nestorcornejo.com/carlos-inventarios/api/cierre_inventario/1

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
