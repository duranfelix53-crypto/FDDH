<?php
$host = "localhost";
$db = "escuela";
$user = "root";
$pass = "";
$charset = "utf8mb4";


$dsn = "mysql:host=$host;dbname=$db;charset=$charset";


try{
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e){
    die("Error de conexión: " . $e->getMessage());
}


$mensaje = "";
$detalle = "";

if ($_SERVER ["REQUEST_METHOD"] === "POST") {
    //datos del formulario
    $nombre = trim($_POST["nombre"] ?? "");
    $apellido = trim($_POST["apellido"] ?? "");
    $correo = trim($_POST["correo"] ?? "");

    //simular error
    $simularError = isset($_POST["simular_error"]);

    //validacion minima (didactica)
    if($nombre === "" || $apellido === "" || $correo === ""){
        $mensaje = "TODOS LOS CAMPOS SON OBLIGATORIOS.";
    } else {
        try {
            //1)INICIAR TRANSACCIÓN
            $pdo->beginTransaction();

            //2)INSERTAR ALUMNO (CREATE)
            $sqlAlumno = "INSERT INTO alumnos (nombre, apellido, correo) VALUES (:nombre, :apellido, :correo)";
            $stmtAlumno = $pdo->prepare($sqlAlumno);
            $stmtAlumno->execute([
                "nombre" => $nombre,
                "apellido" => $apellido,
                "correo" => $correo
            ]);
            $idAlumno = (int) $pdo->lastInsertId();


            //)INSERTAR LOG (BITACORA)
            //SI SE MARCA SIMULAR ERROR. FORZAMOS UNA FALLA CONTROLADA
            if ($simularError){
                //forzamos rollback con una excepcion intencional
                throw new Exception("Simulación de error activada: se fuerza rollback.");
            } else {
                $sqlLog = "INSERT INTO logs_alumnos (idAlumno, accion) VALUES (:idAlumno, :accion)";
                $stmtLog = $pdo->prepare($sqlLog);
                $stmtLog->execute([
                    "idAlumno"=> $idAlumno, "accion" => "ALTA_ALUMNO"
                ]);
            }

            //4)CONFIRMAR TRANSACCION
            $pdo->commit();

            $mensaje = "TRANSACCION CONFRIMADA (COMMIT). ALUMNO REGISTRADO CON ID: $idAlumno";
        } catch (Exception $e){
            //si algo falla, revertir TODO
            if ($pdo->inTransaction()){
                $pdo->rollBack();
            }
            $mensaje = "OCURRIÓ UN ERROR. TRANSACCION REVERTIDA (ROLLBACK. )";
            $detalle = $e->getMessage(); //mostrar solo en entorno de clase/desarrollo
        }
    }
}

$alumnos = $pdo->query("SELECT * FROM alumnos ORDER BY idAlumno DESC")->fetchAll();
$logs = $pdo->query("SELECT * FROM logs_alumnos ORDER BY idLog DESC")->fetchAll();
?>



<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Practica PDO: try/catch y transacciones</title>
  <style>
    body{font-family:Arial, sans-serif; margin:20px; line-height:1.4}
    .card{border:1px solid #ddd; border-radius:10px; padding:16px; margin-bottom:16px}
    .row{display:flex; gap:12px; flex-wrap:wrap}
    label{display:block; font-weight:bold; margin-bottom:6px}
    input[type="text"], input[type="email"]{width:280px; padding:8px; border:1px solid #ccc; border-radius:6px}
    button{padding:10px 14px; border:0; border-radius:8px; cursor:pointer}
    .btn{background:#0b5ed7; color:white}
    .btn:hover{opacity:.9}
    .msg{padding:10px; border-radius:8px; background:#f5f5f5}
    .small{font-size:12px; color:#666}
    table{border-collapse:collapse; width:100%}
    th,td{border:1px solid #ddd; padding:8px; text-align:left}
    th{background:#f0f0f0}
    .danger{color:#b02a37}
    </style>
</head>
<body>

  <h2>Practica: try/catch y transacciones (PDO + MySQL)</h2>
  <div class="card">
    <form method="POST">
      <div class="row">
        <div>
          <label>Nombre</label>
          <input type="text" name="nombre" maxlength="15" value="<?= htmlspecialchars($_POST['nombre'] ?? 'José Alfonso') ?>">
        </div>
        <div>
          <label>Apellido</label>
          <input type="text" name="apellido" maxlength="10" value="<?= htmlspecialchars($_POST['apellido'] ?? 'Aguilar') ?>">
        </div>
        <div>
          <label>Correo</label>
          <input type="email" name="correo" maxlength="50" value="<?= htmlspecialchars($_POST['correo'] ?? 'ja.aguilar@uas.edu.mx') ?>">
        </div>
      </div>

     <p>
        <label style="font-weight:normal">
          <input type="checkbox" name="simular_error" <?= isset($_POST['simular_error']) ? 'checked' : '' ?>>
          Simular error para forzar ROLLBACK
        </label>
        <span class="small">(Activa para comprobar que no se guarda nada si falla un paso.)</span>
     </p>

     <button class="btn" type="submit">Registrar alumno</button>
    </form>
        <?php if ($mensaje): ?>
          <p class="msg"><?= htmlspecialchars($mensaje) ?></p>
          <?php if ($detalle): ?>
            <p class="small danger">Detalle (solo desarrollo): <?= htmlspecialchars($detalle) ?></p>
          <?php endif; ?>
        <?php endif; ?>
    </div>

    <div class="card">
      <h3>Tabla alumnos</h3>
      <?php if (!$alumnos): ?>
        <p class="small">Sin registros.</p>
      <?php else: ?>
        <table>
          <thead>
             <tr>
                  <th>ID</th><th>Nombre</th><th>Apellido</th><th>Correo</th>
             </tr>
          </thead>
          <tbody>
            <?php foreach ($alumnos as $a): ?>
               <tr>
                  <td><?= htmlspecialchars($a['idAlumno']) ?></td>
                  <td><?= htmlspecialchars($a['nombre']) ?></td>
                  <td><?= htmlspecialchars($a['apellido']) ?></td>
                  <td><?= htmlspecialchars($a['correo']) ?></td>
               </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
  </div>
  <div class="card">
    <h3>Tabla logs_alumnos</h3>
    <?php if (!$logs): ?>
        <p class="small">Sin registros.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>ID Log</th><th>ID Alumno</th><th>Acción</th><th>Fecha</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($logs as $l): ?>
                  <tr>
                      <td><?= htmlspecialchars($l['idLog']) ?></td>
                      <td><?= htmlspecialchars($l['idAlumno']) ?></td>
                      <td><?= htmlspecialchars($l['accion']) ?></td>
                      <td><?= htmlspecialchars($l['fecha']) ?></td>
                  </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
 </div>

 <p class="small">
   Prueba recomendada: 1) Registrar sin simular error (COMMIT). 2) Activar “Simular error” y registrar (ROLLBACK).
 </p>

</body>
</html>