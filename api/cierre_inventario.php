<?php

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

require_once __DIR__ . '/../src/config/Database.php';
require_once __DIR__ . '/../src/controllers/CierreInventarioController.php';

$pdo = Database::getConnection();
$controller = new CierreInventarioController($pdo);

$method = $_SERVER['REQUEST_METHOD'];
$url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Assuming the URL is /carlos-inventarios/api/cierre_inventario or /carlos-inventarios/api/cierre_inventario/123
$base = '/carlos-inventarios/api/cierre_inventario';
$path = str_replace($base, '', $url);

if ($path === '' || $path === '/') {
    $id = null;
} elseif ($path === '/rango') {
    // Endpoint especial para rango de fechas
    $controller->getByDateRange();
    exit;
} elseif (preg_match('/^\/(\d+)$/', $path, $matches)) {
    $id = $matches[1];
} else {
    http_response_code(404);
    echo json_encode(['state' => 0, 'message' => 'Ruta no encontrada', 'data' => []]);
    exit;
}

try {
    switch ($method) {
        case 'GET':
            if ($id) {
                $controller->getById($id);
            } else {
                $controller->getAll();
            }
            break;
        case 'POST':
            $controller->create();
            break;
        case 'PUT':
            if ($id) {
                $controller->update($id);
            } else {
                http_response_code(400);
                echo json_encode(['state' => 0, 'message' => 'ID requerido para actualizar', 'data' => []]);
            }
            break;
        case 'DELETE':
            if ($id) {
                $controller->delete($id);
            } else {
                http_response_code(400);
                echo json_encode(['state' => 0, 'message' => 'ID requerido para eliminar', 'data' => []]);
            }
            break;
        default:
            http_response_code(405);
            echo json_encode(['state' => 0, 'message' => 'Método no permitido', 'data' => []]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['state' => 0, 'message' => 'Error interno del servidor: ' . $e->getMessage(), 'data' => []]);
}
?>