<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Colaboradores - LodgeHub</title>
    <!-- Bootstrap y Font Awesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    
    <!-- Estilos de los layouts -->
    <link href="../../public/assets/css/stylesNav.css" rel="stylesheet">
    <link rel="stylesheet" href="../../public/assets/css/stylesMisColaboradores.css">
</head>
<body>

    <?php
        include "layouts/sidebar.php";
        include "layouts/navbar.php";

        // --- INICIO: CONTROL DE ACCESO ---
        // Solo los administradores pueden acceder a esta página
        if (!isset($_SESSION['user']['roles']) || $_SESSION['user']['roles'] !== 'Administrador') {
            echo '<div class="container mt-5"><div class="alert alert-danger text-center"><h4><i class="fas fa-lock"></i> Acceso Denegado</h4><p>No tienes los permisos necesarios para gestionar colaboradores.</p><a href="homepage.php" class="btn btn-primary mt-3">Volver al Inicio</a></div></div>';
            exit(); // Detener la ejecución del script
        }
        // --- FIN: CONTROL DE ACCESO ---
    ?>
    <script src="../../public/assets/js/sidebar.js"></script>

    <div class="container">
        <!-- Campo oculto para que JS pueda leer el ID del hotel del admin -->
        <input type="hidden" id="admin-hotel-id" value="<?php echo htmlspecialchars($_SESSION['hotel_id'] ?? ''); ?>">

        <div class="header">
            <h1>Lista de Colaboradores</h1>
            <p>Gestiona colaboradores y usuarios del sistema LodgeHub</p>
        </div>

        <!-- Sección de búsqueda y filtros -->
        <div class="search-section" style="position: relative; z-index: 2;">
            <div class="row align-items-center mb-3">
                <div class="col-md-4">
                    <div class="input-group">
                        <input type="text" class="form-control" id="buscar-input" placeholder="Buscar por documento, nombre o correo...">
                        <button class="btn btn-outline-primary" type="button" id="buscar-btn">
                            <i class="fas fa-search"></i> Buscar
                        </button>
                    </div>
                </div>
                <div class="col-md-8 text-end">
                    <div class="btn-group me-2">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" >
                            <i class="fas fa-filter"></i> Filtros
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item filter-option" href="#" data-filter="all">Todos</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><h6 class="dropdown-header">Por Rol</h6></li>
                            <li><a class="dropdown-item filter-option" href="#" data-filter="Colaborador">Colaboradores</a></li>
                            <li><a class="dropdown-item filter-option" href="#" data-filter="Usuario">Usuarios</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><h6 class="dropdown-header">Por Sexo</h6></li>
                            <li><a class="dropdown-item filter-option" href="#" data-filter="Hombre">Hombre</a></li>
                            <li><a class="dropdown-item filter-option" href="#" data-filter="Mujer">Mujer</a></li>
                            <li><a class="dropdown-item filter-option" href="#" data-filter="Otro">Otro</a></li>
                        </ul>
                    </div>
                    <button class="btn btn-success" id="refresh-btn">
                        <i class="fas fa-sync-alt"></i> Actualizar
                    </button>
                    <a href="crearMisColaboradores.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Nuevo Colaborador
                    </a>
                </div>
            </div>
        </div>

        <!-- Mensajes -->
        <div id="success-message" class="success-message" style="display: none;">
            ✅ <strong id="success-text">Operación exitosa</strong>
        </div>

        <div id="error-message" class="error-message" style="display: none;">
            ❌ <strong id="error-text">Error en la operación</strong>
        </div>

        <!-- Tabla de Colaboradores -->
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Documento</th>
                        <th>Tipo Doc.</th>
                        <th>Nombres</th>
                        <th>Apellidos</th>
                        <th>Correo</th>
                        <th>Teléfono</th>
                        <th>Sexo</th>
                        <th>Fecha Nac.</th>
                        <th>Rol</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="tabla-colaboradores">
                    <tr>
                        <td colspan="10" class="loading">
                            <i class="fas fa-spinner fa-spin"></i> Cargando colaboradores...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        <nav aria-label="Paginación de Colaboradores" id="paginacion-container" style="display: none;">
            <ul class="pagination" id="paginacion">
                <!-- Generado dinámicamente -->
            </ul>
        </nav>
    </div>

    <!-- Modal de edición -->
    <div class="modal fade" id="editarModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-user-edit"></i> Editar Colaborador
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="form-editar">
                        <input type="hidden" id="edit-documento-original">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit-numDocumento" class="form-label">Número de Documento <span class="required">*</span></label>
                                    <input type="text" class="form-control" id="edit-numDocumento" name="numDocumento" required maxlength="15">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit-tipoDocumento" class="form-label">Tipo de Documento <span class="required">*</span></label>
                                    <select class="form-select" id="edit-tipoDocumento" name="tipoDocumento" required>
                                        <option value="Cédula de Ciudadanía">Cédula de Ciudadanía</option>
                                        <option value="Tarjeta de Identidad">Tarjeta de Identidad</option>
                                        <option value="Cedula de Extranjeria">Cédula de Extranjería</option>
                                        <option value="Pasaporte">Pasaporte</option>
                                        <option value="Registro Civil">Registro Civil</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit-nombres" class="form-label">Nombres <span class="required">*</span></label>
                                    <input type="text" class="form-control" id="edit-nombres" name="nombres" required maxlength="50">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit-apellidos" class="form-label">Apellidos <span class="required">*</span></label>
                                    <input type="text" class="form-control" id="edit-apellidos" name="apellidos" required maxlength="50">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit-correo" class="form-label">Correo Electrónico <span class="required">*</span></label>
                                    <input type="email" class="form-control" id="edit-correo" name="correo" required maxlength="255">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit-numTelefono" class="form-label">Teléfono <span class="required">*</span></label>
                                    <input type="tel" class="form-control" id="edit-numTelefono" name="numTelefono" required maxlength="15">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="edit-sexo" class="form-label">Sexo <span class="required">*</span></label>
                                    <select class="form-select" id="edit-sexo" name="sexo" required>
                                        <option value="Hombre">Hombre</option>
                                        <option value="Mujer">Mujer</option>
                                        <option value="Otro">Otro</option>
                                        <option value="Prefiero no decirlo">Prefiero no decirlo</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="edit-fechaNacimiento" class="form-label">Fecha de Nacimiento <span class="required">*</span></label>
                                    <input type="date" class="form-control" id="edit-fechaNacimiento" name="fechaNacimiento" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="edit-roles" class="form-label">Rol <span class="required">*</span></label>
                                    <select class="form-select" id="edit-roles" name="roles" required>
                                        <option value="Colaborador">Colaborador</option>
                                        <option value="Usuario">Usuario</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit-password" class="form-label">Nueva Contraseña</label>
                                    <input type="password" class="form-control" id="edit-password" name="password" maxlength="255">
                                    <small class="form-text text-muted">Déjalo vacío si no deseas cambiar la contraseña</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit-solicitarContraseña" class="form-label">Solicitar Cambio de Contraseña</label>
                                    <select class="form-select" id="edit-solicitarContraseña" name="solicitarContraseña">
                                        <option value="0">No</option>
                                        <option value="1">Sí</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="guardar-edicion">
                        <i class="fas fa-save"></i> Guardar Cambios
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de visualización completa -->
    <div class="modal fade" id="verModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-user"></i> Detalles del Colaborador
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="detalles-colaborador">
                    <!-- Contenido dinámico -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de confirmación de eliminación -->
    <div class="modal fade" id="eliminarModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-exclamation-triangle"></i> Confirmar Eliminación
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>¿Estás seguro de que deseas eliminar este colaborador?</p>
                    <div class="alert alert-info">
                        <strong id="eliminar-info">Información del Colaborador</strong><br>
                        <small>Documento: <span id="eliminar-documento">-</span></small>
                    </div>
                    <p class="text-danger">
                        <i class="fas fa-warning"></i> 
                        Esta acción no se puede deshacer.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="confirmar-eliminacion">
                        <i class="fas fa-trash"></i> Eliminar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de cambio de contraseña -->
    <div class="modal fade" id="cambiarPasswordModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-key"></i> Cambiar Contraseña
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="form-cambiar-password">
                        <input type="hidden" id="password-documento">
                        
                        <div class="mb-3">
                            <label for="nueva-password" class="form-label">Nueva Contraseña <span class="required">*</span></label>
                            <input type="password" class="form-control" id="nueva-password" name="nueva-password" required minlength="6">
                            <small class="form-text text-muted">Mínimo 6 caracteres</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="confirmar-password" class="form-label">Confirmar Contraseña <span class="required">*</span></label>
                            <input type="password" class="form-control" id="confirmar-password" name="confirmar-password" required>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="solicitar-cambio">
                                <label class="form-check-label" for="solicitar-cambio">
                                    Solicitar cambio de contraseña en el próximo inicio de sesión
                                </label>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="guardar-password">
                        <i class="fas fa-save"></i> Cambiar Contraseña
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../public/assets/js/listarColaboradores.js"></script>

</body>
</html>