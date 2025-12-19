<?php

require_once __DIR__ . '/../models/Product.php';

class ProductController
{
    private $productModel;

    public function __construct()
    {
        // TODO: Obtener la instancia de conexión a la base de datos de tu configuración
        // Ejemplo: $pdo = Database::getConnection();
        // Por ahora, asumiremos que $pdo está disponible o se pasa globalmente
        global $pdo; 
        
        if ($pdo) {
            $this->productModel = new Product($pdo);
        }
    }

    public function getAll()
    {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 5; // Cantidad de productos por página
        $offset = ($page - 1) * $limit;

        $totalRecords = $this->productModel->countAll();
        $totalPages = ceil($totalRecords / $limit);
        $products = $this->productModel->getAll($limit, $offset);

        echo json_encode([
            'state' => 1,
            'message' => 'Listado de productos obtenido con éxito',
            'data' => $products,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $totalPages,
                'total_records' => $totalRecords
            ]
        ]);
    }

    public function getById($id)
    {
        $product = $this->productModel->getById($id);
        if ($product) {
            echo json_encode([
                'state' => 1,
                'message' => 'Producto encontrado',
                'data' => $product
            ]);
        } else {
            http_response_code(404);
            echo json_encode(['state' => 0, 'message' => 'Producto no encontrado', 'data' => []]);
        }
    }

    public function create()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!empty($data['nombre_producto']) && !empty($data['unidad_medida_producto']) && !empty($data['categoria_producto'])) {
            $id = $this->productModel->create($data);
            echo json_encode([
                'state' => 1, 
                'message' => 'Producto creado con éxito', 
                'data' => ['id_producto' => $id]
            ]);
        } else {
            http_response_code(400);
            echo json_encode(['state' => 0, 'message' => 'Datos incompletos', 'data' => []]);
        }
    }

    public function update($id)
    {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!empty($data['nombre_producto']) && !empty($data['unidad_medida_producto']) && !empty($data['categoria_producto'])) {
            $success = $this->productModel->update($id, $data);
            echo json_encode(['state' => 1, 'message' => 'Producto actualizado con éxito', 'data' => []]);
        } else {
            http_response_code(400);
            echo json_encode(['state' => 0, 'message' => 'Datos incompletos', 'data' => []]);
        }
    }

    public function delete($id)
    {
        $success = $this->productModel->delete($id);
        echo json_encode(['state' => 1, 'message' => 'Producto eliminado con éxito', 'data' => []]);
    }
}