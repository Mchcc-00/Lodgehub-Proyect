let currentFilter = 'all';

// Mapeo de clases para los estados de las habitaciones
const statusClass = {
    'disponible': 'room-disponible',
    'reservada': 'room-reservada',
    'en-uso': 'room-en-uso'
};


// Función para renderizar las habitaciones
function renderRooms(roomsToRender = rooms) {
    const roomsGrid = document.getElementById('roomsGrid');
    roomsGrid.innerHTML = '';

    // Ordenar por número de habitación de menor a mayor
    const sortedRooms = [...roomsToRender].sort((a, b) => a.number - b.number);

    sortedRooms.forEach(room => {
        const statusText = {
            'reservada': 'Reservada',
            'en-uso': 'En uso',
            'disponible': 'Disponible'
        };

        // Ajustar clase de estado para CSS
        let estadoClass = '';
        if (room.status === 'en-uso') {
            estadoClass = 'estado-enuso';
        } else {
            estadoClass = 'estado-' + room.status;
        }

        const roomCard = document.createElement('div');
        roomCard.className = `room-card ${estadoClass}`;
        roomCard.innerHTML = `
            <h3>Habitación N° ${room.number}</h3>
            <div class="room-img"><img src='../public/img/iconCama.png' alt='Cama' style='width:60px;height:auto;display:block;margin:auto;'></div>
            <div class="room-type">Tipo: ${room.type}</div>
            <span class="estado-label">Estado: ${statusText[room.status]}</span>
            <button class="edit-room-btn" data-room-id="${room.id}">Editar</button>
        `;
        roomsGrid.appendChild(roomCard);

        // Event listener para el botón editar
        roomCard.querySelector('.edit-room-btn').addEventListener('click', function() {
            editarHabitacion(room);
        });

        roomCard.addEventListener('click', function(e) {
            // Evitar que el click en el botón editar dispare el modal de detalles
            if (e.target.classList.contains('edit-room-btn')) return;
            showRoomDetails(room);
        });
    });
}



// Función para filtrar habitaciones
function filterRooms(status) {
    const filterButtons = document.querySelectorAll('.filter-btn');
    filterButtons.forEach(btn => btn.classList.remove('active'));

    if (status === 'all') {
        renderRooms(rooms);
        currentFilter = 'all';
    } else {
        const filteredRooms = rooms.filter(room => room.status === status);
        renderRooms(filteredRooms);
        currentFilter = status;
        
        // Activar botón correspondiente
        const activeButton = document.querySelector(`[data-filter="estado"]`);
        if (activeButton) activeButton.classList.add('active');
    }
}

function filterRoomsBy(field, value) {
    let filtered;
    if (field === 'precio') {
        // value puede ser un rango, por ejemplo: '0-100000', '100000-200000', etc.
        const [min, max] = value.split('-').map(Number);
        filtered = rooms.filter(room => room.precio >= min && room.precio <= max);
    } else {
        filtered = rooms.filter(room => String(room[field]).toLowerCase() === value.toLowerCase());
    }
    renderRooms(filtered);
    currentFilter = field + ':' + value;
}

function showGenericDropdown(button, field, options) {
    const existingDropdown = document.querySelector('.status-dropdown');
    if (existingDropdown) {
        existingDropdown.remove();
        return;
    }
    const dropdown = document.createElement('div');
    dropdown.className = 'status-dropdown';
    options.forEach(option => {
        const optionDiv = document.createElement('div');
        optionDiv.textContent = option.text;
        optionDiv.addEventListener('click', () => {
            filterRoomsBy(field, option.value);
            dropdown.remove();
        });
        dropdown.appendChild(optionDiv);
    });
    button.style.position = 'relative';
    button.appendChild(dropdown);
    setTimeout(() => {
        document.addEventListener('click', function closeDropdown(e) {
            if (!button.contains(e.target)) {
                dropdown.remove();
                document.removeEventListener('click', closeDropdown);
            }
        });
    }, 100);
}

