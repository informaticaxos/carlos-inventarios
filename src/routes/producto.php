<?php

// API Routes for Producto CRUD operations
// Base URL: https://nestorcornejo.com/carlos-inventarios/api/producto

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once '../config/Database.php';
require_once '../controllers/ProductoController.php';

$database = new Database();
$db = $database->getConnection();

$controller = new ProductoController($db);

$request_method = $_SERVER["REQUEST_METHOD"];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode('/', $uri);

// GET /api/producto - Obtener todos los productos
// Ejemplo en Postman: GET https://nestorcornejo.com/carlos-inventarios/api/producto
// Respuesta: JSON con array de productos

// GET /api/producto/{id} - Obtener un producto específico
// Ejemplo en Postman: GET https://nestorcornejo.com/carlos-inventarios/api/producto/1
// Respuesta: JSON con datos del producto

// POST /api/producto - Crear un nuevo producto
// Ejemplo en Postman: POST https://nestorcornejo.com/carlos-inventarios/api/producto
// Headers: Content-Type: application/json
// Body (raw JSON):
// {
//     "nombre_producto": "Producto Ejemplo",
//     "unidad_medida_producto": "kg",
//     "categoria_producto": "Categoría Ejemplo"
// }

// PUT /api/producto/{id} - Actualizar un producto
// Ejemplo en Postman: PUT https://nestorcornejo.com/carlos-inventarios/api/producto/1
// Headers: Content-Type: application/json
// Body (raw JSON):
// {
//     "nombre_producto": "Producto Actualizado",
//     "unidad_medida_producto": "kg",
//     "categoria_producto": "Categoría Actualizada"
// }

// DELETE /api/producto/{id} - Eliminar un producto
// Ejemplo en Postman: DELETE https://nestorcornejo.com/carlos-inventarios/api/producto/1

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
