<?php
require_once 'validarSesion.php';
$currentPage = 'Huespedes';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Huéspedes - LodgeHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link href="../../public/assets/css/stylesNav.css" rel="stylesheet">
    <link rel="stylesheet" href="../../public/assets/css/stylesHuesped.css">
</head>

<body>

    <?php
    include "layouts/sidebar.php";
    include "layouts/navbar.php";
    $hotelSeleccionado = isset($_SESSION['hotel_id']) && !empty($_SESSION['hotel_id']);
    ?>
    <script src="../../public/assets/js/sidebar.js"></script>

    <div class="container">
        <?php if (!$hotelSeleccionado): ?>
            <div class="alert alert-danger mt-4" role="alert">
                <h4 class="alert-heading"><i class="fas fa-exclamation-triangle"></i> ¡Acción Requerida!</h4>
                <p>Para poder ver la lista de huéspedes, primero debes <strong>seleccionar un hotel</strong> desde el panel principal (Home).</p>
                <hr>
                <p class="mb-0">Por favor, regresa al <a href="homepage.php" class="alert-link">Home</a> y elige el hotel donde deseas trabajar.</p>
            </div>
        <?php else: ?>
            <div class="header">
                <h1><i class="fas fa-user-friends"></i> Lista de Huéspedes</h1>
                <p>Gestiona todos los huéspedes registrados en tu hotel.</p>
            </div>

            <!-- Mensajes de alerta -->
            <div id="success-message" class="success-message" style="display: none;">✅ <strong id="success-text"></strong></div>
            <div id="error-message" class="error-message" style="display: none;">❌ <strong id="error-text"></strong></div>

            <div class="search-section">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <div class="input-group">
                            <input type="text" id="buscar-input" class="form-control" placeholder="Buscar por nombre, documento o correo...">
                            <button class="btn btn-primary" id="buscar-btn"><i class="fas fa-search"></i> Buscar</button>
                        </div>
                    </div>
                    <div class="col-md-6 text-end">
                        <button class="btn btn-secondary" id="refresh-btn"><i class="fas fa-sync-alt"></i> Actualizar</button>
                        <a href="crearHuesped.php" class="btn btn-primary"><i class="fas fa-plus"></i> Crear Huésped</a>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Huésped</th>
                            <th>Documento</th>
                            <th>Contacto</th>
                            <th>Sexo</th>
                            <th>Fecha Registro</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tabla-huespedes">
                        <!-- Filas generadas por JS -->
                    </tbody>
                </table>
            </div>

            <div id="paginacion-container">
                <ul class="pagination justify-content-center" id="paginacion">
                    <!-- Paginación generada por JS -->
                </ul>
            </div>
        <?php endif; ?>
    </div>

    <!-- Modal para Editar Huésped -->
    <div class="modal fade" id="editarModal" tabindex="-1" aria-labelledby="editarModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editarModalLabel"><i class="fas fa-edit"></i> Editar Huésped</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="form-editar-huesped" novalidate>
                        <input type="hidden" id="edit-numDocumentoOriginal" name="numDocumentoOriginal">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="edit-tipoDocumento" class="form-label">Tipo Documento <span class="required">*</span></label>
                                <select id="edit-tipoDocumento" name="tipoDocumento" class="form-select" required>
                                    <option value="Cédula de Ciudadanía">Cédula de Ciudadanía</option>
                                    <option value="Tarjeta de Identidad">Tarjeta de Identidad</option>
                                    <option value="Cedula de Extranjeria">Cédula de Extranjería</option>
                                    <option value="Pasaporte">Pasaporte</option>
                                    <option value="Registro Civil">Registro Civil</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="edit-numDocumento" class="form-label">N° Documento <span class="required">*</span></label>
                                <input type="text" id="edit-numDocumento" name="numDocumento" class="form-control" required>
                                <div id="edit-documento-feedback" class="documento-feedback" style="display: none;"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="edit-nombres" class="form-label">Nombres <span class="required">*</span></label>
                                <input type="text" id="edit-nombres" name="nombres" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label for="edit-apellidos" class="form-label">Apellidos <span class="required">*</span></label>
                                <input type="text" id="edit-apellidos" name="apellidos" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label for="edit-correo" class="form-label">Correo <span class="required">*</span></label>
                                <input type="email" id="edit-correo" name="correo" class="form-control" required>
                                <div id="edit-correo-feedback" class="correo-feedback" style="display: none;"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="edit-numTelefono" class="form-label">Teléfono <span class="required">*</span></label>
                                <input type="tel" id="edit-numTelefono" name="numTelefono" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label for="edit-sexo" class="form-label">Sexo <span class="required">*</span></label>
                                <select id="edit-sexo" name="sexo" class="form-select" required>
                                    <option value="Hombre">Hombre</option>
                                    <option value="Mujer">Mujer</option>
                                    <option value="Otro">Otro</option>
                                    <option value="Prefiero no decirlo">Prefiero no decirlo</option>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="guardar-edicion">Guardar Cambios</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Ver Huésped -->
    <div class="modal fade" id="verModal" tabindex="-1" aria-labelledby="verModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="verModalLabel"><i class="fas fa-eye"></i> Detalles del Huésped</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="detalles-huesped">
                    <!-- Contenido dinámico generado por JS -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Eliminar Huésped -->
    <div class="modal fade" id="eliminarModal" tabindex="-1" aria-labelledby="eliminarModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="eliminarModalLabel"><i class="fas fa-exclamation-triangle"></i> Confirmar Eliminación</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>¿Estás seguro de que deseas eliminar al huésped?</p>
                    <div class="alert alert-info">
                        <strong>Huésped:</strong> <span id="eliminar-info"></span><br>
                        <strong>Documento:</strong> <span id="eliminar-id"></span>
                    </div>
                    <p class="text-danger"><strong>Advertencia:</strong> Esta acción no se puede deshacer. Si el huésped tiene reservas asociadas, no podrá ser eliminado.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="confirmar-eliminacion">Sí, Eliminar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../public/assets/js/listarHuespedes.js"></script>
</body>

</html>