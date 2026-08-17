<div id="content">
    <!-- Formulario Principal de Libros con la clase CSS correcta -->
    <form method="POST" action="index.php?action=libros" class="crud-form">
        <!-- El id="form-action" permite cambiar la acción con JavaScript de forma segura -->
        <input type="hidden" name="action" id="form-action" value="<?= $libroBuscado ? 'editar_libro' : 'guardar_libro' ?>">

        <!-- Campo ID: Obligatorio solo en modo edición o búsqueda -->
        <label for="id">ID del Libro:</label>
        <input type="text" id="id" name="id"
            placeholder="Ej. 1 (Obligatorio para buscar o editar)"
            value="<?= $libroBuscado ? htmlspecialchars($libroBuscado->getId()) : '' ?>"
            <?= $libroBuscado ? 'required' : '' ?>>

        <!-- Los siguientes campos SOLO son requeridos si NO estamos buscando o editando un libro -->
        <label for="titulo">Título:</label>
        <input type="text" id="titulo" name="titulo"
            placeholder="Ej. Cien años de soledad"
            value="<?= $libroBuscado ? htmlspecialchars($libroBuscado->getTitulo()) : '' ?>"
            <?= $libroBuscado ? '' : 'required' ?>>

        <label for="autor">Autor:</label>
        <input type="text" id="autor" name="autor"
            placeholder="Ej. Gabriel García Márquez"
            value="<?= $libroBuscado ? htmlspecialchars($libroBuscado->getAutor()) : '' ?>"
            <?= $libroBuscado ? '' : 'required' ?>>

        <label for="isbn">ISBN:</label>
        <input type="text" id="isbn" name="isbn"
            placeholder="Ej. 978-3-16-148410-0"
            value="<?= $libroBuscado ? htmlspecialchars($libroBuscado->getIsbn()) : '' ?>"
            <?= $libroBuscado ? '' : 'required' ?>>

        <label for="cantidad">Cantidad:</label>
        <input type="number" id="cantidad" name="cantidad" min="1"
            placeholder="Ej. 5"
            value="<?= $libroBuscado ? htmlspecialchars($libroBuscado->getCantidad()) : '' ?>"
            <?= $libroBuscado ? '' : 'required' ?>>

        <!-- Grupo de Botones de Acción Estilizados -->
        <div class="form-actions" style="margin-top: 15px;">
            <?php if ($libroBuscado): ?>
                <button type="submit" class="btn btn-success">Actualizar Cambios</button>
                <a href="index.php?action=libros" class="btn btn-secondary">Cancelar Edición</a>
            <?php else: ?>
                <!-- Botón primario morado consistente con el diseño general -->
                <button type="submit" class="btn btn-primary">Agregar Libro</button>
                <button type="submit" class="btn btn-primary" formnovalidate onclick="document.getElementById('form-action').value='buscar_libro';">Buscar Libro</button>
            <?php endif; ?>
        </div>
    </form>

    <?php $items = $biblioteca->obtenerLibros(); ?>

    <!-- Tabla con las clases semánticas correctas -->
    <div class="table-responsive" style="margin-top: 30px;">
        <table class="crud-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Título</th>
                    <th>Autor</th>
                    <th>ISBN</th>
                    <th>Cantidad</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($items)): ?>
                    <tr>
                        <td colspan="6" class="table-empty">
                            No hay libros registrados en la biblioteca.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td><?= htmlspecialchars($item->getId()) ?></td>
                            <td><strong><?= htmlspecialchars($item->getTitulo()) ?></strong></td>
                            <td><?= htmlspecialchars($item->getAutor()) ?></td>
                            <td><?= htmlspecialchars($item->getIsbn()) ?></td>
                            <td><?= htmlspecialchars($item->getCantidad()) ?></td>
                            <td class="actions-cell">
                                <!-- Enlace de edición directo vía GET integrado al igual que en usuarios -->
                                <a href="index.php?action=editar_libro_form&id=<?= $item->getId() ?>" class="link-edit">Editar</a>
                                <span class="divider">|</span>
                                <!-- Enlace de eliminación con confirmación nativa -->
                                <a href="index.php?action=eliminar_libro&id=<?= $item->getId() ?>"
                                    onclick="return confirm('¿Estás seguro de eliminar este libro?');"
                                    class="link-delete">Eliminar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>