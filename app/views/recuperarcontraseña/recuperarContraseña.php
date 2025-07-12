<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - LodgeHub</title>
    <link rel="stylesheet" href="../../../public/assets/css/loginStyles.css"> <!-- Enlaza el archivo CSS -->
</head>

<body>
    <div class="page-background">
        <div class="borde-container-login">
            <div class="login-container">

                <!-- Círculos dentro del contenedor -->
                <div class="circle circle-1"></div>
                <div class="circle circle-2"></div>
                <div class="circle circle-3"></div>
                <div class="circle circle-4"></div>

                <form class="recuperarcontraseña" method="post">

                    <h4>Ingresa tu correo electronico para enviarte un codigo de seguridad</h4> 

                    <div class="input-group">
                        <input type="text" id="correo" name="correo" placeholder="Ingresa tu correo electronico" required>
                    </div>
                    
                    <button type="submit" class="recuperarcontraseña-button">Recuperar contraseña</button>
                </form>

                <?php
                ?>


            </div>
            <div class ="degrade-container"></div>
            <div class="logo-container">
                <img src="../../../public/img/LogoClaroLH.png" alt="">
                <h6>lodgehubgroup © 2025</h6>
            </div>


            
        </div>
    </div>
</body>