// Event listeners para los filtros
function initializeFilters() {
    document.querySelectorAll('.filter-btn').forEach(button => {
        button.addEventListener('click', function() {
            const filter = this.getAttribute('data-filter');
            
            if (filter === 'estado') {
                // Crear dropdown para estados
                showStatusDropdown(this);
            } else if (filter === 'tamano') {
                showGenericDropdown(this, 'tamano', [
                    { value: 'Pequeña', text: 'Pequeña' },
                    { value: 'Mediana', text: 'Mediana' },
                    { value: 'Grande', text: 'Grande' }
                ]);
            } else if (filter === 'tipo') {
                showGenericDropdown(this, 'type', [
                    { value: 'Individual', text: 'Individual' },
                    { value: 'Doble', text: 'Doble' },
                    { value: 'Suite', text: 'Suite' }
                ]);
            } else if (filter === 'precio') {
                showGenericDropdown(this, 'precio', [
                    { value: '0-100000', text: 'Hasta $100,000' },
                    { value: '100001-200000', text: '$100,001 - $200,000' },
                    { value: '200001-9999999', text: 'Más de $200,000' }
                ]);
            } else {
                // Para otros filtros, mostrar mensaje
                alert(`Filtro ${filter} - Funcionalidad pendiente de implementar`);
            }
        });
    });
}

// Función para mostrar dropdown de estados
function showStatusDropdown(button) {
    // Remover dropdown existente
    const existingDropdown = document.querySelector('.status-dropdown');
    if (existingDropdown) {
        existingDropdown.remove();
        return;
    }

    const dropdown = document.createElement('div');
    dropdown.className = 'status-dropdown';

    const options = [
        { value: 'all', text: 'Todos los estados' },
        { value: 'disponible', text: 'Disponible' },
        { value: 'reservada', text: 'Reservada' },
        { value: 'en-uso', text: 'En uso' }
    ];

    options.forEach(option => {
        const optionDiv = document.createElement('div');
        optionDiv.textContent = option.text;
        
        optionDiv.addEventListener('click', () => {
            filterRooms(option.value);
            dropdown.remove();
        });
        
        dropdown.appendChild(optionDiv);
    });

    button.style.position = 'relative';
    button.appendChild(dropdown);

    // Cerrar dropdown al hacer clic fuera
    setTimeout(() => {
        document.addEventListener('click', function closeDropdown(e) {
            if (!button.contains(e.target)) {
                dropdown.remove();
                document.removeEventListener('click', closeDropdown);
            }
        });
    }, 100);
}

