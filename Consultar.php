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
    <div>
        <h2>Lluvia por meses</h2>
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
    </div>
    <div>
        <h2>Cantidad de lluvia por mes</h2>
        <div style="width: 100%; margin: auto;">
            <canvas id="graficoLluvias"></canvas>
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
        $fechas_maximos_tabla = [];
        $fecha_dia_maximo = [
            "dias" => [],
            "cantidad" => [],
        ];

        while ($res = $result->fetch_assoc()) {
            $fechas_maximos_tabla[] = [
                "fecha" => $res['dia_lluvioso'],
                "cantidad" => $res['cantidad'],
            ];
            $fecha_dia_maximo["fecha"][] = $res['dia_lluvioso'];
            $fecha_dia_maximo["cantidad"][] = $res['cantidad'];
        }

        $datos_grafico_diario_json = json_encode($fecha_dia_maximo);

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
                foreach ($fechas_maximos_tabla as $fecha) {
                    echo "<tr>";
                    echo "<td>$fecha[fecha]</td>";
                    echo "<td>$fecha[cantidad]</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
    <div>
        <h2>Cantidad de lluvia por dia</h2>
        <div style="width: 100%; margin: auto;">
            <canvas id="graficoDias"></canvas>
        </div>
    </div>
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
                            color: 'white',
                        },
                    },
                    x: {
                        title: {
                            display: true,
                            text: "Fecha",
                            color: 'white',
                        },
                    },
                },
                plugins: {
                    legend: {
                        display: true,
                        color: 'white',
                    },
                    title: {
                        display: true,
                        text: "Registro Diario de Lluvias",
                        color: 'white',
                    },
                },
            },
        },
    });

    const dataDiaria = <?php echo $datos_grafico_diario_json ?>;

    const configDiaria = {
        type: "bar",
        data: {
            labels: dataDiaria.fecha,
            datasets: [{
                label: "Lluvia Máxima Diaria (mm)",
                data: dataDiaria.cantidad, // Usar las cantidades
                backgroundColor: "rgba(255, 99, 132, 0.6)",
                borderColor: "rgba(255, 99, 132, 1)",
                borderWidth: 1,
                color: 'white',
            }, ],
        },
        options: {
            indexAxis: 'y', // ¡Esto lo hace horizontal!
            responsive: true,
            categoryPercentage: 0.9, // Por defecto es 0.8. Reduce este valor (ej: 0.6)
            barPercentage: 0.7,
            scales: {
                y: {
                    // Los títulos de los ejes se invierten en el gráfico horizontal
                    title: {
                        display: true,
                        text: "Día",
                        color: 'white',
                    },
                    ticks: {
                        autoSkip: false,
                        color: 'white',
                    },
                },
                x: {
                    title: {
                        display: true,
                        text: "Cantidad (mm)",
                        color: 'white',
                    },
                    beginAtZero: true
                }
            }
        }
    };

    const diaLluvias = new Chart(document.getElementById('graficoDias'), configDiaria);
</script>