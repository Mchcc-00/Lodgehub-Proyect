<div class="user-header">
    <h2 class="form-title">Lista de Usuarios</h2>
    <a href="<?php echo BASE_URL; ?>/usuarios/crear" class="btn-add" title="Agregar Usuario">
        <i class="fas fa-plus"></i>
    </a>
</div>

<table class="user-table">
    <thead class="user-table-header">
        <tr>
            <th>Tipo de documento</th>
            <th>Número de documento</th>
            <th>Nombres</th>
            <th>Apellidos</th>
            <th>Sexo</th>
            <th>Teléfono</th>
            <th>Correo</th>
            <th>Rol</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody class="user-table-body">
        
        <?php if (empty($usuarios)): ?>
            <tr>
                <td colspan="9" style="text-align: center;">No hay usuarios registrados.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($usuarios as $usuario): ?>
                <tr>
                    <td><?php echo htmlspecialchars($usuario['tipo_documento']); ?></td>
                    <td><?php echo htmlspecialchars($usuario['numDocumento']); ?></td>
                    <td><?php echo htmlspecialchars($usuario['nombres']); ?></td>
                    <td><?php echo htmlspecialchars($usuario['apellidos']); ?></td>
                    <td><?php echo htmlspecialchars($usuario['sexo']); ?></td>
                    <td><?php echo htmlspecialchars($usuario['numTelefono']); ?></td>
                    <td><?php echo htmlspecialchars($usuario['correo']); ?></td>
                    <td><?php echo htmlspecialchars($usuario['rol']); ?></td>
                    <td class="action-links">
                        <a href="<?php echo BASE_URL; ?>/usuarios/editar?id=<?php echo htmlspecialchars($usuario['numDocumento']); ?>" class="btn-edit" title="Editar"><i class="fas fa-edit"></i></a>

                        <a href="<?php echo BASE_URL; ?>/usuarios/eliminar?id=<?php echo htmlspecialchars($usuario['numDocumento']); ?>" class="btn-delete" title="Eliminar" onclick="return confirm('¿Estás seguro de que quieres eliminar este usuario?');"><i class="fas fa-trash-alt"></i></a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<footer class="form-footer">
    lodgehubgroup © 2025
</footer>