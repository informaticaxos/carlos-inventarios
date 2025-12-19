<?php

require_once __DIR__ . '/../models/InventoryClosing.php';

class InventoryClosingController
{
    private $inventoryClosingModel;

    public function __construct()
    {
        global $pdo; 
        
        if ($pdo) {
            $this->inventoryClosingModel = new InventoryClosing($pdo);
        }
    }

    public function getAll()
    {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 5; // Cantidad por página
        $offset = ($page - 1) * $limit;

        $totalRecords = $this->inventoryClosingModel->countAll();
        $totalPages = ceil($totalRecords / $limit);
        $data = $this->inventoryClosingModel->getAll($limit, $offset);

        echo json_encode([
            'state' => 1,
            'message' => 'Listado de cierres de inventario obtenido con éxito',
            'data' => $data,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $totalPages,
                'total_records' => $totalRecords
            ]
        ]);
    }

    public function getById($id)
    {
        $item = $this->inventoryClosingModel->getById($id);
        if ($item) {
            echo json_encode([
                'state' => 1,
                'message' => 'Cierre de inventario encontrado',
                'data' => $item
            ]);
        } else {
            http_response_code(404);
            echo json_encode(['state' => 0, 'message' => 'Cierre de inventario no encontrado', 'data' => []]);
        }
    }

    public function create()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!empty($data['fk_id_producto']) && !empty($data['fecha']) && isset($data['cantidad'])) {
            $id = $this->inventoryClosingModel->create($data);
            echo json_encode([
                'state' => 1, 
                'message' => 'Cierre de inventario creado con éxito', 
                'data' => ['id_cierre_invetarios' => $id]
            ]);
        } else {
            http_response_code(400);
            echo json_encode(['state' => 0, 'message' => 'Datos incompletos', 'data' => []]);
        }
    }

    public function update($id)
    {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!empty($data['fk_id_producto']) && !empty($data['fecha']) && isset($data['cantidad'])) {
            $success = $this->inventoryClosingModel->update($id, $data);
            echo json_encode(['state' => 1, 'message' => 'Cierre de inventario actualizado con éxito', 'data' => []]);
        } else {
            http_response_code(400);
            echo json_encode(['state' => 0, 'message' => 'Datos incompletos', 'data' => []]);
        }
    }

    public function delete($id)
    {
        $success = $this->inventoryClosingModel->delete($id);
        echo json_encode(['state' => 1, 'message' => 'Cierre de inventario eliminado con éxito', 'data' => []]);
    }
}