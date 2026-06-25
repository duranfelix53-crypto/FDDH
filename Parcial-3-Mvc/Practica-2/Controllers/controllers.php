<?php

require_once "models/Usuario.php";

class UsuarioController {

    public function mostrarUsuario() {

        $usuario = new Usuario();

        $nombre = $usuario->obtenerNombre();

        require_once "views/usuario.php";
    }
}