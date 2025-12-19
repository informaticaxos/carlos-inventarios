<?php

require_once __DIR__ . '/../models/CierreInventarioModel.php';
require_once __DIR__ . '/../repositories/CierreInventarioRepository.php';

class CierreInventarioController
{
    private $cierreRepository;

    public function __construct($pdo)
    {
        $this->cierreRepository = new CierreInventarioRepository($pdo);
    }

    // GET /cierre_inventario
    public function getAll()
    {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = 10;
        $offset = ($page - 1) * $perPage;

        $data = $this->cierreRepository->findAll($perPage, $offset);
        $total = $this->cierreRepository->count();
        $lastPage = ceil($total / $perPage);

        echo json_encode([
            "state" => 1,
            "message" => "Datos obtenidos exitosamente",
            "data" => $data,
            "pagination" => [
                "current_page" => $page,
                "per_page" => $perPage,
                "total" => $total,
                "last_page" => $lastPage
            ]
        ]);
    }

    // GET /cierre_inventario/{id}
    public function getById($id)
    {
        $data = $this->cierreRepository->findById($id);
        if ($data) {
            echo json_encode([
                "state" => 1,
                "message" => "Dato obtenido exitosamente",
                "data" => $data
            ]);
        } else {
            http_response_code(404);
            echo json_encode([
                "state" => 0,
                "message" => "Cierre de inventario no encontrado"
            ]);
        }
    }

    // POST /cierre_inventario
    public function create()
    {
        $input = json_decode(file_get_contents('php://input'), true);

        if (isset($input['fk_id_producto']) && isset($input['fecha']) && isset($input['cantidad'])) {
            $cierre = new CierreInventario();
            $cierre->setFkIdProducto($input['fk_id_producto']);
            $cierre->setFecha($input['fecha']);
            $cierre->setCantidad($input['cantidad']);

            $this->cierreRepository->save($cierre);

            http_response_code(201);
            echo json_encode([
                "state" => 1,
                "message" => "Cierre de inventario creado exitosamente",
                "data" => ["id_cierre_invetarios" => $cierre->getIdCierreInventarios()]
            ]);
        } else {
            http_response_code(400);
            echo json_encode([
                "state" => 0,
                "message" => "Datos incompletos. Se requieren 'fk_id_producto', 'fecha' y 'cantidad'"
            ]);
        }
    }

    // PUT /cierre_inventario/{id}
    public function update($id)
    {
        $input = json_decode(file_get_contents('php://input'), true);
        
        $existingData = $this->cierreRepository->findById($id);
        if (!$existingData) {
            http_response_code(404);
            echo json_encode(["state" => 0, "message" => "Cierre de inventario no encontrado"]);
            return;
        }

        try {
            $cierre = new CierreInventario($id, 
                $input['fk_id_producto'] ?? $existingData['fk_id_producto'],
                $input['fecha'] ?? $existingData['fecha'],
                $input['cantidad'] ?? $existingData['cantidad']
            );

            $this->cierreRepository->save($cierre);
            echo json_encode([
                "state" => 1,
                "message" => "Cierre de inventario actualizado exitosamente",
                "data" => []
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                "state" => 0,
                "message" => "Error al actualizar: " . $e->getMessage()
            ]);
        }
    }

    // DELETE /cierre_inventario/{id}
    public function delete($id)
    {
        if (!$this->cierreRepository->findById($id)) {
            http_response_code(404);
            echo json_encode([
                "state" => 0,
                "message" => "Cierre de inventario no encontrado"
            ]);
            return;
        }

        try {
            $this->cierreRepository->delete($id);
            echo json_encode([
                "state" => 1,
                "message" => "Cierre de inventario eliminado exitosamente",
                "data" => []
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                "state" => 0,
                "message" => "Error al eliminar: " . $e->getMessage()
            ]);
        }
    }
}