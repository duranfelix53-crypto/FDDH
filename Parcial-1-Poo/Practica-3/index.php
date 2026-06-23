<?php

require_once "clases/Admin.php";
require_once "clases/Alumno.php";

try {

    $admin = new Admin("Luis", "baryplox@gmail.com");

    echo "Nombre: " . $admin->getNombre() . "<br>";
    echo "Correo: " . $admin->getCorreo() . "<br>";
    echo "Rol: " . $admin->getRol() . "<br><br>";

    $alumno = new Alumno("Carlos", "correo_invalido", "A12345");

    echo "Nombre: " . $alumno->getNombre();

} catch (Exception $e){

    echo "Error: " . $e->getMessage();

}

?>