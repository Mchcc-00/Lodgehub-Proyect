<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LodgeHub Plaza Hotel - Información y Reseñas</title>

    <!-- Rutas corregidas para los CSS -->
    <link rel="stylesheet" href="../../public/assets/css/plazaHotel.css">
    <link rel="stylesheet" href="../../public/assets/css/stylesNavUsuario.css">
    <link rel="stylesheet" href="../../public/assets/css/stylesFooter.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

    <?php
        // --- INICIO: Cargar datos del hotel específico ---
        require_once __DIR__ . '/../models/hotelModel.php';

        $hotel = null;
        $hotelId = $_GET['id'] ?? null;

        if ($hotelId) {
            $hotelModel = new HotelModel();
            $hotelResult = $hotelModel->obtenerHotelPorId($hotelId);
            if ($hotelResult['success']) {
                $hotel = $hotelResult['data'];
            }
        }

        // Si no se encuentra el hotel, mostramos un mensaje y salimos
        if (!$hotel) {
            echo "<div class='container-principal' style='text-align:center; padding: 50px;'><h1>Hotel no encontrado</h1><p>El hotel que buscas no existe o no está disponible.</p><a href='../../index.php'>Volver al inicio</a></div>";
            exit;
        }

        // --- FIN: Cargar datos del hotel específico ---

        // Definir el título de la página actual para el navusuario
        $paginaActual = "Detalles de " . htmlspecialchars($hotel['nombre']);
        include "layouts/navusuario.php";
    ?>

