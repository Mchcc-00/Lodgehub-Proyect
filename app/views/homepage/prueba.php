<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cuadro con elementos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
    <style>
        .cuadro-principal {
            width: 300px;
            border: 2px solid #799ad8;
            padding: 10px;
            margin: 50px auto;
            border-radius: 10px;
        }

        .item {
            background-color: #e8f0fe;
            border-radius: 10px;
            padding: 10px;
            margin: 5px;
        }
    </style>
    <?php
        include "../layouts/nav.php";
    ?>
</head>


<body>

    <div class="d-flex justify-content-center gap-4">
        <div class="cuadro-principal text-center">
            <!-- Segundo cuadro -->
            <div class="row mt-1">
                <div class="col--4">
                    <div class="item">Abajo 1</div>
                </div>
                <div class="col--4">
                    <div class="item">Abajo 2</div>
                </div>
                <div class="col--4">
                    <div class="item">Abajo 3</div>
                </div>
            </div>
        </div>
        <div class="cuadro-principal text-center">
            <!-- Primer cuadro -->
            <div class="row">
                <div class="col-6">
                    <div class="item">Arriba 1</div>
                </div>
                <div class="col-6">
                    <div class="item">Arriba 2</div>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-4">
                    <div class="item">Abajo 1</div>
                </div>
                <div class="col-4">
                    <div class="item">Abajo 2</div>
                </div>
                <div class="col-4">
                    <div class="item">Abajo 3</div>
                </div>
            </div>
        </div>

    </div>

    <!-- fila de abajo -->


        <div class="d-flex justify-content-center gap-4">
        <div class="cuadro-principal text-center">
            <!-- Primer cuadro -->
            <div class="row">
                <div class="col-6">
                    <div class="item">Arriba 1</div>
                </div>
                <div class="col-6">
                    <div class="item">Arriba 2</div>
                </div>
            </div>

        </div>
        <div class="cuadro-principal text-center">
            <!-- Segundo cuadro -->
            <div class="row mt-1">
                <div class="col-4">
                    <div class="item">Abajo 1</div>
                </div>
                <div class="col-4">
                    <div class="item">Abajo 2</div>
                </div>
                <div class="col-4">
                    <div class="item">Abajo 3</div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q"
        crossorigin="anonymous"></script>
</body>

</html>