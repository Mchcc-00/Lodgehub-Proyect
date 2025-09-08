<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Gestión de Mantenimiento</title>
    <!-- Bootstrap y Font Awesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <!-- SweetAlert2 para alertas mejoradas -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <link rel="stylesheet" href="../../public/assets/css/stylesMantenimiento.css">
    <link href="../../public/assets/css/stylesNav.css" rel="stylesheet">
</head>
<body>

    <?php
        include "layouts/sidebar.php";
        include "layouts/navbar.php";
        
        // Incluir conexión a base de datos
        include_once "../../config/conexionGlobal.php";
        
        // Obtener el id_hotel del usuario logueado (asumiendo que está en sesión)
        session_start();
        $id_hotel = $_SESSION['id_hotel'] ?? 1; // Valor por defecto si no está en sesión
    ?>
    <script src="../../public/assets/js/sidebar.js"></script>

    <div class="container">
        <div class="header">
            <h1>Sistema de Gestión de Mantenimiento</h1>
            <p>Gestiona y monitorea todas las tareas de mantenimiento del hotel</p>
        </div>

        <div class="main-content">
            <!-- Estadísticas -->
            <div class="stats">
                <div class="stat-card">
                    <div class="stat-number" style="color: #0d6efd;" id="totalTareas">0</div>
                    <div class="stat-label">Total Tareas</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number" style="color: #ffc107;" id="tareasPendientes">0</div>
                    <div class="stat-label">Pendientes</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number" style="color: #198754;" id="tareasFinalizadas">0</div>
                    <div class="stat-label">Finalizadas</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number" style="color: #dc3545;" id="tareasAltas">0</div>
                    <div class="stat-label">Prioridad Alta</div>
                </div>
            </div>

            <!-- Controles -->
            <div class="controls">
                <button class="btn btn-primary" onclick="abrirModal()">
                    ➕ Nueva Tarea
                </button>
                <button class="btn btn-success" onclick="exportarDatos()">
                    📊 Exportar
                </button>

                <div class="search-filter">
                    <div class="form-group">
                        <label>Buscar:</label>
                        <input type="text" class="form-control" id="buscar" placeholder="Buscar por habitación o descripción..." onkeyup="filtrarTabla()">
                    </div>
                    <div class="form-group">
                        <label>Estado:</label>
                        <select class="form-control" id="filtroEstado" onchange="filtrarTabla()">
                            <option value="">Todos</option>
                            <option value="Pendiente">Pendiente</option>
                            <option value="Finalizado">Finalizado</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Tipo:</label>
                        <select class="form-control" id="filtroTipo" onchange="filtrarTabla()">
                            <option value="">Todos</option>
                            <option value="Limpieza">Limpieza</option>
                            <option value="Estructura">Estructura</option>
                            <option value="Eléctrico">Eléctrico</option>
                            <option value="Otro">Otro</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Prioridad:</label>
                        <select class="form-control" id="filtroPrioridad" onchange="filtrarTabla()">
                            <option value="">Todas</option>
                            <option value="Alto">Alto</option>
                            <option value="Bajo">Bajo</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Tabla -->
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Habitación</th>
                            <th>Tipo</th>
                            <th>Descripción</th>
                            <th>Fecha Registro</th>
                            <th>Frecuencia</th>
                            <th>Prioridad</th>
                            <th>Responsable</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tablaTareas">
                        <!-- Los datos se cargarán aquí -->
                    </tbody>
                </table>
            </div>

            <div id="estadoVacio" class="empty-state" style="display: none;">
                <div style="font-size: 4rem; margin-bottom: 20px;">🔧</div>
                <h3>No hay tareas de mantenimiento</h3>
                <p>Comienza agregando una nueva tarea de mantenimiento</p>
                <button class="btn btn-primary" onclick="abrirModal()">➕ Agregar Primera Tarea</button>
            </div>
        </div>
    </div>

    <!-- Modal para agregar/editar tareas -->
    <div id="modalTarea" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="tituloModal">Nueva Tarea de Mantenimiento</h2>
                <button class="close" onclick="cerrarModal()">&times;</button>
            </div>

            <form id="formTarea">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="id_habitacion">Habitación *</label>
                        <select class="form-control" id="id_habitacion" required>
                            <option value="">Seleccionar habitación...</option>
                        </select>
                        <small class="text-danger" id="error-habitacion" style="display: none;"></small>
                    </div>

                    <div class="form-group">
                        <label for="tipo">Tipo de Mantenimiento *</label>
                        <select class="form-control" id="tipo" required>
                            <option value="">Seleccionar...</option>
                            <option value="Limpieza">Limpieza</option>
                            <option value="Estructura">Estructura</option>
                            <option value="Eléctrico">Eléctrico</option>
                            <option value="Otro">Otro</option>
                        </select>
                        <small class="text-danger" id="error-tipo" style="display: none;"></small>
                    </div>

                    <div class="form-group">
                        <label for="prioridad">Prioridad *</label>
                        <select class="form-control" id="prioridad" required>
                            <option value="">Seleccionar...</option>
                            <option value="Bajo">Bajo</option>
                            <option value="Alto">Alto</option>
                        </select>
                        <small class="text-danger" id="error-prioridad" style="display: none;"></small>
                    </div>

                    <div class="form-group">
                        <label for="estado">Estado</label>
                        <select class="form-control" id="estado">
                            <option value="Pendiente">Pendiente</option>
                            <option value="Finalizado">Finalizado</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="frecuencia">¿Es Frecuente? *</label>
                        <select class="form-control" id="frecuencia" onchange="toggleCantFrecuencia()" required>
                            <option value="">Seleccionar...</option>
                            <option value="Sí">Sí</option>
                            <option value="No">No</option>
                        </select>
                        <small class="text-danger" id="error-frecuencia" style="display: none;"></small>
                    </div>

                    <div class="form-group" id="grupoCantFrecuencia">
                        <label for="cantFrecuencia">Frecuencia *</label>
                        <select class="form-control" id="cantFrecuencia" required>
                            <option value="">Seleccionar...</option>
                            <option value="Diario">Diario</option>
                            <option value="Semanal">Semanal</option>
                            <option value="Quincenal">Quincenal</option>
                            <option value="Mensual">Mensual</option>
                        </select>
                        <small class="text-danger" id="error-cant-frecuencia" style="display: none;"></small>
                    </div>
                </div>

                <div class="form-group">
                    <label for="problemaDescripcion">Descripción del Problema *</label>
                    <textarea class="form-control" id="problemaDescripcion" rows="3" maxlength="50" placeholder="Describe el problema o tarea a realizar..." required></textarea>
                    <small class="text-muted">Máximo 50 caracteres. Quedan: <span id="caracteres-restantes">50</span></small>
                    <small class="text-danger" id="error-descripcion" style="display: none;"></small>
                </div>

                <div class="form-group">
                    <label for="numDocumento">Documento del Responsable *</label>
                    <select class="form-control" id="numDocumento" required>
                        <option value="">Seleccionar responsable...</option>
                    </select>
                    <small class="text-danger" id="error-documento" style="display: none;"></small>
                </div>

                <div style="text-align: right; margin-top: 25px;">
                    <button type="button" class="btn" onclick="cerrarModal()" style="background: #6c757d; color: white; margin-right: 10px;">Cancelar</button>
                    <button type="submit" class="btn btn-success" id="btnGuardar">💾 Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Variables globales
        let tareas = [];
        let habitaciones = [];
        let usuarios = [];
        let editandoId = null;
        const idHotel = <?php echo $id_hotel; ?>;

        // Función para cargar habitaciones
        async function cargarHabitaciones() {
            try {
                const response = await fetch('../../controllers/mantenimientoController.php?id_hotel=' + idHotel);
                const data = await response.json();
                
                const select = document.getElementById('id_habitacion');
                select.innerHTML = '<option value="">Seleccionar habitación...</option>';
                
                if (data.success) {
                    habitaciones = data.habitaciones;
                    habitaciones.forEach(hab => {
                        select.innerHTML += `<option value="${hab.id}">${hab.numero}</option>`;
                    });
                }
            } catch (error) {
                console.error('Error cargando habitaciones:', error);
                mostrarError('Error al cargar las habitaciones');
            }
        }

        // Función para cargar usuarios
       async function cargarHabitaciones() {
    try {
        const response = await fetch(`../../controllers/mantenimientoController.php?accion=obtener_habitaciones&id_hotel=${idHotel}`);
        const data = await response.json();
        
        const select = document.getElementById('id_habitacion');
        select.innerHTML = '<option value="">Seleccionar habitación...</option>';
        
        if (data.success) {
            habitaciones = data.habitaciones;
            habitaciones.forEach(hab => {
                select.innerHTML += `<option value="${hab.id}">${hab.numero}</option>`;
            });
        } else {
            console.error('Error:', data.message);
            mostrarError(data.message || 'Error al cargar las habitaciones');
        }
    } catch (error) {
        console.error('Error cargando habitaciones:', error);
        mostrarError('Error al cargar las habitaciones');
    }
}

