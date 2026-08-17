<?php
// 1. Iniciamos el control de sesiones de PHP para evitar pérdidas de estado al recargar
session_start();

require_once 'classes/Biblioteca.php';
require_once 'classes/Libro.php';
require_once 'classes/Usuario.php';
require_once 'classes/Prestamo.php'; // Inclusión indispensable solucionada

// Instanciación de componentes
$biblioteca = new Biblioteca();

// 2. Controladores de datos dinámicos mediante estados en Sesión
$libroBuscado = null;
$usuarioBuscado = null;
$prestamoBuscado = null;

// Determinar la sección a mostrar por defecto desde la URL
$seccion_actual = isset($_GET['action']) ? $_GET['action'] : 'libros';

// Forzar visualmente el estado activo según los formularios que se procesan
if (strpos($seccion_actual, 'usuario') !== false) {
    $seccion_actual = 'usuarios';
} elseif (strpos($seccion_actual, 'prestamo') !== false) {
    $seccion_actual = 'prestamos';
} else {
    $seccion_actual = 'libros';
}

// =========================================================================
// 1. PROCESAMIENTO DE ACCIONES POR URL / ENLACES (GET)
// =========================================================================
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'])) {

    switch ($_GET['action']) {

        // --- ACCIONES DE LIBROS ---
        case 'eliminar_libro':
            if (isset($_GET['id'])) {
                $biblioteca->eliminarLibro($_GET['id']);
                header("Location: index.php?action=libros");
                exit;
            }
            break;

        case 'editar_libro_form':
            if (isset($_GET['id'])) {
                $libroBuscado = $biblioteca->buscarLibro($_GET['id']);
                $seccion_actual = 'libros';
            }
            break;

        // --- ACCIONES DE USUARIOS ---
        case 'eliminar_usuario':
            if (isset($_GET['id'])) {
                $biblioteca->eliminarUsuario($_GET['id']);
                header("Location: index.php?action=usuarios");
                exit;
            }
            break;

        case 'editar_usuario_form':
            if (isset($_GET['id'])) {
                $usuarioBuscado = $biblioteca->buscarUsuario($_GET['id']);
                $seccion_actual = 'usuarios';
            }
            break;

        // --- ACCIONES DE PRÉSTAMOS ---
        case "obtener_prestamos":
            $prestamos = $biblioteca->obtenerPrestamosActivos();
            $seccion_actual = 'prestamos';
            break;

        case "eliminar_prestamo":
            if (isset($_GET['id'])) {
                $biblioteca->eliminarPrestamo($_GET['id']);
                header("Location: index.php?action=prestamos");
                exit;
            }
            break;
    }
}

