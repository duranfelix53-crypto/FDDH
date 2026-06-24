<?php

class Producto {
    private $id;
    private $nombre;
    private $descripcion;
    private $existencia;
    private $precio;

    public function __construct($id = null, $nombre = "", $descripcion = "", $existencia = 0, $precio = 0.00)
    {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->descripcion = $descripcion;
        $this->existencia = $existencia;
        $this->precio = $precio;
    }

// ID SETTERS AND GETTERS
    public function getId() {
        return $this->id;
    }
    public function setId($id) {
        $this->id = $id;
    }
// NOMBRE SETTERS AND GETTERS
    public function getNombre() {
        return $this->nombre;
    }
    public function setNombre($nombre) {
        $this->nombre = $nombre;
    }

// DESCRIPCION SETTERS AND GETTERS
    public function getDescripcion() {
        return $this->descripcion;
    }
    public function setDescripcion($descripcion) {
        $this->descripcion = $descripcion;
    }

// EXISTENCIA GETTERS AND SETTERS
    public function getExistencia() {
        return $this->existencia;
    }
    public function setExistencia($existencia) {
        $this->existencia = $existencia;
    }

// PRECIO GETTERS AND SETTERS
    public function getPrecio() {
        return $this->precio;
    }
    public function setPrecio ($precio) {
        $this->precio = $precio;
    }

}

?>