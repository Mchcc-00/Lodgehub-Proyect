<?php
// Solo mostrar el modal si se accede correctamente
if (!isset($_SESSION['numDocumento'])) {
    exit();
}

// Obtener datos del usuario para el modal
try {
    $db = conexionDB();
    $stmt = $db->prepare("SELECT * FROM tp_usuarios WHERE numDocumento = ?");
    $stmt->execute([$_SESSION['numDocumento']]);
    $usuarioModal = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $usuarioModal = null;
}
?>

<!-- Modal Editar Perfil -->
<div class="modal fade" id="editarPerfilModal" tabindex="-1" aria-labelledby="editarPerfilModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editarPerfilModalLabel">
                    <i class="fas fa-edit me-2"></i>Editar Mi Perfil
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form id="formEditarPerfil" method="POST" action="procesar-editar-perfil.php">
                <div class="modal-body">
                    <!-- Alertas para el modal -->
                    <div id="modalAlert" class="alert d-none" role="alert"></div>
                    
                    <?php if ($usuarioModal): ?>
                    <div class="row g-3">
                        <!-- Nombres -->
                        <div class="col-md-6">
                            <label for="modalNombres" class="form-label">Nombres *</label>
                            <input type="text" class="form-control" id="modalNombres" name="nombres" 
                                   value="<?php echo htmlspecialchars($usuarioModal['nombres']); ?>" required>
                        </div>

                        <!-- Apellidos -->
                        <div class="col-md-6">
                            <label for="modalApellidos" class="form-label">Apellidos *</label>
                            <input type="text" class="form-control" id="modalApellidos" name="apellidos" 
                                   value="<?php echo htmlspecialchars($usuarioModal['apellidos']); ?>" required>
                        </div>

                        <!-- Correo -->
                        <div class="col-md-6">
                            <label for="modalCorreo" class="form-label">Correo Electrónico *</label>
                            <input type="email" class="form-control" id="modalCorreo" name="correo" 
                                   value="<?php echo htmlspecialchars($usuarioModal['correo']); ?>" required>
                        </div>

                        <!-- Teléfono -->
                        <div class="col-md-6">
                            <label for="modalTelefono" class="form-label">Número de Teléfono *</label>
                            <input type="tel" class="form-control" id="modalTelefono" name="numTelefono" 
                                   value="<?php echo htmlspecialchars($usuarioModal['numTelefono']); ?>" required>
                        </div>

                        <!-- Sexo -->
                        <div class="col-md-6">
                            <label for="modalSexo" class="form-label">Sexo</label>
                            <select class="form-select" id="modalSexo" name="sexo">
                                <option value="Hombre" <?php echo ($usuarioModal['sexo'] == 'Hombre') ? 'selected' : ''; ?>>Hombre</option>
                                <option value="Mujer" <?php echo ($usuarioModal['sexo'] == 'Mujer') ? 'selected' : ''; ?>>Mujer</option>
                                <option value="Otro" <?php echo ($usuarioModal['sexo'] == 'Otro') ? 'selected' : ''; ?>>Otro</option>
                                <option value="Prefiero no decirlo" <?php echo ($usuarioModal['sexo'] == 'Prefiero no decirlo') ? 'selected' : ''; ?>>Prefiero no decirlo</option>
                            </select>
                        </div>

                        <!-- Fecha de Nacimiento -->
                        <div class="col-md-6">
                            <label for="modalFechaNacimiento" class="form-label">Fecha de Nacimiento</label>
                            <input type="date" class="form-control" id="modalFechaNacimiento" name="fechaNacimiento" 
                                   value="<?php echo $usuarioModal['fechaNacimiento']; ?>">
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Error al cargar los datos del usuario.
                    </div>
                    <?php endif; ?>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary" id="btnGuardarPerfil">
                        <i class="fas fa-save me-2"></i>Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>