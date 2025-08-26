<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LodgeHub - Encuentra tu Hotel Perfecto</title>
    <link rel="stylesheet" href="../../public/assets/css/stylesNavUsuario.css">
    <link rel="stylesheet" href="../../public/assets/css/stylesHomeUsuario.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

</head>

    <?php
        include "layouts/navusuario.php";
    ?>

<body>
    <!-- Aquí iría tu navUsuario.php -->
    
    <div class="container">
        <div class="header">
            <div class="img">
            <img src="/lodgehub/public/img/LogoClaroLHSinTitulo.png" alt="LODGEHUB">
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
        // Base de datos de hoteles actualizada con todos los datos de tu BD
        const hoteles = [
            {
                id: 1,
                nit: '900123456-1',
                nombre: 'LodgeHub Plaza Hotel',
                direccion: 'Carrera 15 #85-23, Zona Rosa, Bogotá',
                numDocumento: '1234567890',
                telefono: '6015551234',
                correo: 'info@lodgehubplaza.com',
                foto: 'https://images.unsplash.com/photo-1564501049412-61c2a3083791?w=400&h=200&fit=crop'
            },
            {
                id: 2,
                nit: '900789123-4',
                nombre: 'LodgeHub Business Center',
                direccion: 'Avenida 68 #45-67, Centro Internacional, Bogotá',
                numDocumento: '1234567890',
                telefono: '6017891234',
                correo: 'reservas@lodgehubbusiness.com',
                foto: 'https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?w=400&h=200&fit=crop'
            },
            {
                id: 3,
                nit: '900456789-7',
                nombre: 'LodgeHub Garden Resort',
                direccion: 'Km 5 Vía La Calera, Cundinamarca',
                numDocumento: '9876543210',
                telefono: '6014567890',
                correo: 'contacto@lodgehubgarden.com',
                foto: 'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?w=400&h=200&fit=crop'
            },
            {
                id: 4,
                nit: '900234567-2',
                nombre: 'LodgeHub Beach Resort',
                direccion: 'Kilómetro 12 Vía Santa Marta, Rodadero',
                numDocumento: '1000289068',
                telefono: '6054321890',
                correo: 'reservas@lodgehubbeach.com',
                foto: 'https://images.unsplash.com/photo-1571003123894-1f0594d2b5d9?w=400&h=200&fit=crop'
            },
            {
                id: 5,
                nit: '900345678-3',
                nombre: 'LodgeHub Mountain Lodge',
                direccion: 'Vereda El Chico, Vía La Vega',
                numDocumento: '1014596349',
                telefono: '6018765432',
                correo: 'info@lodgehubmountain.com',
                foto: 'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=400&h=200&fit=crop'
            },
            {
                id: 6,
                nit: '900567890-5',
                nombre: 'LodgeHub City Suites',
                direccion: 'Calle 53 #45-25, El Poblado, Medellín',
                numDocumento: '5555666677',
                telefono: '6043216547',
                correo: 'contacto@lodgehubcity.com',
                foto: 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=400&h=200&fit=crop'
            },
            {
                id: 7,
                nit: '900678901-6',
                nombre: 'LodgeHub Colonial Inn',
                direccion: 'Carrera 3 #18-56, Centro Histórico, Cartagena',
                numDocumento: '1234567890',
                telefono: '6057654321',
                correo: 'reservas@lodgehubcolonial.com',
                foto: 'https://images.unsplash.com/photo-1444201983204-c43cbd584d93?w=400&h=200&fit=crop'
            },
            {
                id: 8,
                nit: '900789012-8',
                nombre: 'LodgeHub Eco Retreat',
                direccion: 'Vía Parque Tayrona, Magdalena',
                numDocumento: '9876543210',
                telefono: '6056543210',
                correo: 'info@lodgehubeco.com',
                foto: 'https://images.unsplash.com/photo-1586375300773-8384e3e4916f?w=400&h=200&fit=crop'
            },
            {
                id: 9,
                nit: '900890123-9',
                nombre: 'LodgeHub Executive Tower',
                direccion: 'Avenida Boyacá #15-30, Chapinero Alto, Bogotá',
                numDocumento: '1014596349',
                telefono: '6019876543',
                correo: 'reservas@lodgehubexecutive.com',
                foto: 'https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?w=400&h=200&fit=crop'
            }
        ];

        let todosLosHoteles = [...hoteles];

        function crearTarjetaHotel(hotel) {
            return `
                <div class="hotel-card" data-hotel-id="${hotel.id}">
                    <img src="${hotel.foto}" alt="${hotel.nombre}" class="hotel-image">
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
                        <button class="view-info-btn" onclick="plazaHotel.php(${hotel.id})">
                            Ver Información Completa
                        </button>
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

        function verInfoHotel(hotelId) {
            const hotel = todosLosHoteles.find(h => h.id === hotelId);
            if (hotel) {
                // Simular redirección a página de información del hotel
                // En una aplicación real, esto sería: window.location.href = `/hotel/${hotelId}`
                alert(`Redirigiendo a la información completa del hotel: ${hotel.nombre}\n\nEn una aplicación real, esto te llevaría a: /hotel/${hotelId}`);
            }
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