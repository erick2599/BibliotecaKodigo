<?php
// Aseguramos que la variable del préstamo buscado esté definida para evitar warnings
$prestamoBuscado = $prestamoBuscado ?? null;
?>

<div id="content">
    <!-- Formulario Principal de Préstamos -->
    <form method="POST" action="index.php?action=prestamos" class="crud-form">
        <!-- Control dinámico de acciones de envío -->
        <input type="hidden" name="action" id="form-action" value="<?= $prestamoBuscado ? 'editar_prestamo' : 'guardar_prestamo' ?>">

        <!-- Campo ID: Requerido solo si estás editando o buscando activamente -->
        <label for="id">ID de Préstamo:</label>
        <input type="text" id="id" name="id"
            placeholder="Ej. 1 (Obligatorio para buscar o editar)"
            value="<?= $prestamoBuscado ? htmlspecialchars($prestamoBuscado->getId()) : '' ?>"
            <?= $prestamoBuscado ? 'required' : '' ?>>

        <!-- Campos de Datos: Requeridos solo si se va a registrar uno nuevo -->
        <label for="id_usuario">ID del Usuario:</label>
        <input type="text" id="id_usuario" name="id_usuario"
            placeholder="Ej. 1"
            value="<?= $prestamoBuscado ? htmlspecialchars($prestamoBuscado->getUsuarioId()) : '' ?>"
            <?= $prestamoBuscado ? '' : 'required' ?>>

        <label for="id_libro">ID del Libro:</label>
        <input type="text" id="id_libro" name="id_libro"
            placeholder="Ej. 1"
            value="<?= $prestamoBuscado ? htmlspecialchars($prestamoBuscado->getLibroId()) : '' ?>"
            <?= $prestamoBuscado ? '' : 'required' ?>>

        <!-- DESPLEGABLE NATIVO DE FECHA: type="date" -->
        <label for="fecha_prestamo">Fecha de Préstamo:</label>
        <input type="date" id="fecha_prestamo" name="fecha_prestamo"
            value="<?= $prestamoBuscado ? htmlspecialchars($prestamoBuscado->getFechaPrestamo()) : '' ?>"
            <?= $prestamoBuscado ? '' : 'required' ?>>

        <!-- DESPLEGABLE NATIVO DE FECHA: type="date" -->
        <label for="fecha_devolucion">Fecha de Devolución:</label>
        <input type="date" id="fecha_devolucion" name="fecha_devolucion"
            value="<?= $prestamoBuscado ? htmlspecialchars($prestamoBuscado->getFechaDevolucion()) : '' ?>"
            <?= $prestamoBuscado ? '' : 'required' ?>>

        <!-- DESPLEGABLE DE SELECCIÓN (SELECT) PARA ESTADO -->
        <label for="estado">Estado del Préstamo:</label>
        <select id="estado" name="estado" class="crud-select" <?= $prestamoBuscado ? '' : 'required' ?>>
            <?php $estadoActual = $prestamoBuscado ? $prestamoBuscado->getEstado() : ''; ?>
            <option value="" disabled <?= empty($estadoActual) ? 'selected' : '' ?>>-- Seleccione un Estado --</option>
            <option value="activo" <?= $estadoActual === 'activo' ? 'selected' : '' ?>>Activo</option>
            <option value="devuelto" <?= $estadoActual === 'devuelto' ? 'selected' : '' ?>>Devuelto</option>
            <option value="vencido" <?= $estadoActual === 'vencido' ? 'selected' : '' ?>>Vencido</option>
        </select>

        <!-- Grupo de Botones de Acción Estilizados -->
        <div class="form-actions" style="margin-top: 15px;">
            <?php if ($prestamoBuscado): ?>
                <button type="submit" class="btn btn-success">Actualizar Cambios</button>
                <a href="index.php?action=prestamos" class="btn btn-secondary">Cancelar Edición</a>
            <?php else: ?>
                <button type="submit" class="btn btn-primary">Agregar Préstamo</button>
                <button type="submit" class="btn btn-primary" formnovalidate onclick="document.getElementById('form-action').value='buscar_prestamo';">Buscar Préstamo</button>
            <?php endif; ?>
        </div>
    </form>

    <?php $items = $biblioteca->obtenerPrestamosActivos(); ?>

    <!-- Tabla de Visualización de la Base de Datos -->
    <div class="table-responsive" style="margin-top: 30px;">
        <table class="crud-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>ID Usuario</th>
                    <th>ID Libro</th>
                    <th>Fecha de Préstamo</th>
                    <th>Fecha de Devolución</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($items)): ?>
                    <tr>
                        <td colspan="7" class="table-empty">
                            No hay préstamos activos registrados.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td><?= htmlspecialchars(is_array($item) ? $item['id'] : $item->getId()) ?></td>
                            <td><?= htmlspecialchars(is_array($item) ? $item['id_usuario'] : $item->getUsuarioId()) ?></td>
                            <td><?= htmlspecialchars(is_array($item) ? $item['id_libro'] : $item->getLibroId()) ?></td>
                            <td><?= htmlspecialchars(is_array($item) ? $item['fecha_prestamo'] : $item->getFechaPrestamo()) ?></td>
                            <td><?= htmlspecialchars(is_array($item) ? $item['fecha_devolucion'] : $item->getFechaDevolucion()) ?></td>
                            <td>
                                <span class="status-badge status-<?= htmlspecialchars(is_array($item) ? $item['estado'] : $item->getEstado()) ?>">
                                    <?= htmlspecialchars(ucfirst(is_array($item) ? $item['estado'] : $item->getEstado())) ?>
                                </span>
                            </td>
                            <td class="actions-cell">
                                <a href="index.php?action=editar_prestamo_form&id=<?= is_array($item) ? $item['id'] : $item->getId() ?>" class="link-edit">Editar</a>
                                <span class="divider">|</span>
                                <a href="index.php?action=eliminar_prestamo&id=<?= is_array($item) ? $item['id'] : $item->getId() ?>"
                                    onclick="return confirm('¿Estás seguro de eliminar este préstamo?');"
                                    class="link-delete">Eliminar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>