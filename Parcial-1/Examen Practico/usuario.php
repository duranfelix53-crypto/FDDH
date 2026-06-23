<?php

class Usuario {

    protected $nombre;
    protected $correo;

    public function __construct($nombre, $correo) {

        //validar el correo
        if(!filter_var($correo, FILTER_VALIDATE_EMAIL)){
            throw new Exception("el correo '$correo' no es valido");
        }

        $this->nombre = $nombre;
        $this->correo = $correo;
    }

    public function getNombre(){
        return $this->nombre;
    }

    public function getCorreo(){
        return $this->correo;
    }
}

?>