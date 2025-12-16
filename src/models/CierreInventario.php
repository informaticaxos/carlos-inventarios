<?php

class CierreInventario {
    private $conn;
    private $table_name = "cierre_inventario";

    public $id_cierre_inventario;
    public $fk_id_producto;
    public $fecha;
    public $cantidad;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function read($fechaInicio = null, $fechaFin = null) {
        $query = "SELECT id_cierre_inventario, fk_id_producto, fecha, cantidad FROM " . $this->table_name;

        $conditions = array();
        $params = array();

        if ($fechaInicio !== null) {
            $conditions[] = "fecha >= :fecha_inicio";
            $params[':fecha_inicio'] = $fechaInicio;
        }

        if ($fechaFin !== null) {
            $conditions[] = "fecha <= :fecha_fin";
            $params[':fecha_fin'] = $fechaFin;
        }

        if (!empty($conditions)) {
            $query .= " WHERE " . implode(" AND ", $conditions);
        }

        $query .= " ORDER BY fecha DESC";

        $stmt = $this->conn->prepare($query);

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->execute();

        return $stmt;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " SET fk_id_producto=:fk_id_producto, fecha=:fecha, cantidad=:cantidad";

        $stmt = $this->conn->prepare($query);

        $this->fk_id_producto = htmlspecialchars(strip_tags($this->fk_id_producto));
        $this->fecha = htmlspecialchars(strip_tags($this->fecha));
        $this->cantidad = htmlspecialchars(strip_tags($this->cantidad));

        $stmt->bindParam(":fk_id_producto", $this->fk_id_producto);
        $stmt->bindParam(":fecha", $this->fecha);
        $stmt->bindParam(":cantidad", $this->cantidad);

        if($stmt->execute()) {
            return true;
        }

        return false;
    }

    public function readOne() {
        $query = "SELECT id_cierre_inventario, fk_id_producto, fecha, cantidad FROM " . $this->table_name . " WHERE id_cierre_inventario = ? LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id_cierre_inventario);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->fk_id_producto = $row['fk_id_producto'];
        $this->fecha = $row['fecha'];
        $this->cantidad = $row['cantidad'];
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . " SET fk_id_producto = :fk_id_producto, fecha = :fecha, cantidad = :cantidad WHERE id_cierre_inventario = :id_cierre_inventario";

        $stmt = $this->conn->prepare($query);

        $this->fk_id_producto = htmlspecialchars(strip_tags($this->fk_id_producto));
        $this->fecha = htmlspecialchars(strip_tags($this->fecha));
        $this->cantidad = htmlspecialchars(strip_tags($this->cantidad));
        $this->id_cierre_inventario = htmlspecialchars(strip_tags($this->id_cierre_inventario));

        $stmt->bindParam(':fk_id_producto', $this->fk_id_producto);
        $stmt->bindParam(':fecha', $this->fecha);
        $stmt->bindParam(':cantidad', $this->cantidad);
        $stmt->bindParam(':id_cierre_inventario', $this->id_cierre_inventario);

        if($stmt->execute()) {
            return true;
        }

        return false;
    }

    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id_cierre_inventario = ?";

        $stmt = $this->conn->prepare($query);
        $this->id_cierre_inventario = htmlspecialchars(strip_tags($this->id_cierre_inventario));
        $stmt->bindParam(1, $this->id_cierre_inventario);

        if($stmt->execute()) {
            return true;
        }

        return false;
    }
}
?>
