<?php
require_once "Usuario.php";
require_once "Admin.php";
require_once "Alumno.php";
require_once "Invitado.php";

$usuarios = [];

try {

    $usuarios[] = new Admin("Carlos", "carlos@empresa.com");

    $usuarios[] = new Alumno("Valeria", "valeria@uas.edu.mx", "202300123");

    $usuarios[] = new Invitado("Ana", "ana@empresa.com", "Google");

    // usuario inválido
    $usuarios[] = new Admin("Pedro", "correo_invalido");

} catch (Exception $e) {

    $error = $e->getMessage();
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Sistema de Usuarios</title>
</head>
<body>

<h2>Lista de Usuarios</h2>

<table border="1" cellpadding="8">

<tr>
<th>Nombre</th>
<th>Correo</th>
<th>Rol</th>
<th>Matrícula</th>
<th>Empresa</th>
</tr>

<?php foreach ($usuarios as $u): ?>

<tr>

<td><?php echo $u->getNombre(); ?></td>

<td><?php echo $u->getCorreo(); ?></td>

<td><?php echo $u->getRol(); ?></td>

<td>
<?php
if ($u instanceof Alumno) {
    echo $u->getMatricula();
} else {
    echo "—";
}
?>
</td>

<td>
<?php
if ($u instanceof Invitado) {
    echo $u->getEmpresa();
} else {
    echo "—";
}
?>
</td>

</tr>

<?php endforeach; ?>

</table>

<?php if(isset($error)): ?>

<p style="color:red;">
Error controlado: <?php echo $error; ?>
</p>

<?php endif; ?>

</body>
</html>