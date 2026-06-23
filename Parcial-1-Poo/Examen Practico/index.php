<?php

require_once "Admin.php";
require_once "Alumno.php";

$usuarios = [];

try {

    $admin = new Admin("Adriana Galvan", "admin@gmail.com");
    $usuarios[] = $admin;

    $alumno = new Alumno("Luis Ibarra", "luis@gmail.com", "A12345");
    $usuarios[] = $alumno;

    // usuario con correo invalido
    $alumnoInvalido = new Alumno("Marielos Barron", "maria@gmailcom", "A54321");
    $usuarios[] = $alumnoInvalido;

}catch(Exception $e){

    $error = $e->getMessage();

}

?>

<h2>Lista de Usuarios</h2>

<table border="1">
<tr>
<th>Nombre</th>
<th>Correo</th>
<th>Rol</th>
<th>Matricula</th>
</tr>

<?php

foreach($usuarios as $u){

echo "<tr>";

echo "<td>".$u->getNombre()."</td>";
echo "<td>".$u->getCorreo()."</td>";
echo "<td>".$u->getRol()."</td>";

if(method_exists($u,"getMatricula")){
echo "<td>".$u->getMatricula()."</td>";
}else{
echo "<td>-</td>";
}

echo "</tr>";

}

?>

</table>

<?php

if(isset($error)){
    echo "<p style='color:red;'>Error: $error</p>";
}

?>