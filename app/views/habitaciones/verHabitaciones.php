<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Habitaciones - LodgeHub</title>
            <!-- Bootstrap y Font Awesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    
    <!-- Estilos de los layouts (nav y sidebar) -->
    <link href="../../public/assets/css/stylesNav.css" rel="stylesheet">
    <link rel="stylesheet" href="../../public/assets/css/stylesHabitacionver.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Habitaciones</h1>
            <p>Gestión de habitaciones del hotel</p>
        </div>

        <div class="controls">
            <div class="filters">
                <div class="filter-group">
                    <select id="estado-filter" class="filter-select">
                        <option value="">Todos los Estados</option>
                        <option value="disponible">🟢 Disponible</option>
                        <option value="ocupado">🔴 Ocupado</option>
                        <option value="reservado">🟡 Reservado</option>
                        <option value="mantenimiento">🟣 Mantenimiento</option>
                    </select>
                </div>

                <div class="filter-group">
                    <select id="tipo-filter" class="filter-select">
                        <option value="">Todos los Tipos</option>
                        <option value="Individual">Individual</option>
                        <option value="Doble">Doble</option>
                        <option value="Suite">Suite</option>
                    </select>
                </div>

                <div class="filter-group">
                    <select id="capacidad-filter" class="filter-select">
                        <option value="">Capacidad</option>
                        <option value="1">1 persona</option>
                        <option value="2">2 personas</option>
                        <option value="3">3 personas</option>
                        <option value="4">4+ personas</option>
                    </select>
                </div>

                <input type="text" id="search-input" class="search-box" placeholder="🔍 Buscar por número o descripción...">
            </div>

            <a href="#" class="add-room-btn" onclick="alert('Redirigir a formulario de agregar habitación')">
                Agregar Habitación
            </a>
        </div>

        <div class="stats">
            <div class="stat-card stat-disponible">
                <div class="stat-number" id="stat-disponible">0</div>
                <div class="stat-label">Disponibles</div>
            </div>
            <div class="stat-card stat-ocupado">
                <div class="stat-number" id="stat-ocupado">0</div>
                <div class="stat-label">Ocupadas</div>
            </div>
            <div class="stat-card stat-reservado">
                <div class="stat-number" id="stat-reservado">0</div>
                <div class="stat-label">Reservadas</div>
            </div>
            <div class="stat-card stat-mantenimiento">
                <div class="stat-number" id="stat-mantenimiento">0</div>
                <div class="stat-label">Mantenimiento</div>
            </div>
        </div>

        <div id="loading" class="loading">
            <div class="loading-spinner"></div>
            <p>Cargando habitaciones...</p>
        </div>

        <div id="rooms-container" class="rooms-grid" style="display: none;">
            <!-- Las habitaciones se cargarán aquí dinámicamente -->
        </div>

        <div id="no-rooms" class="no-rooms" style="display: none;">
            <div class="no-rooms-icon">🏨</div>
            <h3>No se encontraron habitaciones</h3>
            <p>No hay habitaciones que coincidan con los filtros seleccionados.</p>
        </div>
    </div>

    <!-- Modal para cambiar estado -->
    <div id="status-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Cambiar Estado</h2>
                <span class="close">&times;</span>
            </div>
            <div class="modal-body">
                <p>Habitación: <strong id="modal-room-number"></strong></p>
                <div class="form-group" style="margin: 20px 0;">
                    <label for="new-status" style="display: block; margin-bottom: 10px; font-weight: 600; color: #2a5298;">Nuevo Estado:</label>
                    <select id="new-status" class="filter-select" style="width: 100%;">
                        <option value="disponible">🟢 Disponible</option>
                        <option value="ocupado">🔴 Ocupado</option>
                        <option value="reservado">🟡 Reservado</option>
                        <option value="mantenimiento">🟣 Mantenimiento</option>
                    </select>
                </div>
                <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 25px;">
                    <button class="action-btn btn-edit" onclick="closeModal()">Cancelar</button>
                    <button class="action-btn btn-status" onclick="updateRoomStatus()">Actualizar</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Base de datos simulada de habitaciones
        const habitaciones = [
            {
                id: 1,
                numero: "101",
                tipo: "Individual",
                costo: 85000,
                capacidad: 1,
                estado: "disponible",
                descripcion: "Habitación individual con vista al jardín, incluye WiFi, TV y aire acondicionado.",
                imagen: "https://images.unsplash.com/photo-1611892440504-42a792e24d32?w=400&h=300&fit=crop&crop=center"
            },
            {
                id: 2,
                numero: "102",
                tipo: "Doble",
                costo: 120000,
                capacidad: 2,
                estado: "ocupado",
                descripcion: "Habitación doble con cama queen, baño privado y balcón con vista a la ciudad.",
                imagen: "https://images.unsplash.com/photo-1590490360182-c33d57733427?w=400&h=300&fit=crop&crop=center"
            },
            {
                id: 3,
                numero: "103",
                tipo: "Suite",
                costo: 200000,
                capacidad: 4,
                estado: "reservado",
                descripcion: "Suite ejecutiva con sala, dormitorio separado, cocina básica y vista panorámica.",
                imagen: "https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?w=400&h=300&fit=crop&crop=center"
            },
            {
                id: 4,
                numero: "201",
                tipo: "Individual",
                costo: 90000,
                capacidad: 1,
                estado: "disponible",
                descripcion: "Habitación individual en segundo piso, tranquila y cómoda.",
                imagen: "https://images.unsplash.com/photo-1566665797739-1674de7a421a?w=400&h=300&fit=crop&crop=center"
            },
            {
                id: 5,
                numero: "202",
                tipo: "Doble",
                costo: 130000,
                capacidad: 2,
                estado: "mantenimiento",
                descripcion: "Habitación doble con dos camas individuales. Actualmente en mantenimiento.",
                imagen: "https://images.unsplash.com/photo-1578683010236-d716f9a3f461?w=400&h=300&fit=crop&crop=center"
            },
            {
                id: 6,
                numero: "203",
                tipo: "Doble",
                costo: 125000,
                capacidad: 2,
                estado: "disponible",
                descripcion: "Habitación doble con decoración moderna y todas las comodidades.",
                imagen: "https://images.unsplash.com/photo-1631049307264-da0ec9d70304?w=400&h=300&fit=crop&crop=center"
            },
            {
                id: 7,
                numero: "301",
                tipo: "Suite",
                costo: 250000,
                capacidad: 6,
                estado: "disponible",
                descripcion: "Suite familiar con dos dormitorios, ideal para familias numerosas.",
                imagen: "https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=400&h=300&fit=crop&crop=center"
            },
            {
                id: 8,
                numero: "302",
                tipo: "Individual",
                costo: 95000,
                capacidad: 1,
                estado: "reservado",
                descripcion: "Habitación individual premium con servicio de habitaciones 24/7.",
                imagen: null
            }
        ];

        let filteredHabitaciones = [...habitaciones];
        let currentRoomId = null;

        // Referencias DOM
        const loadingEl = document.getElementById('loading');
        const roomsContainer = document.getElementById('rooms-container');
        const noRoomsEl = document.getElementById('no-rooms');
        const searchInput = document.getElementById('search-input');
        const estadoFilter = document.getElementById('estado-filter');
        const tipoFilter = document.getElementById('tipo-filter');
        const capacidadFilter = document.getElementById('capacidad-filter');
        const modal = document.getElementById('status-modal');
        const modalRoomNumber = document.getElementById('modal-room-number');
        const newStatusSelect = document.getElementById('new-status');

        // Inicializar la aplicación
        function init() {
            setTimeout(() => {
                loadingEl.style.display = 'none';
                renderRooms();
                updateStats();
                roomsContainer.style.display = 'grid';
            }, 1500);

            // Event listeners
            searchInput.addEventListener('input', filterRooms);
            estadoFilter.addEventListener('change', filterRooms);
            tipoFilter.addEventListener('change', filterRooms);
            capacidadFilter.addEventListener('change', filterRooms);

            // Modal event listeners
            document.querySelector('.close').addEventListener('click', closeModal);
            window.addEventListener('click', (e) => {
                if (e.target === modal) closeModal();
            });
        }

        // Renderizar habitaciones
        function renderRooms() {
            if (filteredHabitaciones.length === 0) {
                roomsContainer.style.display = 'none';
                noRoomsEl.style.display = 'block';
                return;
            }

            noRoomsEl.style.display = 'none';
            roomsContainer.style.display = 'grid';

            roomsContainer.innerHTML = filteredHabitaciones.map(room => `
                <div class="room-card ${room.estado}">
                    <div class="room-image">
                        ${room.imagen ? 
                            `<img src="${room.imagen}" alt="Habitación ${room.numero}">` : 
                            '🛏️'
                        }
                        <div class="room-status status-${room.estado}">
                            ${getStatusText(room.estado)}
                        </div>
                    </div>
                    <div class="room-content">
                        <div class="room-number">Habitación ${room.numero}</div>
                        <div class="room-type">🏷️ ${room.tipo}</div>
                        
                        <div class="room-details">
                            <div class="room-detail">
                                <div class="room-detail-label">Capacidad</div>
                                <div class="room-detail-value">${room.capacidad} pers.</div>
                            </div>
                            <div class="room-detail">
                                <div class="room-detail-label">Estado</div>
                                <div class="room-detail-value">${getStatusText(room.estado)}</div>
                            </div>
                        </div>

                        <div class="room-price">$${room.costo.toLocaleString('es-CO')}/noche</div>
                        
                        <div class="room-description">
                            ${room.descripcion}
                        </div>

                        <div class="room-actions">
                            <button class="action-btn btn-edit" onclick="editRoom(${room.id})">
                                ✏️ Editar
                            </button>
                            <button class="action-btn btn-status" onclick="openStatusModal(${room.id})">
                                🔄 Estado
                            </button>
                            <button class="action-btn btn-delete" onclick="deleteRoom(${room.id})">
                                🗑️ Eliminar
                            </button>
                        </div>
                    </div>
                </div>
            `).join('');
        }

        // Obtener texto del estado
        function getStatusText(estado) {
            const textos = {
                'disponible': 'Disponible',
                'ocupado': 'En uso',
                'reservado': 'Reservada',
                'mantenimiento': 'Mantenimiento'
            };
            return textos[estado] || estado;
        }

        // Filtrar habitaciones
        function filterRooms() {
            const searchTerm = searchInput.value.toLowerCase();
            const estadoSelected = estadoFilter.value;
            const tipoSelected = tipoFilter.value;
            const capacidadSelected = capacidadFilter.value;

            filteredHabitaciones = habitaciones.filter(room => {
                const matchSearch = room.numero.toLowerCase().includes(searchTerm) ||
                                  room.descripcion.toLowerCase().includes(searchTerm) ||
                                  room.tipo.toLowerCase().includes(searchTerm);
                
                const matchEstado = !estadoSelected || room.estado === estadoSelected;
                const matchTipo = !tipoSelected || room.tipo === tipoSelected;
                
                let matchCapacidad = true;
                if (capacidadSelected) {
                    if (capacidadSelected === '4') {
                        matchCapacidad = room.capacidad >= 4;
                    } else {
                        matchCapacidad = room.capacidad === parseInt(capacidadSelected);
                    }
                }

                return matchSearch && matchEstado && matchTipo && matchCapacidad;
            });

            renderRooms();
            updateStats();
        }

        // Actualizar estadísticas
        function updateStats() {
            const stats = filteredHabitaciones.reduce((acc, room) => {
                acc[room.estado] = (acc[room.estado] || 0) + 1;
                return acc;
            }, {});

            document.getElementById('stat-disponible').textContent = stats.disponible || 0;
            document.getElementById('stat-ocupado').textContent = stats.ocupado || 0;
            document.getElementById('stat-reservado').textContent = stats.reservado || 0;
            document.getElementById('stat-mantenimiento').textContent = stats.mantenimiento || 0;
        }

        // Funciones de acción
        function editRoom(id) {
            alert(`Editar habitación con ID: ${id}`);
            // Aquí redirigiría al formulario de edición
        }

        function deleteRoom(id) {
            const room = habitaciones.find(r => r.id === id);
            if (confirm(`¿Está seguro de eliminar la habitación ${room.numero}?`)) {
                const index = habitaciones.findIndex(r => r.id === id);
                habitaciones.splice(index, 1);
                filterRooms();
                alert('Habitación eliminada exitosamente');
            }
        }

        // Modal functions
        function openStatusModal(id) {
            currentRoomId = id;
            const room = habitaciones.find(r => r.id === id);
            modalRoomNumber.textContent = room.numero;
            newStatusSelect.value = room.estado;
            modal.style.display = 'block';
        }

        function closeModal() {
            modal.style.display = 'none';
            currentRoomId = null;
        }

        function updateRoomStatus() {
            if (!currentRoomId) return;
            
            const newStatus = newStatusSelect.value;
            const room = habitaciones.find(r => r.id === currentRoomId);
            room.estado = newStatus;
            
            filterRooms();
            closeModal();
            alert(`Estado de la habitación ${room.numero} actualizado a: ${getStatusText(newStatus)}`);
        }

        // Iniciar aplicación
        init();
    </script>
</body>
</html>