// =========================================================================
// 2. PROCESAMIENTO DE ACCIONES FORMULARIOS (POST)
// =========================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    switch ($_POST['action']) {

        // --- PROCESOS DE LIBROS ---
        case 'guardar_libro':
            $nuevoLibro = new Libro($_POST['titulo'], $_POST['autor'], $_POST['isbn'], (int)$_POST['cantidad']);
            $biblioteca->agregarLibro($nuevoLibro);
            header("Location: index.php?action=libros");
            exit;

        case 'buscar_libro':
            $id = $_POST["id"];
            $libroBuscado = $biblioteca->buscarLibro($id);
            if (!$libroBuscado) {
                echo "<script>alert('No se encontró ningún libro con el ID: " . htmlspecialchars($id) . "');</script>";
            }
            $seccion_actual = 'libros';
            break;

        case 'editar_libro':
            $id = $_POST["id"];
            $nuevosDatos = [
                'titulo' => $_POST['titulo'],
                'autor' => $_POST['autor'],
                'isbn' => $_POST['isbn'],
                'cantidad' => (int)$_POST['cantidad']
            ];
            $biblioteca->editarLibro($id, $nuevosDatos);
            header("Location: index.php?action=libros");
            exit;

            // --- PROCESOS DE USUARIOS ---
        case 'guardar_usuario':
            $nombre = $_POST['Nombre'];
            $email = $_POST['Email'];
            $telefono = $_POST['Telefono'];

            // ADAPTADO: Enviamos 'null' al inicio para saltar el ID autogenerado
            $nuevoUsuario = new Usuario(null, $nombre, $email, $telefono);

            $biblioteca->agregarUsuario($nuevoUsuario);
            header("Location: index.php?action=usuarios");
            exit;



        case 'editar_usuario':
            // Asegúrate de que las llaves del $_POST coincidan con los 'name' del formulario (Nombre, Email, Telefono)
            $id = $_POST["id"];
            $nuevosDatosUsuarios = [
                'nombre' => $_POST['Nombre'],
                'email' => $_POST['Email'],
                'telefono' => $_POST['Telefono']
            ];

            $biblioteca->editarUsuario($id, $nuevosDatosUsuarios);
            header("Location: index.php?action=usuarios");
            exit;


        case 'buscar_usuario':
            $idUsuario = $_POST["id"];
            $usuarioBuscado = $biblioteca->buscarUsuario($idUsuario);
            if (!$usuarioBuscado) {
                echo "<script>alert('No se encontró ningún usuario con el ID: " . htmlspecialchars($idUsuario) . "');</script>";
            }
            $seccion_actual = 'usuarios';
            break;

        // --- PROCESOS DE PRÉSTAMOS ---
        case "guardar_prestamo":
            $libro_id = $_POST['id_libro'];
            $usuario_id = $_POST['id_usuario'];
            $fecha_prestamo = $_POST['fecha_prestamo'];
            $fecha_devolucion = $_POST['fecha_devolucion'];
            $estado = $_POST['estado'];

            // 1. Buscamos de manera estricta ambos elementos en la BD para validar
            $libro = $biblioteca->buscarLibro($libro_id);
            $usuario = $biblioteca->buscarUsuario($usuario_id);

            if ($libro && $usuario) {
                if (class_exists('Prestamo') && method_exists($biblioteca, 'agregarPrestamo')) {

                    // CAMBIO APLICADO: Invertimos el orden de las variables al crear el objeto.
                    // Si antes tenías ($libro_id, $usuario_id) o viceversa, aquí forzamos la sincronización.
                    $prestamo = new Prestamo($usuario_id, $libro_id, $fecha_prestamo, $fecha_devolucion, $estado);

                    $biblioteca->agregarPrestamo($prestamo);
                    header("Location: index.php?action=prestamos");
                    exit;
                }
                echo "<script>alert('La funcionalidad de préstamos no está disponible en esta versión (Verifica las clases).');</script>";
            } else {
                // Mensaje preventivo inteligente: Te dirá si escribiste algo mal antes de romper la base de datos
                echo "<script>
                    alert('Error de validación: El ID de Libro o el ID de Usuario no existen en el sistema. Asegúrate de que estén registrados.');
                    window.location.href='index.php?action=prestamos';
                </script>";
                exit;
            }
            $seccion_actual = 'prestamos';
            break;

        case "devolver_libro":
            $prestamo_id = $_POST['id_prestamo'];
            $prestamo = method_exists($biblioteca, 'buscarPrestamo') ? $biblioteca->buscarPrestamo($prestamo_id) : null;

            if ($prestamo) {
                if (class_exists('Prestamo') && method_exists($biblioteca, 'devolverPrestamo')) {
                    $biblioteca->devolverPrestamo($prestamo_id);
                    header("Location: index.php?action=prestamos");
                    exit;
                }
                echo "<script>alert('La funcionalidad de devolución no está disponible en esta versión.');</script>";
            } else {
                echo "<script>alert('Préstamo no encontrado.');</script>";
            }
            $seccion_actual = 'prestamos';
            break;

        case "editar_prestamo":
            if (method_exists($biblioteca, 'editarPrestamo')) {
                $nuevosDatosPrestamo = [
                    'libro_id' => $_POST['id_libro'],
                    'usuario_id' => $_POST['id_usuario'],
                    'fecha_prestamo' => $_POST['fecha_prestamo'],
                    'fecha_devolucion' => $_POST['fecha_devolucion'],
                    'estado' => $_POST['estado']
                ];
                $biblioteca->editarPrestamo($_POST["id"], $nuevosDatosPrestamo);
                header("Location: index.php?action=prestamos");
                exit;
            }

            echo "<script>alert('La funcionalidad de edición de préstamos no está disponible en esta versión.');</script>";
            $seccion_actual = 'prestamos';
            break;

        case "buscar_prestamo":
            $idPrestamo = $_POST["id"];
            $prestamoBuscado = method_exists($biblioteca, 'buscarPrestamo') ? $biblioteca->buscarPrestamo($idPrestamo) : null;
            if (!$prestamoBuscado) {
                echo "<script>alert('No se encontró ningún préstamo con el ID: " . htmlspecialchars($idPrestamo) . "');</script>";
            }
            $seccion_actual = 'prestamos';
            break;
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Gestión de Biblioteca</title>
    <link rel="stylesheet" href="/biblioteca/style.css">
</head>

<body>
    <div class="container">
        <h1>Biblioteca Mini-App</h1>

        <nav>
            <!-- Clases active dinámicas según la sección de la aplicación -->
            <a href="index.php?action=libros" class="<?= $seccion_actual === 'libros' ? 'active' : '' ?>">Inicio / Libros</a>
            <a href="index.php?action=usuarios" class="<?= $seccion_actual === 'usuarios' ? 'active' : '' ?>">Usuarios</a>
            <a href="index.php?action=prestamos" class="<?= $seccion_actual === 'prestamos' ? 'active' : '' ?>">Préstamos</a>
        </nav>

        <div id="main-content">
            <?php
            // Inyección dinámica de las interfaces de control
            if ($seccion_actual === 'usuarios') {
                include 'UsuariosCRUD.php';
            } elseif ($seccion_actual === 'prestamos') {
                include 'PrestamosCRUD.php';
            } else {
                include 'LibrosCRUD.php';
            }
            ?>
        </div>
    </div>
</body>

</html>