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
        include "layouts/navUsuario.php";
    ?>
<body>
    <div class="container">
        <div class="header">
            <h1>🏨 LodgeHub</h1>
            <p>Descubre los mejores hoteles para tu próxima estadía</p>
            
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
        // Base de datos de hoteles (simulando los datos de tu base de datos)
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
                        </div>
                        <button class="view-info-btn" onclick="verInfoHotel(${hotel.id})">
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
</body>
</html>