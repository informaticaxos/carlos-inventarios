<?php

require_once __DIR__ . '/../models/ProductModel.php';
require_once __DIR__ . '/../repositories/ProductRepository.php';

class ProductController
{
    private $productRepository;

    public function __construct($pdo)
    {
        $this->productRepository = new ProductRepository($pdo);
    }

    // GET /producto
    public function getAll()
    {
        if (isset($_GET['all'])) {
            $search = isset($_GET['search']) ? trim($_GET['search']) : null;
            $data = $this->productRepository->findAll(null, 0, $search);
            echo json_encode([
                "state" => 1,
                "data" => $data
            ]);
        } else {
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $perPage = 10;
            $offset = ($page - 1) * $perPage;
            $search = isset($_GET['search']) ? trim($_GET['search']) : null;

            $data = $this->productRepository->findAll($perPage, $offset, $search);
            $total = $this->productRepository->count($search);
            $lastPage = ceil($total / $perPage);

            echo json_encode([
                "state" => 1,
                "data" => $data,
                "pagination" => [
                    "current_page" => $page,
                    "per_page" => $perPage,
                    "total" => $total,
                    "last_page" => $lastPage
                ]
            ]);
        }
    }

    // GET /producto/{id}
    public function getById($id)
    {
        $data = $this->productRepository->findById($id);
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

        // Validar campos mínimos requeridos
        if (isset($input['nombre_producto'])) {
            $product = new Product();
            $product->setNombreProducto($input['nombre_producto'] ?? '');
            $product->setUnidadMedidaProducto($input['unidad_medida_producto'] ?? '');
            $product->setCategoriaProducto($input['categoria_producto'] ?? '');

            $this->productRepository->save($product);

            http_response_code(201);
            echo json_encode([
                "state" => 1,
                "message" => "Producto creado exitosamente",
                "id" => $product->getIdProducto()
            ]);
        } else {
            http_response_code(400);
            echo json_encode([
                "state" => 0,
                "message" => "Datos incompletos. Se requiere 'nombre_producto'"
            ]);
        }
    }

    // PUT /producto/{id}
    public function update($id)
    {
        $input = json_decode(file_get_contents('php://input'), true);
        
        // Verificar si existe
        $existingData = $this->productRepository->findById($id);
        if (!$existingData) {
            http_response_code(404);
            echo json_encode(["state" => 0, "message" => "Producto no encontrado"]);
            return;
        }

        try {
            // Crear objeto con datos existentes y sobrescribir con los nuevos
            $product = new Product($id, 
                $input['nombre_producto'] ?? $existingData['nombre_producto'],
                $input['unidad_medida_producto'] ?? $existingData['unidad_medida_producto'],
                $input['categoria_producto'] ?? $existingData['categoria_producto']
            );

            $this->productRepository->save($product);
            echo json_encode(["state" => 1, "message" => "Producto actualizado"]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["state" => 0, "message" => "Error al actualizar: " . $e->getMessage()]);
        }
    }

    // DELETE /producto/{id}
    public function delete($id)
    {
        // Verificar si existe antes de borrar
        if (!$this->productRepository->findById($id)) {
            http_response_code(404);
            echo json_encode(["state" => 0, "message" => "Producto no encontrado"]);
            return;
        }

        try {
            $this->productRepository->delete($id);
            echo json_encode(["state" => 1, "message" => "Producto eliminado"]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["state" => 0, "message" => "Error al eliminar"]);
        }
    }
}