<?php $usuarioBuscado = $usuarioBuscado ?? null; ?>

<div id="content">
    <!-- Formulario de Usuarios -->
    <div id="content">
        <!-- FORMULARIO CORREGIDO: Aseguramos que apunte a index.php manteniendo la sección activa -->
        <form method="POST" action="index.php?action=usuarios" class="crud-form">

            <!-- Control dinámico de la acción (guardar_usuario o editar_usuario) -->
            <input type="hidden" name="action" id="form-action" value="<?= $usuarioBuscado ? 'editar_usuario' : 'guardar_usuario' ?>">

            <!-- Campo ID: Siempre oculto o visible pero obligatorio para actualizar -->
            <label for="id">ID del Usuario:</label>
            <input type="text" id="id" name="id"
                placeholder="Ej. 1 (Obligatorio para buscar o editar)"
                value="<?= $usuarioBuscado ? htmlspecialchars($usuarioBuscado->getId()) : '' ?>"
                <?= $usuarioBuscado ? 'required' : '' ?>>

            <!-- Campos de Datos: Revisa que los 'name' coincidan en mayúsculas exactamente con tu index.php -->
            <label for="Nombre">Nombre Completo:</label>
            <input type="text" id="Nombre" name="Nombre"
                placeholder="Ej. Juan Pérez"
                value="<?= $usuarioBuscado ? htmlspecialchars($usuarioBuscado->getNombre()) : '' ?>"
                <?= $usuarioBuscado ? '' : 'required' ?>>

            <label for="Email">Correo Electrónico:</label>
            <input type="email" id="Email" name="Email"
                placeholder="Ej. juan.perez@example.com"
                value="<?= $usuarioBuscado ? htmlspecialchars($usuarioBuscado->getEmail()) : '' ?>"
                <?= $usuarioBuscado ? '' : 'required' ?>>

            <label for="Telefono">Teléfono de Contacto:</label>
            <input type="tel" id="Telefono" name="Telefono"
                placeholder="Ej. 123-456-7890"
                value="<?= $usuarioBuscado ? htmlspecialchars($usuarioBuscado->getTelefono()) : '' ?>"
                <?= $usuarioBuscado ? '' : 'required' ?>>

            <!-- Botones de Acción -->
            <div class="form-actions" style="margin-top: 15px;">
                <?php if ($usuarioBuscado): ?>
                    <!-- Botón de actualización: Tipo submit obligatorio para disparar el método POST -->
                    <button type="submit" class="btn btn-success">Actualizar Cambios</button>
                    <a href="index.php?action=usuarios" class="btn btn-secondary">Cancelar Edición</a>
                <?php else: ?>
                    <button type="submit" class="btn btn-primary">Agregar Usuario</button>
                    <button type="submit" class="btn btn-primary" formnovalidate onclick="document.getElementById('form-action').value='buscar_usuario';">Buscar Usuario</button>
                <?php endif; ?>
            </div>
        </form>


        <?php $usuarios = $biblioteca->obtenerUsuarios(); ?>


        <div class="table-responsive" style="margin-top: 30px;">
            <table class="crud-table">
                <thead>
                    <tr>
                        <th>ID Usuario</th>
                        <th>Nombre</th>
                        <th>Correo Electrónico</th>
                        <th>Teléfono</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($usuarios)): ?>
                        <tr>
                            <td colspan="5" class="table-empty">
                                No hay usuarios registrados en la biblioteca.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($usuarios as $user): ?>
                            <tr>
                                <td><?= htmlspecialchars($user->getId()) ?></td>
                                <td><strong><?= htmlspecialchars($user->getNombre()) ?></strong></td>

                                <td><?= htmlspecialchars($user->getEmail()) ?></td>
                                <td><?= htmlspecialchars($user->getTelefono()) ?></td>
                                <td class="actions-cell">
                                    <a href="index.php?action=editar_usuario_form&id=<?= $user->getId() ?>" class="link-edit">Editar</a>
                                    <span class="divider">|</span>
                                    <a href="index.php?action=eliminar_usuario&id=<?= $user->getId() ?>"
                                        onclick="return confirm('¿Estás seguro de que deseas eliminar permanentemente a este usuario?');"
                                        class="link-delete">Eliminar</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>

        </div>
    </div>