// Función para cargar usuarios CORREGIDA
async function cargarUsuarios() {
    try {
        const response = await fetch(`../../controllers/mantenimientoController.php?accion=obtener_usuarios&id_hotel=${idHotel}`);
        const data = await response.json();
        
        const select = document.getElementById('numDocumento');
        select.innerHTML = '<option value="">Seleccionar responsable...</option>';
        
        if (data.success) {
            usuarios = data.usuarios;
            usuarios.forEach(user => {
                select.innerHTML += `<option value="${user.numDocumento}">${user.nombres} ${user.apellidos} (${user.numDocumento})</option>`;
            });
        } else {
            console.error('Error:', data.message);
            mostrarError(data.message || 'Error al cargar los usuarios');
        }
    } catch (error) {
        console.error('Error cargando usuarios:', error);
        mostrarError('Error al cargar los usuarios');
    }
}

// Función para cargar tareas CORREGIDA
async function cargarTabla() {
    try {
        const response = await fetch(`../../controllers/mantenimientoController.php?accion=obtener_mantenimientos&id_hotel=${idHotel}`);
        const data = await response.json();
        
        if (data.success) {
            tareas = data.mantenimientos;
            mostrarTabla();
        } else {
            console.error('Error:', data.message);
            mostrarError(data.message || 'Error al cargar las tareas');
        }
    } catch (error) {
        console.error('Error cargando tareas:', error);
        mostrarError('Error al cargar las tareas');
    }
}

// Función eliminar tarea CORREGIDA
async function eliminarTarea(id) {
    const resultado = await Swal.fire({
        title: '¿Estás seguro?',
        text: "Esta acción no se puede deshacer",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    });

    if (resultado.isConfirmed) {
        try {
            const response = await fetch('../../controllers/mantenimientoController.php?accion=eliminar', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ 
                    id: id, 
                    id_hotel: idHotel 
                })
            });

            const data = await response.json();

            if (data.success) {
                Swal.fire('¡Eliminado!', data.message, 'success');
                cargarTabla();
            } else {
                Swal.fire('Error', data.message, 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            Swal.fire('Error', 'Error al eliminar la tarea', 'error');
        }
    }
}
    </script>
</body>
</html>