// Función para mostrar detalles de habitación en un modal bonito
function showRoomDetails(room) {
    // Eliminar modal anterior si existe
    const oldModal = document.getElementById('roomDetailModal');
    if (oldModal) oldModal.remove();

    // Crear overlay
    const overlay = document.createElement('div');
    overlay.id = 'roomDetailModal';
    overlay.style.position = 'fixed';
    overlay.style.top = '0';
    overlay.style.left = '0';
    overlay.style.width = '100vw';
    overlay.style.height = '100vh';
    overlay.style.background = 'rgba(0,0,0,0.7)';
    overlay.style.display = 'flex';
    overlay.style.alignItems = 'center';
    overlay.style.justifyContent = 'center';
    overlay.style.zIndex = '9999';

    // Modal principal
    const modal = document.createElement('div');
    modal.style.background = '#222';
    modal.style.borderRadius = '18px';
    modal.style.display = 'flex';
    modal.style.overflow = 'hidden';
    modal.style.maxWidth = '900px';
    modal.style.width = '90vw';
    modal.style.boxShadow = '0 8px 32px #0008';
    modal.style.color = '#fff';
    modal.style.fontFamily = 'Outfit, Montserrat, Arial, sans-serif';

    // Imagen
    const imgDiv = document.createElement('div');
    imgDiv.style.flex = '1 1 50%';
    imgDiv.style.background = '#fff';
    imgDiv.style.display = 'flex';
    imgDiv.style.alignItems = 'center';
    imgDiv.style.justifyContent = 'center';
    imgDiv.innerHTML = `<img src='../public/img/previaHabitacion.png' alt='Cama' style='width:100%;max-width:420px;max-height:350px;border-radius:0 0 0 18px;'>`;

    // Info
    const infoDiv = document.createElement('div');
    infoDiv.style.flex = '1 1 50%';
    infoDiv.style.padding = '32px 28px 24px 28px';
    infoDiv.style.display = 'flex';
    infoDiv.style.flexDirection = 'column';
    infoDiv.style.justifyContent = 'space-between';

    // Información adicional
    const infoAdicional = document.createElement('div');
    infoAdicional.innerHTML = `
        <div style='font-size:1.2em;font-weight:700;color:#4299e1;margin-bottom:12px;'>INFORMACIÓN ADICIONAL</div>
        <div style='font-size:1em;line-height:1.6;margin-bottom:18px;'>${room.info ? room.info.replace(/\n/g, '<br>') : 'Sin información adicional.'}</div>
    `;

    // Información general
    const infoGeneral = document.createElement('div');
    infoGeneral.innerHTML = `
        <div style='font-size:1.1em;font-weight:700;color:#4299e1;margin-bottom:10px;'>INFORMACIÓN GENERAL</div>
        <div style='display:flex;flex-wrap:wrap;gap:18px 32px;font-size:1em;'>
            <div><b>Habitación:</b> ${room.number}</div>
            <div><b>Costo:</b> $${Number(room.precio).toLocaleString('es-CO')}</div>
            <div><b>Tipo:</b> ${room.type}</div>
            <div><b>Tamaño:</b> ${room.tamano}</div>
            <div><b>Capacidad:</b> ${room.capacidad ? room.capacidad + ' personas' : '-'}</div>
            <div><b>Estado:</b> ${room.status === 'en-uso' ? 'En uso' : room.status.charAt(0).toUpperCase() + room.status.slice(1)}</div>
        </div>
    `;

    // Botón cerrar
    const closeBtn = document.createElement('button');
    closeBtn.innerHTML = '✖';
    closeBtn.style.position = 'absolute';
    closeBtn.style.top = '18px';
    closeBtn.style.left = '18px';
    closeBtn.style.background = '#c82333';
    closeBtn.style.color = '#fff';
    closeBtn.style.border = 'none';
    closeBtn.style.borderRadius = '8px';
    closeBtn.style.fontSize = '2em';
    closeBtn.style.width = '44px';
    closeBtn.style.height = '44px';
    closeBtn.style.cursor = 'pointer';
    closeBtn.style.zIndex = '10001';
    closeBtn.addEventListener('click', () => overlay.remove());

    // Ensamblar
    infoDiv.appendChild(infoAdicional);
    infoDiv.appendChild(infoGeneral);
    modal.appendChild(imgDiv);
    modal.appendChild(infoDiv);
    overlay.appendChild(modal);
    overlay.appendChild(closeBtn);
    document.body.appendChild(overlay);
}

// Función para agregar nueva habitación
function addRoom() {
    const newRoomNumber = Math.max(...rooms.map(r => r.number)) + 1;
    const newRoom = {
        id: Date.now(),
        number: newRoomNumber,
        type: "••••••••",
        status: "disponible"
    };
    
    rooms.push(newRoom);
    
    // Re-renderizar según el filtro actual
    if (currentFilter === 'all') {
        renderRooms(rooms);
    } else {
        filterRooms(currentFilter);
    }
    
    // Animación de nueva habitación
    setTimeout(() => {
        const newCard = document.querySelector('.room-card:last-child');
        if (newCard) {
            newCard.style.transform = 'scale(1.05)';
            setTimeout(() => {
                newCard.style.transform = 'scale(1)';
            }, 200);
        }
    }, 100);
}

// Función para editar habitación (redirige al controlador para cargar el formulario con datos)
function editarHabitacion(room) {
    window.location.href = `../controllers/habitacionController.php?accion=editar&numero=${room.number}`;
}

// Inicializar la aplicación
document.addEventListener('DOMContentLoaded', function() {
    renderRooms();
    initializeFilters();
});