<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Gestión de Mantenimiento</title>
        <!-- Bootstrap y Font Awesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <link rel="stylesheet" href="../../public/assets/css/stylesMantenimiento.css">
    

    <link href="../../public/assets/css/stylesNav.css" rel="stylesheet">
</head>
<body>

    <?php
        include "layouts/sidebar.php";
        include "layouts/navbar.php";
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
                        <label for="numeroHabitacion">Número de Habitación *</label>
                        <input type="text" class="form-control" id="numeroHabitacion" maxlength="5" required>
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
                    </div>

                    <div class="form-group">
                        <label for="prioridad">Prioridad *</label>
                        <select class="form-control" id="prioridad" required>
                            <option value="">Seleccionar...</option>
                            <option value="Bajo">Bajo</option>
                            <option value="Alto">Alto</option>
                        </select>
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
                    </div>
                </div>

                <div class="form-group">
                    <label for="problemaDescripcion">Descripción del Problema *</label>
                    <textarea class="form-control" id="problemaDescripcion" rows="3" maxlength="50" placeholder="Describe el problema o tarea a realizar..." required></textarea>
                </div>

                <div class="form-group">
                    <label for="numDocumento">Documento del Responsable *</label>
                    <input type="text" class="form-control" id="numDocumento" maxlength="15" placeholder="Número de documento del responsable" required>
                </div>

                <div style="text-align: right; margin-top: 25px;">
                    <button type="button" class="btn" onclick="cerrarModal()" style="background: #6c757d; color: white; margin-right: 10px;">Cancelar</button>
                    <button type="submit" class="btn btn-success">💾 Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Datos de ejemplo (simulando base de datos)
        let tareas = [
            {
                id: 1,
                numeroHabitacion: '101',
                tipo: 'Limpieza',
                problemaDescripcion: 'Limpieza profunda de baño',
                fechaRegistro: '2025-01-15 10:30:00',
                ultimaActualizacion: '2025-01-15 10:30:00',
                frecuencia: 'Sí',
                cantFrecuencia: 'Diario',
                prioridad: 'Alto',
                numDocumento: '12345678',
                estado: 'Pendiente'
            },
            {
                id: 2,
                numeroHabitacion: '102',
                tipo: 'Eléctrico',
                problemaDescripcion: 'Reparar lámpara del techo',
                fechaRegistro: '2025-01-14 14:20:00',
                ultimaActualizacion: '2025-01-14 14:20:00',
                frecuencia: 'No',
                cantFrecuencia: 'Mensual',
                prioridad: 'Bajo',
                numDocumento: '87654321',
                estado: 'Finalizado'
            },
            {
                id: 3,
                numeroHabitacion: '103',
                tipo: 'Estructura',
                problemaDescripcion: 'Pintar paredes',
                fechaRegistro: '2025-01-13 09:15:00',
                ultimaActualizacion: '2025-01-13 09:15:00',
                frecuencia: 'No',
                cantFrecuencia: 'Quincenal',
                prioridad: 'Alto',
                numDocumento: '11223344',
                estado: 'Pendiente'
            }
        ];

        let editandoId = null;

        function cargarTabla() {
            const tbody = document.getElementById('tablaTareas');
            const estadoVacio = document.getElementById('estadoVacio');
            const tablaContainer = document.querySelector('.table-container');
            
            if (tareas.length === 0) {
                tablaContainer.style.display = 'none';
                estadoVacio.style.display = 'block';
                return;
            }
            
            tablaContainer.style.display = 'block';
            estadoVacio.style.display = 'none';
            
            tbody.innerHTML = tareas.map(tarea => `
                <tr>
                    <td><strong>#${tarea.id}</strong></td>
                    <td><strong>${tarea.numeroHabitacion}</strong></td>
                    <td><span class="badge badge-${tarea.tipo.toLowerCase()}">${tarea.tipo}</span></td>
                    <td>${tarea.problemaDescripcion}</td>
                    <td>${formatearFecha(tarea.fechaRegistro)}</td>
                    <td>${tarea.frecuencia === 'Sí' ? tarea.cantFrecuencia : 'No frecuente'}</td>
                    <td><span class="badge badge-${tarea.prioridad.toLowerCase()}">${tarea.prioridad}</span></td>
                    <td>${tarea.numDocumento}</td>
                    <td><span class="badge badge-${tarea.estado.toLowerCase()}">${tarea.estado}</span></td>
                    <td>
                        <button class="btn btn-warning" onclick="editarTarea(${tarea.id})" style="padding: 6px 12px; margin-right: 5px;">✏️</button>
                        <button class="btn btn-danger" onclick="eliminarTarea(${tarea.id})" style="padding: 6px 12px;">🗑️</button>
                    </td>
                </tr>
            `).join('');
            
            actualizarEstadisticas();
        }

        function actualizarEstadisticas() {
            document.getElementById('totalTareas').textContent = tareas.length;
            document.getElementById('tareasPendientes').textContent = tareas.filter(t => t.estado === 'Pendiente').length;
            document.getElementById('tareasFinalizadas').textContent = tareas.filter(t => t.estado === 'Finalizado').length;
            document.getElementById('tareasAltas').textContent = tareas.filter(t => t.prioridad === 'Alto').length;
        }

        function formatearFecha(fecha) {
            return new Date(fecha).toLocaleDateString('es-ES') + ' ' + new Date(fecha).toLocaleTimeString('es-ES', {hour: '2-digit', minute: '2-digit'});
        }

        function abrirModal(id = null) {
            editandoId = id;
            const modal = document.getElementById('modalTarea');
            const titulo = document.getElementById('tituloModal');
            const form = document.getElementById('formTarea');
            
            if (id) {
                const tarea = tareas.find(t => t.id === id);
                titulo.textContent = 'Editar Tarea de Mantenimiento';
                
                document.getElementById('numeroHabitacion').value = tarea.numeroHabitacion;
                document.getElementById('tipo').value = tarea.tipo;
                document.getElementById('problemaDescripcion').value = tarea.problemaDescripcion;
                document.getElementById('frecuencia').value = tarea.frecuencia;
                document.getElementById('cantFrecuencia').value = tarea.cantFrecuencia;
                document.getElementById('prioridad').value = tarea.prioridad;
                document.getElementById('numDocumento').value = tarea.numDocumento;
                document.getElementById('estado').value = tarea.estado;
                
                toggleCantFrecuencia();
            } else {
                titulo.textContent = 'Nueva Tarea de Mantenimiento';
                form.reset();
                document.getElementById('estado').value = 'Pendiente';
                toggleCantFrecuencia();
            }
            
            modal.style.display = 'block';
        }

        function cerrarModal() {
            document.getElementById('modalTarea').style.display = 'none';
            editandoId = null;
        }

        function toggleCantFrecuencia() {
            const frecuencia = document.getElementById('frecuencia').value;
            const grupo = document.getElementById('grupoCantFrecuencia');
            const cantFrecuencia = document.getElementById('cantFrecuencia');
            
            if (frecuencia === 'No') {
                grupo.style.display = 'none';
                cantFrecuencia.removeAttribute('required');
            } else {
                grupo.style.display = 'block';
                cantFrecuencia.setAttribute('required', 'required');
            }
        }

        function editarTarea(id) {
            abrirModal(id);
        }

        function eliminarTarea(id) {
            if (confirm('¿Estás seguro de que deseas eliminar esta tarea?')) {
                tareas = tareas.filter(t => t.id !== id);
                cargarTabla();
            }
        }

        function filtrarTabla() {
            const buscar = document.getElementById('buscar').value.toLowerCase();
            const estado = document.getElementById('filtroEstado').value;
            const tipo = document.getElementById('filtroTipo').value;
            const prioridad = document.getElementById('filtroPrioridad').value;
            
            const filas = document.querySelectorAll('#tablaTareas tr');
            
            filas.forEach(fila => {
                const textoFila = fila.textContent.toLowerCase();
                const cumpleBusqueda = textoFila.includes(buscar);
                const cumpleEstado = !estado || fila.textContent.includes(estado);
                const cumpleTipo = !tipo || fila.textContent.includes(tipo);
                const cumplePrioridad = !prioridad || fila.textContent.includes(prioridad);
                
                if (cumpleBusqueda && cumpleEstado && cumpleTipo && cumplePrioridad) {
                    fila.style.display = '';
                } else {
                    fila.style.display = 'none';
                }
            });
        }

        function exportarDatos() {
            const csv = [
                ['ID', 'Habitación', 'Tipo', 'Descripción', 'Fecha Registro', 'Frecuencia', 'Cant. Frecuencia', 'Prioridad', 'Documento', 'Estado'],
                ...tareas.map(t => [
                    t.id, t.numeroHabitacion, t.tipo, t.problemaDescripcion,
                    t.fechaRegistro, t.frecuencia, t.cantFrecuencia,
                    t.prioridad, t.numDocumento, t.estado
                ])
            ].map(row => row.join(',')).join('\n');
            
            const blob = new Blob([csv], { type: 'text/csv' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'mantenimiento_' + new Date().toISOString().split('T')[0] + '.csv';
            a.click();
            URL.revokeObjectURL(url);
        }

        // Manejar envío del formulario
        document.getElementById('formTarea').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const datos = {
                numeroHabitacion: document.getElementById('numeroHabitacion').value,
                tipo: document.getElementById('tipo').value,
                problemaDescripcion: document.getElementById('problemaDescripcion').value,
                frecuencia: document.getElementById('frecuencia').value,
                cantFrecuencia: document.getElementById('cantFrecuencia').value,
                prioridad: document.getElementById('prioridad').value,
                numDocumento: document.getElementById('numDocumento').value,
                estado: document.getElementById('estado').value
            };
            
            if (editandoId) {
                // Editar tarea existente
                const indice = tareas.findIndex(t => t.id === editandoId);
                tareas[indice] = {
                    ...tareas[indice],
                    ...datos,
                    ultimaActualizacion: new Date().toISOString().replace('T', ' ').substring(0, 19)
                };
            } else {
                // Crear nueva tarea
                const nuevaTarea = {
                    id: Math.max(...tareas.map(t => t.id), 0) + 1,
                    ...datos,
                    fechaRegistro: new Date().toISOString().replace('T', ' ').substring(0, 19),
                    ultimaActualizacion: new Date().toISOString().replace('T', ' ').substring(0, 19)
                };
                tareas.push(nuevaTarea);
            }
            
            cargarTabla();
            cerrarModal();
        });

        // Cerrar modal al hacer clic fuera de él
        window.onclick = function(event) {
            const modal = document.getElementById('modalTarea');
            if (event.target === modal) {
                cerrarModal();
            }
        }

        // Cargar datos iniciales
        cargarTabla();
    </script>
</body>
</html>