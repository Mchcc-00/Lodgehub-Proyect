<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LodgeHub - Encuentra tu Hotel Perfecto</title>
    <link rel="stylesheet" href="public/assets/css/stylesNavUsuario.css">
    <link rel="stylesheet" href="public/assets/css/stylesFooter.css">
    <link rel="stylesheet" href="public/assets/css/stylesHomeUsuario.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

</head>

    <?php
        $paginaActual = "Página Principal";
        // --- INICIO: Cargar datos de hoteles desde la BD ---
        require_once '/app/models/hotelModel.php';
        
        $hotelModel = new HotelModel();
        $hotelesResult = $hotelModel->obtenerHoteles();
        
        $hoteles_data = [];
        if ($hotelesResult['success']) {
            $hoteles_data = $hotelesResult['data'];
        }
        // --- FIN: Cargar datos de hoteles desde la BD ---
        include "/app/views/layouts/navusuario.php";
    ?>

<body>
    <!-- Aquí iría tu navUsuario.php -->
    
    <div class="container">
        <div class="header">
            <div class="img">
            <img src="/public/img/LogoClaroLHSinTitulo.png" alt="LODGEHUB">
            <h1>LodgeHub</h1>
            </div>
            <p>¡Descubre los mejores hoteles para tu próxima estadía!</p>
            
            <div class="search-container">
                <input 
                    type="text" 
                    class="search-input" 
                    id="searchInput"
                    placeholder="🔍 Buscar hoteles por nombre..."
                >
            </div>
        </div>

        <div class="hotels-grid" id="hotelsGrid">
            <!-- Los hoteles se generarán dinámicamente aquí -->
        </div>

        <div class="no-results hidden" id="noResults">
            No se encontraron hoteles que coincidan con tu búsqueda
        </div>
    </div>

    <script>
        // Usamos los datos de hoteles cargados desde PHP
        const todosLosHoteles = <?php echo json_encode($hoteles_data); ?>;

        function crearTarjetaHotel(hotel) {
            return `
                <div class="hotel-card" data-hotel-id="${hotel.id}">
                    <img src="${hotel.foto || '/public/assets/uploads/hoteles/hotel1.png'}" 
                         alt="${hotel.nombre}" 
                         class="hotel-image"
                         onerror="this.onerror=null;this.src='/public/assets/uploads/hoteles/hotel1.png';">
                    <div class="hotel-info">
                        <h3 class="hotel-name">${hotel.nombre}</h3>
                        <div class="hotel-address">
                            📍 ${hotel.direccion}
                        </div>
                        <div class="hotel-contact">
                            <div class="contact-item">
                                📞 ${hotel.telefono}
                            </div>
                            <div class="contact-item">
                                ✉️ ${hotel.correo}
                            </div>
                            <div class="contact-item" style="font-size: 0.8rem; color: #999; margin-top: 0.5rem;">
                                NIT: ${hotel.nit}
                            </div>
                        </div>
                        <a href="/app/views/plazaHotel.php?id=${hotel.id}" class="view-info-btn">
                            Ver Información Completa
                        </a>
                    </div>
                </div>
            `;
        }

        function mostrarHoteles(hoteles) {
            const grid = document.getElementById('hotelsGrid');
            const noResults = document.getElementById('noResults');
            
            if (hoteles.length === 0) {
                grid.innerHTML = '';
                noResults.classList.remove('hidden');
            } else {
                noResults.classList.add('hidden');
                grid.innerHTML = hoteles.map(crearTarjetaHotel).join('');
            }
        }

        function filtrarHoteles(termino) {
            const terminoLower = termino.toLowerCase().trim();
            
            if (terminoLower === '') {
                return todosLosHoteles;
            }
            
            return todosLosHoteles.filter(hotel => 
                hotel.nombre.toLowerCase().includes(terminoLower)
            );
        }

        // Event listener para la búsqueda
        document.getElementById('searchInput').addEventListener('input', function(e) {
            const termino = e.target.value;
            const hotelesEncontrados = filtrarHoteles(termino);
            mostrarHoteles(hotelesEncontrados);
        });

        // Mostrar todos los hoteles al cargar la página
        mostrarHoteles(todosLosHoteles);

        // Animación suave al hacer scroll
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            searchInput.focus();
        });
    </script>
    <?php
        // Incluir el footer
        include __DIR__ . '/app/views/layouts/footer.php';
    ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

        <!--nav-->
        <script>
        function setActive(clickedLink) {
            // Remover clase active de todos los enlaces
            const links = document.querySelectorAll('.nav-link');
            links.forEach(link => link.classList.remove('active'));
            
            // Agregar clase active al enlace clickeado
            clickedLink.classList.add('active');
        }

        function handleProfileClick() {
            alert('Accediendo al perfil del usuario...');
            // Aquí puedes agregar la lógica para mostrar el perfil
        }

        // Funcionalidad adicional para los enlaces de navegación
        document.addEventListener('DOMContentLoaded', function() {
            const navLinks = document.querySelectorAll('.nav-link');
            
            navLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    const section = this.getAttribute('href').substring(1);
                    console.log(`Navegando a: ${section}`);
                    
                    // Aquí puedes agregar lógica para cambiar el contenido
                    updateContent(section);
                });
            });
        });

        function updateContent(section) {
            const content = document.querySelector('.content');
            
            if (section === 'inicio') {
                content.innerHTML = `
                    <h1>Bienvenido a Nuestro Hotel</h1>
                    <p>Experimenta la mejor hospitalidad con nuestros servicios premium. Tu comodidad es nuestra prioridad.</p>
                `;
            } else if (section === 'hotel') {
                content.innerHTML = `
                    <h1>Nuestro Hotel</h1>
                    <p>Descubre nuestras lujosas habitaciones, servicios excepcionales y ubicación privilegiada. Un lugar donde cada detalle está pensado para tu bienestar.</p>
                `;
            }
        }
    </script>
</body>
</html>