<body>
    <div class="container-principal">
        <!-- HEADER CON IMAGEN -->
        <div class="header-imagen">
            <img class="imagen-hotel" 
                 src="<?php echo htmlspecialchars($hotel['foto'] ?? '../../public/assets/img/default_hotel_banner.png'); ?>" 
                 alt="<?php echo htmlspecialchars($hotel['nombre']); ?>"
                 onerror="this.onerror=null;this.src='../../public/assets/img/default_hotel_banner.png';" />
            <div class="overlay-gradiente"></div>
            <div class="info-superpuesta">
                <h1 class="titulo-hotel"><?php echo htmlspecialchars($hotel['nombre']); ?></h1>
                <div class="contenedor-calificacion">
                    <div class="estrellas-promedio">
                        <!-- La lógica de estrellas se puede implementar después -->
                        <svg class="estrella activa" viewBox="0 0 24 24">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>
                        <svg class="estrella activa" viewBox="0 0 24 24">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>
                        <svg class="estrella activa" viewBox="0 0 24 24">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>
                        <svg class="estrella activa" viewBox="0 0 24 24">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>
                        <svg class="estrella" viewBox="0 0 24 24">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>
                    </div>
                    <span class="numero-promedio">4.5</span>
                    <span class="cantidad-reseñas">(125 reseñas)</span>
                </div>
            </div>
        </div>

        <!-- CONTENIDO PRINCIPAL -->
        <div class="contenido-principal">

            <!-- GRID DE INFORMACIÓN -->
            <div class="grid-informacion">

                <!-- COLUMNA 1: DESCRIPCIÓN -->
                <div class="columna-descripcion">
                    <h2 class="titulo-seccion">Descripción</h2>
                    <p class="texto-descripcion"><?php echo htmlspecialchars($hotel['descripcion'] ?? 'No hay una descripción disponible para este hotel.'); ?></p>
                </div>

                <!-- COLUMNA 2: CONTACTO -->
                <div class="columna-contacto">
                    <h2 class="titulo-seccion">Información de Contacto</h2>
                    <div class="lista-contactos">

                        <div class="item-contacto">
                            <svg class="icono-contacto" viewBox="0 0 24 24">
                                <path d="M3 21h18v-2H3v2zm0-4h18v-2H3v2zm0-4h18v-2H3v2zm0-4h18V7H3v2zm0-6v2h18V3H3z"/>
                            </svg>
                            <div class="info-contacto">
                                <span class="etiqueta-contacto">NIT</span>
                                <span class="valor-contacto"><?php echo htmlspecialchars($hotel['nit']); ?></span>
                            </div>
                        </div>

                        <div class="item-contacto">
                            <svg class="icono-contacto" viewBox="0 0 24 24">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6z"/>
                                <polyline points="14,2 14,8 20,8"/>
                            </svg>
                            <div class="info-contacto">
                                <span class="etiqueta-contacto">Admin. Doc</span>
                                <span class="valor-contacto"><?php echo htmlspecialchars($hotel['numDocumentoAdmin']); ?></span>
                            </div>
                        </div>

                        <div class="item-contacto">
                            <svg class="icono-contacto" viewBox="0 0 24 24">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                                <circle cx="12" cy="10" r="3"/>
                            </svg>
                            <div class="info-contacto">
                                <span class="etiqueta-contacto">Dirección</span>
                                <span class="valor-contacto"><?php echo htmlspecialchars($hotel['direccion'] ?? 'No disponible'); ?></span>
                            </div>
                        </div>

                        <div class="item-contacto">
                            <svg class="icono-contacto" viewBox="0 0 24 24">
                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                            </svg>
                            <div class="info-contacto">
                                <span class="etiqueta-contacto">Teléfono</span>
                                <span class="valor-contacto"><?php echo htmlspecialchars($hotel['telefono'] ?? 'No disponible'); ?></span>
                            </div>
                        </div>

                        <div class="item-contacto">
                            <svg class="icono-contacto" viewBox="0 0 24 24">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                                <polyline points="22,6 12,13 2,6"/>
                            </svg>
                            <div class="info-contacto">
                                <span class="etiqueta-contacto">Correo</span>
                                <span class="valor-contacto"><?php echo htmlspecialchars($hotel['correo'] ?? 'No disponible'); ?></span>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- SISTEMA DE CALIFICACIONES -->
            <div class="seccion-calificaciones">
                <h2 class="titulo-seccion">Calificaciones y Reseñas</h2>

                <!-- FORMULARIO NUEVA RESEÑA -->
                <div class="formulario-reseña">
                    <h3 class="titulo-formulario">Agregar tu reseña</h3>

                    <div class="campo-calificacion">
                        <div class="etiqueta-campo">Calificación</div>
                        <div class="selector-estrellas">
                            <svg class="estrella-interactiva" viewBox="0 0 24 24" onclick="setRating(1)">
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                            </svg>
                            <svg class="estrella-interactiva" viewBox="0 0 24 24" onclick="setRating(2)">
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                            </svg>
                            <svg class="estrella-interactiva" viewBox="0 0 24 24" onclick="setRating(3)">
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                            </svg>
                            <svg class="estrella-interactiva" viewBox="0 0 24 24" onclick="setRating(4)">
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                            </svg>
                            <svg class="estrella-interactiva" viewBox="0 0 24 24" onclick="setRating(5)">
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                            </svg>
                        </div>
                    </div>

                    <div class="campo-descripcion">
                        <div class="etiqueta-campo">Descripción</div>
                        <textarea class="textarea-reseña" rows="4" placeholder="Comparte tu experiencia en el hotel..."></textarea>
                    </div>

                    <button class="boton-enviar" onclick="enviarReseña()">Enviar Reseña</button>
                </div>

                <!-- LISTA DE RESEÑAS -->
                <div class="lista-reseñas">
                    <h3 class="titulo-lista">Reseñas de huéspedes</h3>

                    <div class="tarjeta-reseña">
                        <div class="header-reseña">
                            <div class="info-autor">
                                <div class="estrellas-reseña">
                                    <svg class="estrella" viewBox="0 0 24 24">
                                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                    </svg>
                                    <svg class="estrella" viewBox="0 0 24 24">
                                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                    </svg>
                                    <svg class="estrella" viewBox="0 0 24 24">
                                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                    </svg>
                                    <svg class="estrella" viewBox="0 0 24 24">
                                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                    </svg>
                                    <svg class="estrella" viewBox="0 0 24 24">
                                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                    </svg>
                                </div>
                                <span class="nombre-autor">María González</span>
                            </div>
                            <span class="fecha-reseña">2025-08-05</span>
                        </div>
                        <p class="texto-reseña">Excelente servicio y instalaciones. Muy recomendado para viajes de negocios. El personal es muy atento y las habitaciones están impecables. La ubicación es perfecta para acceder a todo lo que necesitas en la ciudad.</p>
                    </div>

                    <div class="tarjeta-reseña">
                        <div class="header-reseña">
                            <div class="info-autor">
                                <div class="estrellas-reseña">
                                    <svg class="estrella" viewBox="0 0 24 24">
                                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                    </svg>
                                    <svg class="estrella" viewBox="0 0 24 24">
                                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                    </svg>
                                    <svg class="estrella" viewBox="0 0 24 24">
                                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                    </svg>
                                    <svg class="estrella" viewBox="0 0 24 24">
                                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                    </svg>
                                    <svg class="estrella" style="fill: #d1d5db;" viewBox="0 0 24 24">
                                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                    </svg>
                                </div>
                                <span class="nombre-autor">Carlos Rodríguez</span>
                            </div>
                            <span class="fecha-reseña">2025-08-03</span>
                        </div>
                        <p class="texto-reseña">Buena ubicación y habitaciones cómodas. El desayuno podría mejorar un poco, pero en general es una muy buena opción para hospedarse en Bogotá. El gimnasio y el spa son excelentes.</p>
                    </div>

                    <div class="tarjeta-reseña">
                        <div class="header-reseña">
                            <div class="info-autor">
                                <div class="estrellas-reseña">
                                    <svg class="estrella" viewBox="0 0 24 24">
                                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                    </svg>
                                    <svg class="estrella" viewBox="0 0 24 24">
                                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                    </svg>
                                    <svg class="estrella" viewBox="0 0 24 24">
                                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                    </svg>
                                    <svg class="estrella" viewBox="0 0 24 24">
                                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                    </svg>
                                    <svg class="estrella" viewBox="0 0 24 24">
                                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                    </svg>
                                </div>
                                <span class="nombre-autor">Ana Martínez</span>
                            </div>
                            <span class="fecha-reseña">2025-08-01</span>
                        </div>
                        <p class="texto-reseña">Perfecto para una escapada romántica. Las habitaciones son elegantes y el servicio al cliente es excepcional. Definitivamente volveremos.</p>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script>
        let currentRating = 0;

        function setRating(rating) {
            currentRating = rating;
            const stars = document.querySelectorAll('.estrella-interactiva');
            
            stars.forEach((star, index) => {
                if (index < rating) {
                    star.style.fill = '#fbbf24';
                    star.style.filter = 'drop-shadow(0 0 8px rgba(251, 191, 36, 0.4))';
                    star.style.transform = 'scale(1.1)';
                } else {
                    star.style.fill = '#d1d5db';
                    star.style.filter = 'none';
                    star.style.transform = 'scale(1)';
                }
            });
        }

        function enviarReseña() {
            const textarea = document.querySelector('.textarea-reseña');
            const reviewText = textarea.value.trim();
            
            if (currentRating === 0) {
                alert('Por favor, selecciona una calificación');
                return;
            }
            
            if (reviewText === '') {
                alert('Por favor, escribe tu reseña');
                return;
            }

            // Crear nueva reseña
            const nuevaReseña = document.createElement('div');
            nuevaReseña.className = 'tarjeta-reseña';
            nuevaReseña.style.animation = 'fadeInUp 0.8s ease-out';
            
            const today = new Date().toISOString().split('T')[0];
            
            nuevaReseña.innerHTML = `
                <div class="header-reseña">
                    <div class="info-autor">
                        <div class="estrellas-reseña">
                            ${generateStarsHTML(currentRating)}
                        </div>
                        <span class="nombre-autor">Usuario Anónimo</span>
                    </div>
                    <span class="fecha-reseña">${today}</span>
                </div>
                <p class="texto-reseña">${reviewText}</p>
            `;

            // Insertar después del título
            const listaReseñas = document.querySelector('.lista-reseñas');
            const tituloLista = document.querySelector('.titulo-lista');
            listaReseñas.insertBefore(nuevaReseña, tituloLista.nextSibling);

            // Limpiar formulario
            textarea.value = '';
            currentRating = 0;
            setRating(0);

            // Mostrar mensaje de éxito
            const boton = document.querySelector('.boton-enviar');
            const textoOriginal = boton.textContent;
            boton.textContent = '¡Reseña enviada!';
            boton.style.background = 'linear-gradient(135deg, #10b981 0%, #059669 100%)';
            
            setTimeout(() => {
                boton.textContent = textoOriginal;
                boton.style.background = 'linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%)';
            }, 2000);
        }

        function generateStarsHTML(rating) {
            let starsHTML = '';
            for (let i = 1; i <= 5; i++) {
                starsHTML += `
                    <svg class="estrella" viewBox="0 0 24 24" ${i > rating ? 'style="fill: #d1d5db;"' : ''}>
                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                    </svg>
                `;
            }
            return starsHTML;
        }

        // Agregar efecto hover a las estrellas interactivas
        document.querySelectorAll('.estrella-interactiva').forEach((star, index) => {
            star.addEventListener('mouseenter', () => {
                if (currentRating === 0) {
                    const stars = document.querySelectorAll('.estrella-interactiva');
                    stars.forEach((s, i) => {
                        if (i <= index) {
                            s.style.fill = '#fbbf24';
                            s.style.filter = 'drop-shadow(0 0 8px rgba(251, 191, 36, 0.4))';
                            s.style.transform = 'scale(1.05)';
                        } else {
                            s.style.fill = '#d1d5db';
                            s.style.filter = 'none';
                            s.style.transform = 'scale(1)';
                        }
                    });
                }
            });

            star.addEventListener('mouseleave', () => {
                if (currentRating === 0) {
                    setRating(0);
                }
            });
        });

        // Animación de aparición escalonada para las reseñas existentes
        document.addEventListener('DOMContentLoaded', () => {
            const reseñas = document.querySelectorAll('.tarjeta-reseña');
            reseñas.forEach((reseña, index) => {
                reseña.style.animationDelay = `${0.6 + (index * 0.1)}s`;
            });
        });
    </script>

    <?php
        // Incluir el footer con la ruta correcta desde esta ubicación
        include __DIR__ . '/layouts/footer.php';
    ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>