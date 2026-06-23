<?php

require_once "Admin.php";

$admin = new Admin("Luis", "baryplox@gmail.com");

echo "Nombre: " . $admin->getNombre() . "<br>";
echo "Correo: " . $admin->getCorreo() . "<br>";
echo "Rol: " . $admin->getRol();

?>