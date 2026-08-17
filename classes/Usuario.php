<?php

class Usuario
{
    private $id;
    private $nombre;
    private $email;
    private $telefono;

    // CONSTRUCTOR CORREGIDO: Pasamos el $id al final para que la creación e inserción sean intuitivas
    public function __construct($id = null, $nombre = null, $email = null, $telefono = null)
    {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->email = $email;
        $this->telefono = $telefono;
    }


    // Getters y Setters
    public function getId()
    {
        return $this->id;
    }

    public function getNombre()
    {
        return $this->nombre;
    }

    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getTelefono()
    {
        return $this->telefono;
    }

    public function setTelefono($telefono)
    {
        $this->telefono = $telefono;
    }
}
