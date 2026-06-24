<?php

    require_once("../../controllers/torneosController.php");
    $objTorneosController = new torneosController();
    //obtener el id desde el botón que mandará eliminar el registro.
    //lo obtendremos de la pantalla del listado general de torneos.
    $objTorneosController->delete($_GET['id']);

?>
