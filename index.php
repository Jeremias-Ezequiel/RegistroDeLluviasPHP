<?php


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Lluvias</title>
    <link rel="stylesheet" href="app.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <header>
        <h1>Registro de lluvias</h1>
    </header>
    <main>
        <nav id="navegacion">
            <form method="get">
                <ul>
                    <li><a href="?seccion=registrar">Registrar</a></li>
                    <li><a href="?seccion=consultar">Consultar</a></li>
                </ul>
            </form>
        </nav>
        <div id="seccion">
            <?php
            if (isset($_GET['seccion'])) {
                switch ($_GET['seccion']) {
                    case 'registrar':
                        include_once 'Registrar.php';
                        break;
                    case 'consultar':
                        include_once 'Consultar.php';
                        break;
                    default:
                        die("Recurso no disponible");
                        break;
                }
            }
            ?>
        </div>
    </main>
    <script src="grafico.js"></script>
</body>

</html>