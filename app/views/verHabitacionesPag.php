<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Habitaciones - LodgeHub</title>

        <link rel="stylesheet" href="../../public/assets/css/stylesHabitacionver.css">
        
    <!-- Bootstrap y Font Awesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">


    <style>
        /* Estilos para la paginación */
        .pagination-container {
            margin-top: 30px;
            padding: 20px 0;
            border-top: 1px solid #e9ecef;
        }

        .pagination-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .results-info {
            color: #6c757d;
            font-size: 14px;
        }

        .per-page-selector {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .per-page-selector label {
            font-size: 14px;
            color: #2a5298;
            font-weight: 500;
        }

        .per-page-selector select {
            padding: 5px 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background: white;
            color: #333;
            font-size: 14px;
        }

        .pagination-controls {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 5px;
            flex-wrap: wrap;
        }

        .pagination-btn {
            padding: 8px 12px;
            border: 1px solid #ddd;
            background: white;
            color: #2a5298;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.2s ease;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 5px;
            min-width: 40px;
            justify-content: center;
        }

        .pagination-btn:hover:not(.disabled) {
            background: #2a5298;
            color: white;
            text-decoration: none;
        }

        .pagination-btn.active {
            background: #2a5298;
            color: white;
            border-color: #2a5298;
        }

        .pagination-btn.disabled {
            opacity: 0.5;
            cursor: not-allowed;
            color: #999;
        }

        .pagination-ellipsis {
            padding: 8px 4px;
            color: #6c757d;
        }

        /* Responsive para la paginación */
        @media (max-width: 768px) {
            .pagination-info {
                flex-direction: column;
                text-align: center;
            }

            .pagination-controls {
                justify-content: center;
                gap: 3px;
            }

            .pagination-btn {
                padding: 6px 10px;
                font-size: 12px;
                min-width: 35px;
            }
        }

        /* Ajuste para mostrar loading de paginación */
        .pagination-loading {
            text-align: center;
            padding: 20px;
            color: #6c757d;
        }

        .pagination-loading .loading-spinner {
            width: 20px;
            height: 20px;
            margin: 0 auto 10px;
        }
    </style>
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

        <!-- Controles de paginación -->
        <div id="pagination-container" class="pagination-container" style="display: none;">
            <div class="pagination-info">
                <div class="results-info" id="results-info">
                    <!-- Se llenará dinámicamente -->
                </div>
                <div class="per-page-selector">
                    <label for="per-page-select">Mostrar:</label>
                    <select id="per-page-select">
                        <option value="12">12 por página</option>
                        <option value="16">16 por página</option>
                        <option value="21">21 por página</option>
                        <option value="50">50 por página</option>
                    </select>
                </div>
            </div>
            <div class="pagination-controls" id="pagination-controls">
                <!-- Se llenará dinámicamente -->
            </div>
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
        // Base de datos simulada de habitaciones (expandida para probar la paginación)
        const habitaciones = [{
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
            },
            // Habitaciones adicionales para probar la paginación
            {
                id: 9,
                numero: "303",
                tipo: "Doble",
                costo: 140000,
                capacidad: 2,
                estado: "disponible",
                descripcion: "Habitación doble con jacuzzi y vista panorámica de la ciudad.",
                imagen: "https://images.unsplash.com/photo-1618773928121-c32242e63f39?w=400&h=300&fit=crop&crop=center"
            },
            {
                id: 10,
                numero: "401",
                tipo: "Suite",
                costo: 300000,
                capacidad: 8,
                estado: "disponible",
                descripcion: "Suite presidencial con tres dormitorios y terraza privada.",
                imagen: "https://images.unsplash.com/photo-1591088398332-8a7791972843?w=400&h=300&fit=crop&crop=center"
            },
            {
                id: 11,
                numero: "402",
                tipo: "Doble",
                costo: 115000,
                capacidad: 2,
                estado: "ocupado",
                descripcion: "Habitación doble estándar con todas las comodidades básicas.",
                imagen: "https://images.unsplash.com/photo-1584132967334-10e028bd69f7?w=400&h=300&fit=crop&crop=center"
            },
            {
                id: 12,
                numero: "403",
                tipo: "Individual",
                costo: 80000,
                capacidad: 1,
                estado: "disponible",
                descripcion: "Habitación individual económica pero confortable.",
                imagen: "https://images.unsplash.com/photo-1586023492125-27b2c045efd7?w=400&h=300&fit=crop&crop=center"
            },
            {
                id: 13,
                numero: "501",
                tipo: "Suite",
                costo: 180000,
                capacidad: 3,
                estado: "reservado",
                descripcion: "Mini suite con sala de estar y dormitorio separado.",
                imagen: "https://images.unsplash.com/photo-1595576508898-0ad5c879a061?w=400&h=300&fit=crop&crop=center"
            },
            {
                id: 14,
                numero: "502",
                tipo: "Doble",
                costo: 135000,
                capacidad: 2,
                estado: "disponible",
                descripcion: "Habitación doble con balcón y vista al mar.",
                imagen: "https://images.unsplash.com/photo-1571896349842-33c89424de2d?w=400&h=300&fit=crop&crop=center"
            },
            {
                id: 15,
                numero: "503",
                tipo: "Individual",
                costo: 92000,
                capacidad: 1,
                estado: "mantenimiento",
                descripcion: "Habitación individual en remodelación.",
                imagen: null
            },
            {
                id: 16,
                numero: "601",
                tipo: "Suite",
                costo: 220000,
                capacidad: 5,
                estado: "disponible",
                descripcion: "Suite familiar con cocina completa y dos baños.",
                imagen: "https://images.unsplash.com/photo-1562790351-d273a961e0e9?w=400&h=300&fit=crop&crop=center"
            },
            {
                id: 17,
                numero: "602",
                tipo: "Doble",
                costo: 128000,
                capacidad: 2,
                estado: "ocupado",
                descripcion: "Habitación doble con decoración temática y ambiente romántico.",
                imagen: "https://images.unsplash.com/photo-1594736797933-d0c9b5ad80fe?w=400&h=300&fit=crop&crop=center"
            },
            {
                id: 18,
                numero: "603",
                tipo: "Individual",
                costo: 88000,
                capacidad: 1,
                estado: "disponible",
                descripcion: "Habitación individual con escritorio y zona de trabajo.",
                imagen: "https://images.unsplash.com/photo-1596394516093-501ba68a0ba6?w=400&h=300&fit=crop&crop=center"
            }
        ];

        let filteredHabitaciones = [...habitaciones];
        let currentRoomId = null;

        // Variables de paginación
        let currentPage = 1;
        let itemsPerPage = 12;
        let totalPages = 1;
        let paginatedRooms = [];

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

        // Referencias DOM de paginación
        const paginationContainer = document.getElementById('pagination-container');
        const resultsInfo = document.getElementById('results-info');
        const paginationControls = document.getElementById('pagination-controls');
        const perPageSelect = document.getElementById('per-page-select');

        // Inicializar la aplicación
        function init() {
            setTimeout(() => {
                loadingEl.style.display = 'none';
                filterRooms();
                roomsContainer.style.display = 'grid';
            }, 1500);

            // Event listeners
            searchInput.addEventListener('input', () => {
                currentPage = 1;
                filterRooms();
            });
            estadoFilter.addEventListener('change', () => {
                currentPage = 1;
                filterRooms();
            });
            tipoFilter.addEventListener('change', () => {
                currentPage = 1;
                filterRooms();
            });
            capacidadFilter.addEventListener('change', () => {
                currentPage = 1;
                filterRooms();
            });

            // Event listener para cambio de items por página
            perPageSelect.addEventListener('change', (e) => {
                itemsPerPage = parseInt(e.target.value);
                currentPage = 1;
                filterRooms();
            });

            // Modal event listeners
            document.querySelector('.close').addEventListener('click', closeModal);
            window.addEventListener('click', (e) => {
                if (e.target === modal) closeModal();
            });
        }

        // Calcular paginación
        function calculatePagination() {
            totalPages = Math.ceil(filteredHabitaciones.length / itemsPerPage);
            if (currentPage > totalPages) currentPage = Math.max(1, totalPages);

            const startIndex = (currentPage - 1) * itemsPerPage;
            const endIndex = startIndex + itemsPerPage;
            paginatedRooms = filteredHabitaciones.slice(startIndex, endIndex);
        }

        // Renderizar habitaciones
        function renderRooms() {
            if (filteredHabitaciones.length === 0) {
                roomsContainer.style.display = 'none';
                noRoomsEl.style.display = 'block';
                paginationContainer.style.display = 'none';
                return;
            }

            noRoomsEl.style.display = 'none';
            roomsContainer.style.display = 'grid';
            paginationContainer.style.display = 'block';

            roomsContainer.innerHTML = paginatedRooms.map(room => `
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

            renderPaginationInfo();
            renderPaginationControls();
        }

        // Renderizar información de paginación
        function renderPaginationInfo() {
            const startItem = (currentPage - 1) * itemsPerPage + 1;
            const endItem = Math.min(currentPage * itemsPerPage, filteredHabitaciones.length);
            const totalItems = filteredHabitaciones.length;

            resultsInfo.innerHTML = `Mostrando ${startItem}-${endItem} de ${totalItems} habitaciones`;
        }

        // Renderizar controles de paginación
        function renderPaginationControls() {
            if (totalPages <= 1) {
                paginationControls.innerHTML = '';
                return;
            }

            let paginationHTML = '';

            // Botón Anterior
            paginationHTML += `
                <button class="pagination-btn ${currentPage === 1 ? 'disabled' : ''}" 
                        onclick="${currentPage === 1 ? '' : 'goToPage(' + (currentPage - 1) + ')'}">
                    <i class="fas fa-chevron-left"></i> Anterior
                </button>
            `;

            // Lógica para mostrar números de página
            const maxVisiblePages = 5;
            let startPage = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2));
            let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);

            // Ajustar si estamos cerca del final
            if (endPage - startPage < maxVisiblePages - 1) {
                startPage = Math.max(1, endPage - maxVisiblePages + 1);
            }

            // Primera página y elipsis si es necesario
            if (startPage > 1) {
                paginationHTML += `<button class="pagination-btn" onclick="goToPage(1)">1</button>`;
                if (startPage > 2) {
                    paginationHTML += `<span class="pagination-ellipsis">...</span>`;
                }
            }

            // Páginas visibles
            for (let i = startPage; i <= endPage; i++) {
                paginationHTML += `
                    <button class="pagination-btn ${i === currentPage ? 'active' : ''}" 
                            onclick="goToPage(${i})">${i}</button>
                `;
            }

            // Última página y elipsis si es necesario
            if (endPage < totalPages) {
                if (endPage < totalPages - 1) {
                    paginationHTML += `<span class="pagination-ellipsis">...</span>`;
                }
                paginationHTML += `<button class="pagination-btn" onclick="goToPage(${totalPages})">${totalPages}</button>`;
            }

            // Botón Siguiente
            paginationHTML += `
                <button class="pagination-btn ${currentPage === totalPages ? 'disabled' : ''}" 
                        onclick="${currentPage === totalPages ? '' : 'goToPage(' + (currentPage + 1) + ')'}">
                    Siguiente <i class="fas fa-chevron-right"></i>
                </button>
            `;

            paginationControls.innerHTML = paginationHTML;
        }

        // Ir a página específica
        function goToPage(page) {
            if (page < 1 || page > totalPages || page === currentPage) return;
            currentPage = page;
            calculatePagination();
            renderRooms();

            // Scroll suave hacia arriba
            document.querySelector('.header').scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
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

            calculatePagination();
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

                // Recalcular después de eliminar
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
            
        }

        // Iniciar aplicación
        init();
    </script>
</body>

</html>