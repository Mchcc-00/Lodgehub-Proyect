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

                <form class="../login/validar.php" method="post">

                    <h1>¡BIENVENIDO A LODGEHUB!</h1> <!-- Título -->

                    <div class="input-group">
                        <label for="username">Usuario</label>
                        <input type="text" id="usuario" name="correo" placeholder="Ingresa tu usuario" required>
                    </div>

                    <div class="input-group">
                        <label for="password">Contraseña</label>
                        <input type="password" id="password" name="password" placeholder="Ingresa tu contraseña" required>
                    </div>

                    <a href="#" class="account-link">¿Olvidaste tu contraseña?</a>
                    <a href=" ../Usuarios/crear.php" class="account-link">¿No tienes una cuenta? ¡Crea una!</a>

                    <button type="submit" class="login-button">Ingresar</button>
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

</html>


