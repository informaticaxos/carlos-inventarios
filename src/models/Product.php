<?php

class Product
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAll($limit, $offset)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM producto ORDER BY id_producto DESC LIMIT :limit OFFSET :offset");
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM producto WHERE id_producto = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data)
    {
        $sql = "INSERT INTO producto (nombre_producto, unidad_medida_producto, categoria_producto) 
                VALUES (:nombre, :unidad, :categoria)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'nombre' => $data['nombre_producto'],
            'unidad' => $data['unidad_medida_producto'],
            'categoria' => $data['categoria_producto']
        ]);
        return $this->pdo->lastInsertId();
    }

    public function update($id, $data)
    {
        $sql = "UPDATE producto SET 
                nombre_producto = :nombre, 
                unidad_medida_producto = :unidad, 
                categoria_producto = :categoria 
                WHERE id_producto = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'nombre' => $data['nombre_producto'],
            'unidad' => $data['unidad_medida_producto'],
            'categoria' => $data['categoria_producto']
        ]);
    }

    public function delete($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM producto WHERE id_producto = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function countAll()
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) as total FROM producto");
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }
}