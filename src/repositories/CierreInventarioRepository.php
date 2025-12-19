<?php

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/CierreInventarioModel.php';

/**
 * Repositorio para la entidad CierreInventario
 * Encapsula las operaciones CRUD contra la base de datos
 */
class CierreInventarioRepository
{
    private $pdo;

    /**
     * Constructor: obtiene la conexión a la base de datos
     */
    public function __construct($pdo = null)
    {
        $this->pdo = $pdo ?: Database::getConnection();
    }

    /**
     * Obtiene todos los registros de cierre_inventario
     *
     * @return array
     */
    public function findAll($limit = null, $offset = 0)
    {
        $sql = "SELECT * FROM cierre_inventario ORDER BY fecha DESC";
        if ($limit !== null) {
            $sql .= " LIMIT $limit OFFSET $offset";
        }
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Cuenta el total de cierre_inventario
     *
     * @return int
     */
    public function count()
    {
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM cierre_inventario");
        return $stmt->fetchColumn();
    }

    /**
     * Obtiene registros de cierre_inventario por rango de fechas
     *
     * @param string $fechaInicio
     * @param string $fechaFinal
     * @param int|null $limit
     * @param int $offset
     * @return array
     */
    public function findByDateRange($fechaInicio, $fechaFinal, $limit = null, $offset = 0)
    {
        $sql = "SELECT * FROM cierre_inventario WHERE fecha BETWEEN ? AND ? ORDER BY fecha DESC";
        if ($limit !== null) {
            $sql .= " LIMIT $limit OFFSET $offset";
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$fechaInicio, $fechaFinal]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Cuenta el total de cierre_inventario por rango de fechas
     *
     * @param string $fechaInicio
     * @param string $fechaFinal
     * @return int
     */
    public function countByDateRange($fechaInicio, $fechaFinal)
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM cierre_inventario WHERE fecha BETWEEN ? AND ?");
        $stmt->execute([$fechaInicio, $fechaFinal]);
        return $stmt->fetchColumn();
    }

    /**
     * Obtiene un cierre_inventario por ID
     *
     * @param int $id
     * @return array|null
     */
    public function findById($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM cierre_inventario WHERE id_cierre_invetarios = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Guarda un cierre_inventario (inserta si no tiene ID, actualiza si lo tiene)
     *
     * @param CierreInventario $cierre
     */
    public function save(CierreInventario $cierre)
    {
        if ($cierre->getIdCierreInventarios()) {
            // Actualizar
            $stmt = $this->pdo->prepare("UPDATE cierre_inventario SET fk_id_producto = ?, fecha = ?, cantidad = ? WHERE id_cierre_invetarios = ?");
            $stmt->execute([
                $cierre->getFkIdProducto(),
                $cierre->getFecha(),
                $cierre->getCantidad(),
                $cierre->getIdCierreInventarios()
            ]);
        } else {
            // Insertar
            $stmt = $this->pdo->prepare("INSERT INTO cierre_inventario (fk_id_producto, fecha, cantidad) VALUES (?, ?, ?)");
            $stmt->execute([
                $cierre->getFkIdProducto(),
                $cierre->getFecha(),
                $cierre->getCantidad()
            ]);
            $cierre->setIdCierreInventarios($this->pdo->lastInsertId());
        }
    }

    /**
     * Elimina un cierre_inventario por ID
     *
     * @param int $id
     */
    public function delete($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM cierre_inventario WHERE id_cierre_invetarios = ?");
        $stmt->execute([$id]);
    }
}