<?php

require_once __DIR__ . '/../models/Product.php';

class ProductController
{
    private $productModel;

    public function __construct($pdo)
    {
        $this->productModel = new Product($pdo);
    }

    // GET /producto
    public function getAll()
    {
        // Obtener parámetros de paginación de la URL (ej: ?limit=10&offset=0)
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 100;
        $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;

        $data = $this->productModel->getAll($limit, $offset);
        echo json_encode([
            "state" => 1,
            "data" => $data
        ]);
    }

    // GET /producto/{id}
    public function getById($id)
    {
        $data = $this->productModel->getById($id);
        if ($data) {
            echo json_encode([
                "state" => 1,
                "data" => $data
            ]);
        } else {
            http_response_code(404);
            echo json_encode([
                "state" => 0,
                "message" => "Producto no encontrado"
            ]);
        }
    }

    // POST /producto
    public function create()
    {
        // Leer el JSON enviado en el cuerpo de la petición
        $input = json_decode(file_get_contents('php://input'), true);

        if (isset($input['nombre_producto'], $input['unidad_medida_producto'], $input['categoria_producto'])) {
            $id = $this->productModel->create($input);
            http_response_code(201);
            echo json_encode([
                "state" => 1,
                "message" => "Producto creado exitosamente",
                "id" => $id
            ]);
        } else {
            http_response_code(400);
            echo json_encode([
                "state" => 0,
                "message" => "Datos incompletos. Se requiere nombre_producto, unidad_medida_producto y categoria_producto"
            ]);
        }
    }

    // PUT /producto/{id}
    public function update($id)
    {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if ($this->productModel->update($id, $input)) {
            echo json_encode(["state" => 1, "message" => "Producto actualizado"]);
        } else {
            http_response_code(500);
            echo json_encode(["state" => 0, "message" => "No se pudo actualizar el producto"]);
        }
    }

    // DELETE /producto/{id}
    public function delete($id)
    {
        if ($this->productModel->delete($id)) {
            echo json_encode(["state" => 1, "message" => "Producto eliminado"]);
        } else {
            http_response_code(500);
            echo json_encode(["state" => 0, "message" => "No se pudo eliminar el producto"]);
        }
    }
}