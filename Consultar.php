<form method="get">
    <input type="submit" name="mesLluvioso" value="Mes más lluvioso">
    <input type="submit" name="test" value="test">
</form>

<?php
if (isset($_GET['test'])) {
    echo "test";
}

if (isset($_GET['mesLluvioso'])) {
    require_once 'Database.php';

    $con = new Database("lluvias");

    $result = $con->query("SELECT 
                    MONTH(fecha_ID) AS mes,
                    SUM(cantidad) AS total_cantidad
                FROM lluvias
                GROUP BY MONTH(fecha_ID)
                HAVING SUM(cantidad) = (
                    SELECT MAX(suma_por_mes)
                    FROM (
                        SELECT SUM(cantidad) AS suma_por_mes
                        FROM lluvias
                        GROUP BY MONTH(fecha_ID)
                    ) AS sub
                )
                ORDER BY mes;");

    var_dump($result);

    if ($result) {
        if ($result->num_rows === 0) {
            // No existe la fecha entonces debemos consultar
            while ($mes = $result->fetch_assoc()) {
                echo $mes['mes'] . " con cantidad de: " . $mes['total_cantidad'] . "<br>";
            }
        } else {
            $repetido = $result->fetch_assoc();
            echo "<h3 class='error-msg'>No hay datos ingresados</h3>";
        }
    } else {
        echo "<h3 class='error-msg'>Fallo al realizar la consultar</h3>";
    }
}
