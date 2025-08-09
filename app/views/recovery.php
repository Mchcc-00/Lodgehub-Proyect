<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require '../../PHPMailer/Exception.php';
require '../../PHPMailer/PHPMailer.php';
require '../../PHPMailer/SMTP.php';



require_once ('../../config/conexionGlobal.php');
$db = conexionDB();

$correo = $_POST['correo'];
session_start();
$_SESSION['correo'] = $correo;

$conexion = mysqli_connect("localhost","root","","lodgehub");

$consulta = "SELECT * FROM tp_usuarios where correo = '$correo' and sesionCaducada = 1";
$resultado = mysqli_query($conexion, $consulta);
$row = mysqli_fetch_assoc($resultado);

$filas = mysqli_num_rows($resultado);

if($filas > 0){
    
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'lodgehub3@gmail.com';
        $mail->Password   = 'myid grtr grcu fjab';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Permitir certificados autofirmados (solo para pruebas locales)
        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true,
            ]
        ];

        $mail->setFrom('lodgehub3@gmail.com', 'LODGEHUB');
        $mail->addAddress($correo);

        // Ya no necesitamos embebir la imagen, usaremos un icono SVG

        $mail->isHTML(true);
        $mail->Subject = 'Recuperacion de Contrasena - LodgeHub';
        
        // Obtener el ID del usuario
        $numDocumento = isset($row['numDocumento']) ? $row['numDocumento'] : '';
        
        // HTML Template con imagen corregida
        $htmlBody = '
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Recuperación de Contraseña - LodgeHub</title>
                                <style>
                * {
                    margin: 0;
                    padding: 0;
                    box-sizing: border-box;
                }
                
                body {
                    font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    min-height: 100vh;
                    margin: 0;
                    padding: 20px;
                    width: 100%;
                }
                
                .email-container {
                    background: #ffffff;
                    max-width: 600px;
                    width: 100%;
                    margin: 0 auto;
                    border-radius: 20px;
                    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
                    overflow: hidden;
                    animation: slideIn 0.8s ease-out;
                }
                
                @keyframes slideIn {
                    from {
                        opacity: 0;
                        transform: translateY(30px);
                    }
                    to {
                        opacity: 1;
                        transform: translateY(0);
                    }
                }
                
                .header {
                    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
                    padding: 40px 30px;
                    text-align: center;
                    position: relative;
                    overflow: hidden;
                }
                
                .header::before {
                    content: "";
                    position: absolute;
                    top: -50%;
                    left: -50%;
                    width: 200%;
                    height: 200%;
                    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
                    animation: shimmer 3s ease-in-out infinite;
                }
                
                @keyframes shimmer {
                    0%, 100% { transform: translate(-50%, -50%) rotate(0deg); }
                    50% { transform: translate(-50%, -50%) rotate(180deg); }
                }
                
                .brand-name {
                    color: white;
                    font-size: 42px;
                    font-weight: 800;
                    margin-bottom: 10px;
                    position: relative;
                    z-index: 1;
                    letter-spacing: 2px;
                    text-shadow: 0 2px 4px rgba(0,0,0,0.2);
                }
                
                .header-subtitle {
                    color: rgba(255, 255, 255, 0.9);
                    font-size: 16px;
                    position: relative;
                    z-index: 1;
                }
                
                .content {
                    padding: 50px 40px;
                    text-align: center;
                }
                
                .title {
                    color: #2c3e50;
                    font-size: 32px;
                    font-weight: 700;
                    margin-bottom: 20px;
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    -webkit-background-clip: text;
                    -webkit-text-fill-color: transparent;
                    background-clip: text;
                }
                
                .message {
                    color: #5a6c7d;
                    font-size: 18px;
                    line-height: 1.6;
                    margin-bottom: 40px;
                    max-width: 400px;
                    margin-left: auto;
                    margin-right: auto;
                }
                
                .cta-button {
                    color: #ffffff !important;
                    display: inline-block;
                    background: linear-gradient(135deg, #286aa779 0%, #205bc9ff 100%);
                    text-decoration: none;
                    padding: 18px 40px;
                    border-radius: 50px;
                    font-size: 18px;
                    font-weight: 600;
                    transition: all 0.3s ease;
                    box-shadow: 0 10px 30px rgba(40, 49, 167, 0.3);
                    position: relative;
                    overflow: hidden;
                    border: none;
                }
                
                .cta-button::before {
                    content: "";
                    position: absolute;
                    top: 0;
                    left: -100%;
                    width: 100%;
                    height: 100%;
                    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
                    transition: left 0.5s ease;
                }
                
                .cta-button:hover::before {
                    left: 100%;
                }
                
                .cta-button:hover {
                    transform: translateY(-2px);
                    box-shadow: 0 15px 35px rgba(40, 167, 69, 0.4);
                }
                
                .security-info {
                    background: linear-gradient(135deg, #f8f9ff 0%, #e8f2ff 100%);
                    padding: 30px;
                    border-radius: 15px;
                    margin-top: 40px;
                    border: 1px solid rgba(102, 126, 234, 0.1);
                }
                
                .security-title {
                    color: #4a5568;
                    font-size: 16px;
                    font-weight: 600;
                    margin-bottom: 15px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    gap: 10px;
                }
                
                .security-text {
                    color: #718096;
                    font-size: 14px;
                    line-height: 1.5;
                }
                
                .footer {
                    background: #f8fafc;
                    padding: 30px;
                    text-align: center;
                    border-top: 1px solid #e2e8f0;
                }
                
                .footer-text {
                    color: #a0aec0;
                    font-size: 14px;
                    line-height: 1.5;
                }
                
                .footer-link {
                    color: #667eea;
                    text-decoration: none;
                }
                
                .footer-link:hover {
                    text-decoration: underline;
                }
                
                @media (max-width: 600px) {
                    .email-container {
                        margin: 10px;
                        border-radius: 15px;
                    }
                    
                    .content {
                        padding: 30px 20px;
                    }
                    
                    .title {
                        font-size: 28px;
                    }
                    
                    .message {
                        font-size: 16px;
                    }
                    
                    .cta-button {
                        padding: 16px 30px;
                        font-size: 16px;
                    }
                }
            </style>
        </head>
        <body>
            <div class="email-container">
                <!-- Header -->
                <div class="header">
                    <div class="logo">';
        
        // Usar icono de candado mejorado
        $htmlBody .= '<svg viewBox="0 0 24 24" fill="white" width="50" height="50">
                        <path d="M6 10h1V7c0-2.76 2.24-5 5-5s5 2.24 5 5v3h1c1.1 0 2 .9 2 2v8c0 1.1-.9 2-2 2H6c-1.1 0-2-.9-2-2v-8c0-1.1.9-2 2-2zm3-3c0-1.66 1.34-3 3-3s3 1.34 3 3v3H9V7zm3 9c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2z"/>
                      </svg>';
        
        $htmlBody .= '</div>
                    <div class="brand-name">LodgeHub</div>
                    <div class="header-subtitle">Sistema de Gestión Hotelera</div>
                </div>
                
                <!-- Content -->
                <div class="content">
                    <h1 class="title">Recuperación de Contraseña</h1>
                    
                    <p class="message">
                        ¡Hola! Recibimos una solicitud para restablecer tu contraseña. 
                        Haz clic en el botón de abajo para continuar con el proceso de recuperación.
                    </p>
                    
                    <a href="http://localhost/lodgehub/app/views/recuperarcontraseña/Contraseña.php?id='.$numDocumento.'" class="cta-button">
                        Recuperar Contraseña
                    </a>
                    
                    <div class="security-info">
                        <div class="security-title">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 2L2 7v10c0 5.55 3.84 9.739 9 11 5.16-1.261 9-5.45 9-11V7l-10-5z"/>
                            </svg>
                            Información de Seguridad
                        </div>
                        <div class="security-text">
                            Este enlace es válido por 24 horas. Si no solicitaste este cambio, 
                            puedes ignorar este correo de forma segura. Tu contraseña actual 
                            permanecerá sin cambios.
                        </div>
                    </div>
                </div>
                
                <!-- Footer -->
                <div class="footer">
                    <div class="footer-text">
                        Este es un mensaje automático de <strong>LodgeHub</strong><br>
                        Si tienes problemas, contacta a nuestro 
                        <a href="mailto:soporte@lodgehub.com" class="footer-link">equipo de soporte</a>
                    </div>
                </div>
            </div>
        </body>

        </html>'
        ;

        $mail->Body = $htmlBody;
        $mail->AltBody = 'Hola, este es un mensaje de recuperación de contraseña. Por favor visita: http://localhost/lodgehub/app/views/Contraseña.php?id='.$numDocumento;

        $mail->send();
        header("location: login.php?mensaje=Correo enviado correctamente");
    } catch (Exception $e) {
        header("location: login.php?mensaje=Error al enviar el correo: {$mail->ErrorInfo}");
    }

} else {
    header("location: login.php?mensaje=El correo no existe o no es válido");
}

mysqli_free_result($resultado);
mysqli_close($conexion);

?>