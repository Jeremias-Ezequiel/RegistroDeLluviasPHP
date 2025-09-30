<?php

// Obtenemos la fecha del dia de hoy
date_default_timezone_set('America/Argentina/Buenos_Aires');
$diaAnterior = date('Y-m-d', strtotime("-1 day"));
?>
<form id="formulario-registro" action="" method="post">
    <legend>Registrar lluvia del dia</legend>
    <div>
        <label for="fecha">Fecha:</label>
        <input type="date" min="2025-01-01" name="fecha" max="<?= $diaAnterior ?>" value="<?= $diaAnterior ?>" />
    </div>
    <div>
        <label for="cantidad">Cantidad de lluvia en mm:</label>
        <input type="number" min='0' max="500" name="cantidad" autofocus />
    </div>
    <button name="cancelar">Cancelar</button>
    <button name="registrar">Registrar</button>
</form>

<?php
// Realizar la conexion a la base de datos
if (isset($_POST['registrar'])) {
    require_once 'Database.php';

    $con = new Database("lluvias");
    $fechaRegistro = $_POST['fecha'];
    $cantidadRegistro = $_POST['cantidad'];

    // Verificar si existe un registro con la fecha seleccionada
    $result = $con->query("SELECT * FROM lluvias WHERE fecha_ID = $fechaRegistro;");

    if ($result) {
        echo "Paso aca";
        // $con->query("INSERT INTO lluvias VALUES ('$fechaRegistro','$cantidadRegistro');");
    } else {
        echo "Fallo al realizar la consultar";
    }
    // Insertar en la base de datos

}
?>