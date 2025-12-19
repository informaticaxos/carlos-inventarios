<?php

class InventoryClosing
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAll($limit, $offset)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM cierre_inventario ORDER BY id_cierre_invetarios DESC LIMIT :limit OFFSET :offset");
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM cierre_inventario WHERE id_cierre_invetarios = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data)
    {
        $sql = "INSERT INTO cierre_inventario (fk_id_producto, fecha, cantidad) 
                VALUES (:fk_id_producto, :fecha, :cantidad)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'fk_id_producto' => $data['fk_id_producto'],
            'fecha' => $data['fecha'],
            'cantidad' => $data['cantidad']
        ]);
        return $this->pdo->lastInsertId();
    }

    public function update($id, $data)
    {
        $sql = "UPDATE cierre_inventario SET 
                fk_id_producto = :fk_id_producto, 
                fecha = :fecha, 
                cantidad = :cantidad 
                WHERE id_cierre_invetarios = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'fk_id_producto' => $data['fk_id_producto'],
            'fecha' => $data['fecha'],
            'cantidad' => $data['cantidad']
        ]);
    }

    public function delete($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM cierre_inventario WHERE id_cierre_invetarios = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function countAll()
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) as total FROM cierre_inventario");
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }
}