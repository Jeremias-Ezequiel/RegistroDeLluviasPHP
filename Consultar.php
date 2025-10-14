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

$datos_grafico = [
    "fecha" => [],
    "cantidad" => [],
];

?>
<div class="consultar">
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
                $datos_grafico['fecha'][] = $nombre_meses[$res['mes']];
                $datos_grafico['cantidad'][] = $res['total_cantidad'];

                $clase_css = $lluvia == $res['total_cantidad'] ? " class='max_cant'" : "";
                echo "<tr>";
                echo "<td>" . $nombre_meses[$res['mes']] . "</td>";
                echo "<td" . $clase_css . ">" . $res['total_cantidad'] . "</td>";
                echo "</tr>";
            }
            $datos_grafico = json_encode($datos_grafico);
            ?>
        </tbody>
    </table>
    <div>
        <h2>Cantidad de lluvia por mes</h2>
        <div style="width: 100%; margin: auto;">
            <canvas id="graficoLluvias"></canvas>
        </div>
    </div>
</div>

<div>
    <h2>Dias más lluviosos</h2>
    <?php
    # Realizar la consultar
    $query = "SELECT
                DATE(l1.fecha_ID) AS dia_lluvioso,
                l1.cantidad
            FROM
                lluvias l1
            INNER JOIN (
                -- Subconsulta: Encuentra la MÁXIMA cantidad de lluvia por día para CADA mes
                SELECT
                    MONTH(fecha_ID) AS mes,
                    MAX(cantidad) AS maxima_lluvia_diaria
                FROM
                    lluvias
                GROUP BY
                    mes
            ) AS MaximosPorMes
            ON
                -- Condición 1: Une los registros por el mismo mes
                MONTH(l1.fecha_ID) = MaximosPorMes.mes 
                AND 
                -- Condición 2: Filtra solo aquellos registros cuya cantidad coincide con la máxima de su mes
                l1.cantidad = MaximosPorMes.maxima_lluvia_diaria
            ORDER BY
                dia_lluvioso;";

    $result = $con->query($query);
    $fechas_maximos = [];

    while ($res = $result->fetch_assoc()) {
        $fechas_maximos[] = [
            "fecha" => $res['dia_lluvioso'],
            "cantidad" => $res['cantidad']
        ];
    }

    ?>
    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Cantidad</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($fechas_maximos as $fecha) {
                echo "<tr>";
                echo "<td>$fecha[fecha]</td>";
                echo "<td>$fecha[cantidad]</td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<script>
    const ctx = document.getElementById("graficoLluvias").getContext("2d");
    const data = <?php echo $datos_grafico ?>

    new Chart(ctx, {
        type: "bar",
        data: {
            labels: data.fecha,
            datasets: [{
                label: "Cantidad de Lluvia (mm)",
                data: data.cantidad,
                backgroundColor: "rgba(75, 192, 192, 0.6)",
                borderColor: "rgba(75, 192, 192, 1)",
                borderWidth: 1,
            }, ],
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: "Cantidad (mm)",
                        },
                    },
                    x: {
                        title: {
                            display: true,
                            text: "Fecha",
                        },
                    },
                },
                plugins: {
                    legend: {
                        display: true,
                    },
                    title: {
                        display: true,
                        text: "Registro Diario de Lluvias",
                    },
                },
            },
        },
    });
</script>