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
        $products = $this->productModel->getAll();
        echo json_encode($products);
    }

    public function getById($id)
    {
        $product = $this->productModel->getById($id);
        if ($product) {
            echo json_encode($product);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Producto no encontrado']);
        }
    }

    public function create()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (isset($data['nombre_producto'], $data['unidad_medida_producto'], $data['categoria_producto'])) {
            $id = $this->productModel->create($data);
            echo json_encode(['message' => 'Producto creado', 'id_producto' => $id]);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Datos incompletos']);
        }
    }

    public function update($id)
    {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (isset($data['nombre_producto'], $data['unidad_medida_producto'], $data['categoria_producto'])) {
            $success = $this->productModel->update($id, $data);
            echo json_encode(['success' => $success]);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Datos incompletos']);
        }
    }

    public function delete($id)
    {
        $success = $this->productModel->delete($id);
        echo json_encode(['success' => $success]);
    }
}