<?php

class CierreInventario
{
    private $id_cierre_invetarios;
    private $fk_id_producto;
    private $fecha;
    private $cantidad;

    public function __construct($id_cierre_invetarios = null, $fk_id_producto = null, $fecha = null, $cantidad = null)
    {
        $this->id_cierre_invetarios = $id_cierre_invetarios;
        $this->fk_id_producto = $fk_id_producto;
        $this->fecha = $fecha;
        $this->cantidad = $cantidad;
    }

    public function getIdCierreInventarios()
    {
        return $this->id_cierre_invetarios;
    }

    public function setIdCierreInventarios($id_cierre_invetarios)
    {
        $this->id_cierre_invetarios = $id_cierre_invetarios;
    }

    public function getFkIdProducto()
    {
        return $this->fk_id_producto;
    }

    public function setFkIdProducto($fk_id_producto)
    {
        $this->fk_id_producto = $fk_id_producto;
    }

    public function getFecha()
    {
        return $this->fecha;
    }

    public function setFecha($fecha)
    {
        $this->fecha = $fecha;
    }

    public function getCantidad()
    {
        return $this->cantidad;
    }

    public function setCantidad($cantidad)
    {
        $this->cantidad = $cantidad;
    }
}