<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Habitaciones - LodgeHub</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header h1 {
            color: white;
            font-size: 2.5rem;
            font-weight: 300;
            margin-bottom: 10px;
            text-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }

        .header p {
            color: rgba(255,255,255,0.8);
            font-size: 1.1rem;
        }

        /* Controles superiores */
        .controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .filters {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .filter-btn {
            padding: 10px 20px;
            border: none;
            border-radius: 25px;
            background: rgba(255,255,255,0.2);
            color: white;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
            font-size: 14px;
        }

        .filter-btn:hover {
            background: rgba(255,255,255,0.3);
            transform: translateY(-2px);
        }

        .filter-btn.active {
            background: rgba(255,255,255,0.9);
            color: #667eea;
        }

        .add-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            background: white;
            color: #667eea;
            border: none;
            border-radius: 25px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }

        .add-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.3);
        }

        /* Grid de habitaciones */
        .rooms-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }

        .room-card {
            background: rgba(255,255,255,0.15);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 20px;
            border: 2px solid transparent;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .room-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, transparent, rgba(255,255,255,0.1), transparent);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .room-card:hover::before {
            opacity: 1;
        }

        .room-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }

        /* Estados de habitaciones */
        .room-card.disponible { border-color: #4CAF50; }
        .room-card.ocupado { border-color: #F44336; }
        .room-card.reservado { border-color: #FF9800; }
        .room-card.mantenimiento { border-color: #9C27B0; }

        .room-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .room-number {
            font-size: 1.2rem;
            font-weight: 600;
            color: white;
        }

        .room-status {
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 500;
            color: white;
        }

        .room-status.disponible { background: #4CAF50; }
        .room-status.ocupado { background: #F44336; }
        .room-status.reservado { background: #FF9800; }
        .room-status.mantenimiento { background: #9C27B0; }

        .room-image {
            width: 100%;
            height: 150px;
            background: rgba(255,255,255,0.2);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            margin-bottom: 15px;
            overflow: hidden;
        }

        .room-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 15px;
        }

        .room-info {
            color: white;
            margin-bottom: 15px;
        }

        .room-type {
            font-size: 0.95rem;
            opacity: 0.9;
            margin-bottom: 5px;
        }

        .room-details {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .room-detail {
            text-align: center;
        }

        .room-detail-label {
            font-size: 0.8rem;
            opacity: 0.8;
            margin-bottom: 2px;
        }

        .room-detail-value {
            font-weight: 600;
        }

        .room-price {
            font-size: 1.3rem;
            font-weight: 700;
            color: #FFD700;
            text-align: center;
            margin-bottom: 15px;
        }

        .room-actions {
            display: flex;
            gap: 8px;
            justify-content: center;
        }

        .action-btn {
            flex: 1;
            padding: 8px 12px;
            border: none;
            border-radius: 10px;
            font-size: 12px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-edit {
            background: #2196F3;
            color: white;
        }

        .btn-status {
            background: #FF9800;
            color: white;
        }

        .btn-delete {
            background: #F44336;
            color: white;
        }

        .action-btn:hover {
            transform: translateY(-1px);
            filter: brightness(1.1);
        }

        /* Estadísticas */
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: rgba(255,255,255,0.15);
            backdrop-filter: blur(20px);
            border-radius: 15px;
            padding: 20px;
            text-align: center;
            color: white;
            border: 2px solid rgba(255,255,255,0.2);
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .stat-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        /* Loading */
        .loading {
            text-align: center;
            padding: 60px 20px;
            color: white;
        }

        .loading-spinner {
            width: 50px;
            height: 50px;
            border: 3px solid rgba(255,255,255,0.3);
            border-top: 3px solid white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .controls {
                flex-direction: column;
                align-items: stretch;
            }

            .filters {
                justify-content: center;
            }

            .rooms-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .header h1 {
                font-size: 2rem;
            }
        }

        /* Animaciones */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .room-card {
            animation: fadeInUp 0.6s ease forwards;
        }

        .room-card:nth-child(1) { animation-delay: 0.1s; }
        .room-card:nth-child(2) { animation-delay: 0.2s; }
        .room-card:nth-child(3) { animation-delay: 0.3s; }
        .room-card:nth-child(4) { animation-delay: 0.4s; }
        .room-card:nth-child(5) { animation-delay: 0.5s; }
        .room-card:nth-child(6) { animation-delay: 0.6s; }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>🏨 Habitaciones</h1>
            <p>Gestión de habitaciones del hotel</p>
        </div>

        <!-- Controles -->
        <div class="controls">
            <div class="filters">
                <button class="filter-btn active" data-filter="todos">Todos</button>
                <button class="filter-btn" data-filter="disponible">🟢 Disponible</button>
                <button class="filter-btn" data-filter="ocupado">🔴 Ocupado</button>
                <button class="filter-btn" data-filter="reservado">🟡 Reservado</button>
                <button class="filter-btn" data-filter="mantenimiento">🟣 Mantenimiento</button>
            </div>
            <button class="add-btn">
                <span>➕</span>
                Agregar Habitación
            </button>
        </div>

        <!-- Estadísticas -->
        <div class="stats">
            <div class="stat-card">
                <div class="stat-number" id="stat-disponible">6</div>
                <div class="stat-label">Disponibles</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" id="stat-ocupado">3</div>
                <div class="stat-label">Ocupadas</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" id="stat-reservado">3</div>
                <div class="stat-label">Reservadas</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" id="stat-mantenimiento">2</div>
                <div class="stat-label">Mantenimiento</div>
            </div>
        </div>

        <!-- Loading inicial -->
        <div id="loading" class="loading">
            <div class="loading-spinner"></div>
            <p>Cargando habitaciones...</p>
        </div>

        <!-- Grid de habitaciones -->
        <div id="rooms-grid" class="rooms-grid" style="display: none;">
            <!-- Las habitaciones se generarán dinámicamente -->
        </div>
    </div>

    <script>
        // Base de datos de habitaciones
        const habitaciones = [
            {
                id: 1,
                numero: "101",
                tipo: "Individual",
                costo: 85000,
                capacidad: 1,
                estado: "disponible",
                imagen: "🛏️"
            },
            {
                id: 2,
                numero: "102",
                tipo: "Doble",
                costo: 120000,
                capacidad: 2,
                estado: "ocupado",
                imagen: "🛏️"
            },
            {
                id: 3,
                numero: "103",
                tipo: "Suite",
                costo: 200000,
                capacidad: 4,
                estado: "reservado",
                imagen: "🛏️"
            },
            {
                id: 4,
                numero: "201",
                tipo: "Individual",
                costo: 90000,
                capacidad: 1,
                estado: "disponible",
                imagen: "🛏️"
            },
            {
                id: 5,
                numero: "202",
                tipo: "Doble",
                costo: 130000,
                capacidad: 2,
                estado: "mantenimiento",
                imagen: "🛏️"
            },
            {
                id: 6,
                numero: "203",
                tipo: "Doble",
                costo: 125000,
                capacidad: 2,
                estado: "disponible",
                imagen: "🛏️"
            },
            {
                id: 7,
                numero: "301",
                tipo: "Suite",
                costo: 250000,
                capacidad: 6,
                estado: "disponible",
                imagen: "🛏️"
            },
            {
                id: 8,
                numero: "302",
                tipo: "Individual",
                costo: 95000,
                capacidad: 1,
                estado: "reservado",
                imagen: "🛏️"
            },
            {
                id: 9,
                numero: "303",
                tipo: "Doble",
                costo: 140000,
                capacidad: 2,
                estado: "disponible",
                imagen: "🛏️"
            },
            {
                id: 10,
                numero: "401",
                tipo: "Suite",
                costo: 300000,
                capacidad: 8,
                estado: "disponible",
                imagen: "🛏️"
            },
            {
                id: 11,
                numero: "402",
                tipo: "Doble",
                costo: 115000,
                capacidad: 2,
                estado: "ocupado",
                imagen: "🛏️"
            },
            {
                id: 12,
                numero: "403",
                tipo: "Individual",
                costo: 80000,
                capacidad: 1,
                estado: "disponible",
                imagen: "🛏️"
            }
        ];

        let filteredHabitaciones = [...habitaciones];
        let currentFilter = 'todos';

        // Elementos DOM
        const loadingEl = document.getElementById('loading');
        const roomsGrid = document.getElementById('rooms-grid');
        const filterBtns = document.querySelectorAll('.filter-btn');

        // Función para obtener texto del estado
        function getStatusText(estado) {
            const textos = {
                'disponible': 'Disponible',
                'ocupado': 'En Uso',
                'reservado': 'Reservada',
                'mantenimiento': 'Mantenimiento'
            };
            return textos[estado] || estado;
        }

        // Renderizar habitaciones
        function renderRooms() {
            roomsGrid.innerHTML = filteredHabitaciones.map(room => `
                <div class="room-card ${room.estado}">
                    <div class="room-header">
                        <div class="room-number">Habitación N° ${room.numero}</div>
                        <div class="room-status ${room.estado}">${getStatusText(room.estado)}</div>
                    </div>
                    
                    <div class="room-image">
                        ${room.imagen}
                    </div>
                    
                    <div class="room-info">
                        <div class="room-type">Tipo: ${room.tipo}</div>
                        <div class="room-details">
                            <div class="room-detail">
                                <div class="room-detail-label">Capacidad</div>
                                <div class="room-detail-value">${room.capacidad} pers</div>
                            </div>
                            <div class="room-detail">
                                <div class="room-detail-label">Estado</div>
                                <div class="room-detail-value">${getStatusText(room.estado)}</div>
                            </div>
                        </div>
                        <div class="room-price">$${room.costo.toLocaleString('es-CO')}</div>
                    </div>
                    
                    <div class="room-actions">
                        <button class="action-btn btn-edit" onclick="editRoom(${room.id})">✏️ Editar</button>
                        <button class="action-btn btn-status" onclick="changeStatus(${room.id})">🔄 Estado</button>
                        <button class="action-btn btn-delete" onclick="deleteRoom(${room.id})">🗑️ Eliminar</button>
                    </div>
                </div>
            `).join('');
        }

        // Filtrar habitaciones
        function filterRooms(filter) {
            currentFilter = filter;
            
            if (filter === 'todos') {
                filteredHabitaciones = [...habitaciones];
            } else {
                filteredHabitaciones = habitaciones.filter(room => room.estado === filter);
            }
            
            renderRooms();
            updateFilterButtons();
        }

        // Actualizar botones de filtro
        function updateFilterButtons() {
            filterBtns.forEach(btn => {
                btn.classList.remove('active');
                if (btn.dataset.filter === currentFilter) {
                    btn.classList.add('active');
                }
            });
        }

        // Event listeners para filtros
        filterBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                filterRooms(btn.dataset.filter);
            });
        });

        // Funciones de acción (placeholder)
        function editRoom(id) {
            alert(`Editar habitación ID: ${id}`);
        }

        function changeStatus(id) {
            alert(`Cambiar estado habitación ID: ${id}`);
        }

        function deleteRoom(id) {
            if (confirm('¿Eliminar esta habitación?')) {
                const index = habitaciones.findIndex(r => r.id === id);
                habitaciones.splice(index, 1);
                filterRooms(currentFilter);
            }
        }

        // Inicializar aplicación
        function init() {
            setTimeout(() => {
                loadingEl.style.display = 'none';
                roomsGrid.style.display = 'grid';
                renderRooms();
            }, 1500);
        }

        // Iniciar
        init();
    </script>
</body>
</html>