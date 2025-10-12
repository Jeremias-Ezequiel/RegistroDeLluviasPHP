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
        <input type="number" min='0' max="400" name="cantidad" autofocus />
    </div>
    <button name="registrar">Registrar</button>
    <button name="cancelar">Cancelar</button>
</form>

<?php
// Realizar la conexion a la base de datos
if (isset($_POST['cancelar'])) {
    header("Location: ./index.php");
}

if (isset($_POST['registrar'])) {
    require_once 'Database.php';

    $con = new Database("lluvias");
    $fechaRegistro = $_POST['fecha'];
    $cantidadRegistro = $_POST['cantidad'];

    if (!$cantidadRegistro || !$fechaRegistro) {
        echo "<h3 class='error-msg'>El campo date y/o cantidad no pueden estar vacio</h3>";
        return;
    }

    // Verificar si existe un registro con la fecha seleccionada
    $result = $con->query("SELECT * FROM lluvias WHERE fecha_ID = '$fechaRegistro';");

    if ($result) {
        if ($result->num_rows === 0) {
            // No existe la fecha entonces debemos consultar
            $result = $con->query("INSERT INTO lluvias VALUES ('$fechaRegistro',$cantidadRegistro);");
            if ($result) {
                echo "<h3 class='exito-msg'>Fecha $fechaRegistro: Cantidad $cantidadRegistro agregada con exito!";
            }
        } else {
            $repetido = $result->fetch_assoc();
            echo "<h3 class='error-msg'>La fecha ya ha sido ingresada con un valor de " . $repetido['cantidad'] . "</h3>";
            echo "<h2>¿Desea cambiar el valor de " . $repetido['cantidad'] . " a " . $cantidadRegistro . "?</h2>";
?>
            <form method="post" class="form-actualizar">
                <button type="submit">No</button>
                <button type="submit" name="cambiarValor">Si</button>
                <input type="hidden" name="fechaRegistro" value="<?= $fechaRegistro ?>">
                <input type="hidden" name="cantidadRegistro" value="<?= $cantidadRegistro ?>">
            </form>
<?php
        }
    } else {
        echo "<h3 class='error-msg'>Fallo al realizar la consultar</h3>";
    }
}

if (isset($_POST['cambiarValor'])) {
    require_once 'Database.php';

    $db = new Database("lluvias");
    $con = $db->getConnection();

    $fechaRegistro = $_POST['fechaRegistro'];
    $cantidadRegistro = $_POST['cantidadRegistro'];

    $query = "UPDATE lluvias SET cantidad = $cantidadRegistro WHERE fecha_ID = '$fechaRegistro';";
    if ($con->query($query)) {

        if ($con->affected_rows) {
            echo "<p class='exito-msg'>Se ha actualizado correctamente la fecha " . $fechaRegistro . " con el valor " . $cantidadRegistro . "</p>";
        } else if ($con->affected_rows === 0) {
            echo "<p class='error-msg'>El valor que se intenta actualizar es el mismo</p>";
        } else {
            echo "<p class='error-msg'>Ha ocurrido un error</p>";
        }
    }
}
?>