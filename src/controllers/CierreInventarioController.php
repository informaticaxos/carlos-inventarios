<?php

require_once '../repositories/CierreInventarioRepository.php';

class CierreInventarioController {
    private $repository;

    public function __construct($db) {
        $this->repository = new CierreInventarioRepository($db);
    }

    public function getAll() {
        $fechaInicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : null;
        $fechaFin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : null;

        $stmt = $this->repository->getAll($fechaInicio, $fechaFin);
        $num = $stmt->rowCount();

        if($num > 0) {
            $cierres_arr = array();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                $cierre_item = array(
                    "id_cierre_invetarios" => $id_cierre_invetarios,
                    "fk_id_producto" => $fk_id_producto,
                    "fecha" => $fecha,
                    "cantidad" => $cantidad
                );
                array_push($cierres_arr, $cierre_item);
            }

            http_response_code(200);
            echo json_encode(array(
                "state" => 1,
                "message" => "Cierres de inventario obtenidos correctamente",
                "data" => $cierres_arr
            ));
        } else {
            http_response_code(404);
            echo json_encode(array(
                "state" => 0,
                "message" => "No se encontraron cierres de inventario",
                "data" => array()
            ));
        }
    }

    public function getById($id) {
        $cierre = $this->repository->getById($id);

        if($cierre->id_cierre_invetarios != null) {
            $cierre_arr = array(
                "id_cierre_invetarios" => $cierre->id_cierre_invetarios,
                "fk_id_producto" => $cierre->fk_id_producto,
                "fecha" => $cierre->fecha,
                "cantidad" => $cierre->cantidad
            );

            http_response_code(200);
            echo json_encode(array(
                "state" => 1,
                "message" => "Cierre de inventario obtenido correctamente",
                "data" => $cierre_arr
            ));
        } else {
            http_response_code(404);
            echo json_encode(array(
                "state" => 0,
                "message" => "Cierre de inventario no encontrado",
                "data" => null
            ));
        }
    }

    public function create() {
        $data = json_decode(file_get_contents("php://input"));

        if(!empty($data->fk_id_producto) && !empty($data->fecha) && isset($data->cantidad)) {
            if($this->repository->create((array)$data)) {
                http_response_code(201);
                echo json_encode(array(
                    "state" => 1,
                    "message" => "Cierre de inventario creado correctamente",
                    "data" => null
                ));
            } else {
                http_response_code(503);
                echo json_encode(array(
                    "state" => 0,
                    "message" => "Error al crear el cierre de inventario",
                    "data" => null
                ));
            }
        } else {
            http_response_code(400);
            echo json_encode(array(
                "state" => 0,
                "message" => "Datos incompletos para crear el cierre de inventario",
                "data" => null
            ));
        }
    }

    public function update($id) {
        $data = json_decode(file_get_contents("php://input"));

        if(!empty($data->fk_id_producto) && !empty($data->fecha) && isset($data->cantidad)) {
            if($this->repository->update($id, (array)$data)) {
                http_response_code(200);
                echo json_encode(array(
                    "state" => 1,
                    "message" => "Cierre de inventario actualizado correctamente",
                    "data" => null
                ));
            } else {
                http_response_code(503);
                echo json_encode(array(
                    "state" => 0,
                    "message" => "Error al actualizar el cierre de inventario",
                    "data" => null
                ));
            }
        } else {
            http_response_code(400);
            echo json_encode(array(
                "state" => 0,
                "message" => "Datos incompletos para actualizar el cierre de inventario",
                "data" => null
            ));
        }
    }

    public function delete($id) {
        if($this->repository->delete($id)) {
            http_response_code(200);
            echo json_encode(array(
                "state" => 1,
                "message" => "Cierre de inventario eliminado correctamente",
                "data" => null
            ));
        } else {
            http_response_code(503);
            echo json_encode(array(
                "state" => 0,
                "message" => "Error al eliminar el cierre de inventario",
                "data" => null
            ));
        }
    }

    public function getReport() {
        $fechaInicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : null;
        $fechaFin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : null;

        $stmt = $this->repository->getReport($fechaInicio, $fechaFin);
        $num = $stmt->rowCount();

        if($num > 0) {
            // Incluir TCPDF
            require_once '../vendor/tecnickcom/tcpdf/tcpdf.php';

            // Crear nuevo documento PDF
            $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

            // Configurar documento
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetAuthor('Sistema de Inventarios');
            $pdf->SetTitle('Reporte de Cierres de Inventario');
            $pdf->SetSubject('Reporte PDF');
            $pdf->SetKeywords('Inventario, Reporte, PDF');

            // Configurar márgenes
            $pdf->SetMargins(15, 20, 15);
            $pdf->SetHeaderMargin(10);
            $pdf->SetFooterMargin(10);

            // Configurar auto page breaks
            $pdf->SetAutoPageBreak(TRUE, 15);

            // Agregar página
            $pdf->AddPage();

            // Título
            $pdf->SetFont('helvetica', 'B', 16);
            $pdf->Cell(0, 10, 'Reporte de Cierres de Inventario', 0, 1, 'C');
            $pdf->Ln(5);

            // Fechas del reporte
            $pdf->SetFont('helvetica', '', 12);
            if ($fechaInicio && $fechaFin) {
                $pdf->Cell(0, 8, 'Período: ' . date('d/m/Y', strtotime($fechaInicio)) . ' - ' . date('d/m/Y', strtotime($fechaFin)), 0, 1, 'C');
            } elseif ($fechaInicio) {
                $pdf->Cell(0, 8, 'Desde: ' . date('d/m/Y', strtotime($fechaInicio)), 0, 1, 'C');
            } elseif ($fechaFin) {
                $pdf->Cell(0, 8, 'Hasta: ' . date('d/m/Y', strtotime($fechaFin)), 0, 1, 'C');
            }
            $pdf->Ln(5);

            // Fecha de generación
            $pdf->Cell(0, 8, 'Generado el: ' . date('d/m/Y H:i:s'), 0, 1, 'R');
            $pdf->Ln(10);

            // Encabezados de tabla
            $pdf->SetFont('helvetica', 'B', 10);
            $pdf->SetFillColor(240, 240, 240);
            $pdf->Cell(25, 8, 'Fecha', 1, 0, 'C', 1);
            $pdf->Cell(60, 8, 'Producto', 1, 0, 'C', 1);
            $pdf->Cell(25, 8, 'Unidad', 1, 0, 'C', 1);
            $pdf->Cell(40, 8, 'Categoría', 1, 0, 'C', 1);
            $pdf->Cell(30, 8, 'Cantidad', 1, 1, 'C', 1);

            // Datos de la tabla
            $pdf->SetFont('helvetica', '', 9);
            $fill = false;

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $pdf->Cell(25, 6, date('d/m/Y', strtotime($row['fecha'])), 1, 0, 'C', $fill);
                $pdf->Cell(60, 6, $row['nombre_producto'], 1, 0, 'L', $fill);
                $pdf->Cell(25, 6, $row['unidad_medida_producto'], 1, 0, 'C', $fill);
                $pdf->Cell(40, 6, $row['categoria_producto'], 1, 0, 'L', $fill);
                $pdf->Cell(30, 6, number_format($row['cantidad'], 2), 1, 1, 'R', $fill);
                $fill = !$fill;
            }

            // Total de registros
            $pdf->Ln(5);
            $pdf->SetFont('helvetica', 'B', 10);
            $pdf->Cell(0, 8, 'Total de registros: ' . $num, 0, 1, 'L');

            // Configurar headers para descarga
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="reporte_cierres_' . date('Y-m-d') . '.pdf"');
            header('Cache-Control: private, max-age=0, must-revalidate');
            header('Pragma: public');

            // Generar y enviar PDF
            $pdf->Output('reporte_cierres_' . date('Y-m-d') . '.pdf', 'D');
            exit;
        } else {
            http_response_code(404);
            echo json_encode(array(
                "state" => 0,
                "message" => "No se encontraron datos para el reporte en el período especificado",
                "data" => array()
            ));
        }
    }
}
?>
