<?php

class Prestamo
{
    private $id;
    private $libro_id;
    private $usuario_id;
    private $fecha_prestamo;
    private $fecha_devolucion;
    private $estado;

    public function __construct($id = null, $libro_id = null, $usuario_id = null, $fecha_prestamo = null, $fecha_devolucion = null, $estado = 'activo')
    {
        $this->id = $id;
        $this->libro_id = $libro_id;
        $this->usuario_id = $usuario_id;
        $this->fecha_prestamo = $fecha_prestamo ?? date('Y-m-d');
        $this->fecha_devolucion = $fecha_devolucion ?? date('Y-m-d');
        $this->estado = $estado;
    }

    // Getters y Setters
    public function getId()
    {
        return $this->id;
    }

    public function getLibroId()
    {
        return $this->libro_id;
    }

    public function getUsuarioId()
    {
        return $this->usuario_id;
    }

    public function getFechaPrestamo()
    {
        return $this->fecha_prestamo;
    }

    public function getFechaDevolucion()
    {
        return $this->fecha_devolucion;
    }

    public function setFechaDevolucion($fecha)
    {
        $this->fecha_devolucion = $fecha;
    }

    public function getEstado()
    {
        return $this->estado;
    }

    public function setEstado($estado)
    {
        $this->estado = $estado;
    }
}
