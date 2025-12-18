<?php

require_once '../repositories/ProductoRepository.php';

class ProductoController {
    private $repository;

    public function __construct($db) {
        $this->repository = new ProductoRepository($db);
    }

    public function getAll() {
        try {
            $stmt = $this->repository->getAll();
            if ($stmt === false) {
                http_response_code(500);
                echo json_encode(array(
                    "state" => 0,
                    "message" => "Error al ejecutar la consulta",
                    "data" => null
                ));
                return;
            }
            $num = $stmt->rowCount();

            if($num > 0) {
                $productos_arr = array();

                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    extract($row);
                    $producto_item = array(
                        "id_producto" => $id_producto,
                        "nombre_producto" => $nombre_producto,
                        "unidad_medida_producto" => $unidad_medida_producto,
                        "categoria_producto" => $categoria_producto
                    );
                    array_push($productos_arr, $producto_item);
                }

                http_response_code(200);
                echo json_encode(array(
                    "state" => 1,
                    "message" => "Productos obtenidos correctamente",
                    "data" => $productos_arr
                ));
            } else {
                http_response_code(404);
                echo json_encode(array(
                    "state" => 0,
                    "message" => "No se encontraron productos",
                    "data" => array()
                ));
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(array(
                "state" => 0,
                "message" => "Error interno del servidor: " . $e->getMessage(),
                "data" => null
            ));
        }
    }

    public function getById($id) {
        $producto = $this->repository->getById($id);

        if($producto->id_producto != null) {
            $producto_arr = array(
                "id_producto" => $producto->id_producto,
                "nombre_producto" => $producto->nombre_producto,
                "unidad_medida_producto" => $producto->unidad_medida_producto,
                "categoria_producto" => $producto->categoria_producto
            );

            http_response_code(200);
            echo json_encode(array(
                "state" => 1,
                "message" => "Producto obtenido correctamente",
                "data" => $producto_arr
            ));
        } else {
            http_response_code(404);
            echo json_encode(array(
                "state" => 0,
                "message" => "Producto no encontrado",
                "data" => null
            ));
        }
    }

    public function create() {
        $data = json_decode(file_get_contents("php://input"));

        if(!empty($data->nombre_producto) && !empty($data->unidad_medida_producto) && !empty($data->categoria_producto)) {
            if($this->repository->create((array)$data)) {
                http_response_code(201);
                echo json_encode(array(
                    "state" => 1,
                    "message" => "Producto creado correctamente",
                    "data" => null
                ));
            } else {
                http_response_code(503);
                echo json_encode(array(
                    "state" => 0,
                    "message" => "Error al crear el producto",
                    "data" => null
                ));
            }
        } else {
            http_response_code(400);
            echo json_encode(array(
                "state" => 0,
                "message" => "Datos incompletos para crear el producto",
                "data" => null
            ));
        }
    }

    public function update($id) {
        $data = json_decode(file_get_contents("php://input"));

        if(!empty($data->nombre_producto) && !empty($data->unidad_medida_producto) && !empty($data->categoria_producto)) {
            if($this->repository->update($id, (array)$data)) {
                http_response_code(200);
                echo json_encode(array(
                    "state" => 1,
                    "message" => "Producto actualizado correctamente",
                    "data" => null
                ));
            } else {
                http_response_code(503);
                echo json_encode(array(
                    "state" => 0,
                    "message" => "Error al actualizar el producto",
                    "data" => null
                ));
            }
        } else {
            http_response_code(400);
            echo json_encode(array(
                "state" => 0,
                "message" => "Datos incompletos para actualizar el producto",
                "data" => null
            ));
        }
    }

    public function delete($id) {
        if($this->repository->delete($id)) {
            http_response_code(200);
            echo json_encode(array(
                "state" => 1,
                "message" => "Producto eliminado correctamente",
                "data" => null
            ));
        } else {
            http_response_code(503);
            echo json_encode(array(
                "state" => 0,
                "message" => "Error al eliminar el producto",
                "data" => null
            ));
        }
    }

    public function getReport() {
        $stmt = $this->repository->getReport();
        $num = $stmt->rowCount();

        if($num > 0) {
            // Usar FPDF para generar el PDF
            require_once '../libs/fpdf.php';

            class PDF extends FPDF {
                function Header() {
                    $this->SetFont('Arial','B',15);
                    $this->Cell(0,10,'Reporte de Productos',0,1,'C');
                    $this->Ln(5);
                }
            }

            $pdf = new PDF();
            $pdf->AliasNbPages();
            $pdf->AddPage();
            $pdf->SetFont('Arial','',12);

            // Fecha de generación
            $pdf->Cell(0, 8, 'Generado el: ' . date('d/m/Y H:i:s'), 0, 1, 'R');
            $pdf->Ln(10);

            // Encabezados de tabla
            $pdf->SetFont('Arial','B',10);
            $pdf->SetFillColor(200,220,255);
            $pdf->Cell(20, 8, 'ID', 1, 0, 'C', true);
            $pdf->Cell(80, 8, 'Producto', 1, 0, 'C', true);
            $pdf->Cell(40, 8, 'Unidad', 1, 0, 'C', true);
            $pdf->Cell(50, 8, 'Categoría', 1, 1, 'C', true);

            // Datos de la tabla
            $pdf->SetFont('Arial','',9);
            $fill = false;
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $pdf->Cell(20, 6, $row['id_producto'], 1, 0, 'C', $fill);
                $pdf->Cell(80, 6, utf8_decode($row['nombre_producto']), 1, 0, 'L', $fill);
                $pdf->Cell(40, 6, utf8_decode($row['unidad_medida_producto']), 1, 0, 'C', $fill);
                $pdf->Cell(50, 6, utf8_decode($row['categoria_producto']), 1, 1, 'L', $fill);
                $fill = !$fill;
            }

            // Total de registros
            $pdf->Ln(5);
            $pdf->SetFont('Arial','B',10);
            $pdf->Cell(0, 8, 'Total de productos: ' . $num, 0, 1, 'L');

            // Configurar headers para descarga
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="reporte_productos_' . date('Y-m-d') . '.pdf"');
            header('Cache-Control: private, max-age=0, must-revalidate');
            header('Pragma: public');

            // Generar y enviar PDF
            $pdf->Output('D', 'reporte_productos_' . date('Y-m-d') . '.pdf');
            exit;
        } else {
            http_response_code(404);
            echo json_encode(array(
                "state" => 0,
                "message" => "No se encontraron productos para el reporte",
                "data" => array()
            ));
        }
    }
}
?>
