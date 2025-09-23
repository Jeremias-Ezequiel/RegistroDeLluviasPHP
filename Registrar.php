<?php

// Obtenemos la fecha del dia de hoy
date_default_timezone_set('America/Argentina/Buenos_Aires');
$fecha = date('Y-m-d');

// Obtenemos la hora actual y establecemos el rango de hora en el cual se podrá completar el formulario

$horaActual = date('H');
$minutoActual = date('i');
$horaInicio = 12;
$horaFin = 24;
$minutoInicio = 0;
$formulario_permitido = $horaInicio == $horaActual && $minutoActual >= $minutoInicio;
?>

<?php if ($formulario_permitido):?> 
    <form id="formulario-registro" action="" method="post">
        <legend>Registrar lluvia del dia</legend>
        <div>
            <label for="fecha">Fecha:</label>
            <input type="date" max="<?=$fecha?>" value="<?=$fecha?>"/>
        </div>
        <div>
            <label for="cantidad">Cantidad de lluvia en mm:</label>
            <input type="number" min='0' max="500" autofocus/>
        </div>
            <button name="cancelar">Cancelar</button>
            <button name="registrar">Registrar</button>
    </form>

    <?php
        // Verificar si existe un registro con la fecha seleccionada
        
        // Insertar en la base de datos
    ?>

<?php else: ?>
    <h2>El formulario solo está disponible desde <?=$horaInicio?>:<?=$minutoInicio?>hs hasta <?=$horaFin?>hs</h2>
    <?php endif;?>