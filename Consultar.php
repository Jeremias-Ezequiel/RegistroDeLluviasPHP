<?php
# Realizar la consulta a la base de datos para obtener los meses y cantidades
$query = "SELECT 
                MONTH(fecha_ID) AS mes,
                SUM(cantidad) AS total_cantidad
            FROM lluvias
            GROUP BY MONTH(fecha_ID)
            ORDER BY mes;
    ";

# Obtener la conexión
require_once 'Database.php';

$con = new Database("lluvias");
$result = $con->query($query);

if (!$result->num_rows) {
    die("No hay resultados");
}

$nombre_meses = [
    1 => "Enero",
    2 => "Febrero",
    3 => "Marzo",
    4 => "Abril",
    5 => "Mayo",
    6 => "Junio",
    7 => "Julio",
    8 => "Agosto",
    9 => "Septiembre",
    10 => "Octubre",
    11 => "Noviembre",
    12 => "Diciembre",
];

$max_lluvia_query = "SELECT
                        MAX(total_lluvia) AS max_lluvia
                    FROM (
                        -- Subconsulta: Calcula la suma de lluvia para CADA mes
                        SELECT
                            SUM(cantidad) AS total_lluvia
                        FROM
                            lluvias
                        GROUP BY
                            MONTH(fecha_ID)
                    ) AS LluviasMensual;";

$lluvia = $con->query($max_lluvia_query)->fetch_assoc()["max_lluvia"];

?>
<table>
    <thead>
        <tr>
            <th>Mes</th>
            <th>Cantidad</th>
        </tr>
    </thead>
    <tbody>
        <?php
        while ($res = $result->fetch_assoc()) {
            $clase_css = $lluvia == $res['total_cantidad'] ? " class='max_cant'" : "";
            echo "<tr>";
            echo "<td>" . $nombre_meses[$res['mes']] . "</td>";
            echo "<td" . $clase_css . ">" . $res['total_cantidad'] . "</td>";
            echo "</tr>";
        }
        ?>
    </tbody>
</table>