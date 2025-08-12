<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Huéspedes - LodgeHub</title>
    <!-- Bootstrap y Font Awesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    
    <!-- Estilos de los layouts -->
    <link href="../../public/assets/css/stylesNav.css" rel="stylesheet">
    <link rel="stylesheet" href="../../public/assets/css/stylesHuesped.css">
</head>
<body>

    <?php
        include "layouts/sidebar.php";
        include "layouts/navbar.php";
    ?>
    <script src="../../public/assets/js/sidebar.js"></script>

    <div class="container">
        <div class="header">
            <h1>Lista de Huéspedes</h1>
            <p>Gestiona todos los huéspedes registrados en el sistema</p>
        </div>

        <!-- Sección de búsqueda y acciones -->
        <div class="search-section">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="input-group">
                        <input type="text" class="form-control" id="buscar-input" placeholder="Buscar por nombre, apellido, documento o correo...">
                        <button class="btn btn-outline-primary" type="button" id="buscar-btn">
                            <i class="fas fa-search"></i> Buscar
                        </button>
                    </div>
                </div>
                <div class="col-md-6 text-end">
                    <button class="btn btn-success" id="refresh-btn">
                        <i class="fas fa-sync-alt"></i> Actualizar
                    </button>
                    <a href="crearHuesped.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Nuevo Huésped
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

        <!-- Tabla de huéspedes -->
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Documento</th>
                        <th>Nombres</th>
                        <th>Apellidos</th>
                        <th>Sexo</th>
                        <th>Teléfono</th>
                        <th>Correo</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="tabla-huespedes">
                    <tr>
                        <td colspan="7" class="loading">
                            <i class="fas fa-spinner fa-spin"></i> Cargando huéspedes...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        <nav aria-label="Paginación de huéspedes" id="paginacion-container" style="display: none;">
            <ul class="pagination" id="paginacion">
                <!-- Generado dinámicamente -->
            </ul>
        </nav>
    </div>

    <!-- Modal de edición -->
    <div class="modal fade" id="editarModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-edit"></i> Editar Huésped
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="form-editar">
                        <input type="hidden" id="edit-numDocumento">
                        
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
                                    <label for="edit-sexo" class="form-label">Sexo <span class="required">*</span></label>
                                    <select class="form-select" id="edit-sexo" name="sexo" required>
                                        <option value="Hombre">Hombre</option>
                                        <option value="Mujer">Mujer</option>
                                        <option value="Otro">Otro</option>
                                        <option value="Prefiero no decirlo">Prefiero no decirlo</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit-numTelefono" class="form-label">Teléfono <span class="required">*</span></label>
                                    <input type="tel" class="form-control" id="edit-numTelefono" name="numTelefono" required maxlength="15">
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit-correo" class="form-label">Correo Electrónico <span class="required">*</span></label>
                            <input type="email" class="form-control" id="edit-correo" name="correo" required maxlength="30">
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
                    <p>¿Estás seguro de que deseas eliminar al huésped:</p>
                    <div class="alert alert-info">
                        <strong id="eliminar-nombre">Nombre del huésped</strong><br>
                        <small>Documento: <span id="eliminar-documento">Número</span></small>
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

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../public/assets/js/listarHuespedes.js"></script>

</body>
</html>