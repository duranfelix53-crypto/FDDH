<?php

class Usuario {

    private $nombre;

    public function __construct() {
        $this->nombre = "Felix";
    }

    public function obtenerNombre() {
        return $this->nombre;
    }
}