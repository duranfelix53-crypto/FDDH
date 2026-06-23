<?php

require_once "Usuario.php";

$usuario = new Usuario("Luis", "baryplox@gmail.com");

echo "Nombre: " . $usuario->getNombre();
echo "<br>";
echo "Correo: " . $usuario->getCorreo();

?>