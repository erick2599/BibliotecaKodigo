<?php
require_once 'Database.php';
require_once 'Libro.php';
require_once 'Usuario.php';
require_once 'Prestamo.php';

class Biblioteca
{
    private $db;
    private $conn;

    public function __construct()
    {
        $this->db = new Database();
        $this->conn = $this->db->getConnection();
    }

    // Gestión de Libros
    public function agregarLibro(Libro $libro)
    {
        try {
            $query = "INSERT INTO libros (titulo, autor, isbn, cantidad) VALUES (:titulo, :autor, :isbn, :cantidad)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':titulo', $libro->getTitulo());
            $stmt->bindValue(':autor', $libro->getAutor());
            $stmt->bindValue(':isbn', $libro->getIsbn());
            $stmt->bindValue(':cantidad', $libro->getCantidad());
            $stmt->execute();
        } catch (PDOException $e) {
            echo "Error al agregar libro: " . $e->getMessage();
        }
    }

    public function editarLibro($id, $nuevosDatos)
    {
        try {
            $query = "UPDATE libros SET titulo = :titulo, autor = :autor, isbn = :isbn, cantidad = :cantidad WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':titulo', $nuevosDatos['titulo']);
            $stmt->bindValue(':autor', $nuevosDatos['autor']);
            $stmt->bindValue(':isbn', $nuevosDatos['isbn']);
            $stmt->bindValue(':cantidad', $nuevosDatos['cantidad']);
            $stmt->bindValue(':id', $id);
            $stmt->execute();
        } catch (PDOException $e) {
            echo "Error al editar libro: " . $e->getMessage();
        }
    }

    public function eliminarLibro($id)
    {
        try {
            $query = "DELETE FROM libros WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':id', $id);
            $stmt->execute();
        } catch (PDOException $e) {
            echo "Error al eliminar libro: " . $e->getMessage();
        }
    }

    public function obtenerLibros()
    {
        try {
            $query = "SELECT id, titulo, autor, isbn, cantidad FROM libros ORDER BY id ASC LIMIT 5";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $libros = [];

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $libro = new Libro($row['titulo'], $row['autor'], $row['isbn'], $row['cantidad'], $row['id']);
                $libros[] = $libro;
            }

            return $libros;
        } catch (PDOException $e) {
            echo "Error al obtener libros: " . $e->getMessage();
            return [];
        }
    }

    public function buscarLibro($id)
    {
        try {
            $query = "SELECT id, titulo, autor, isbn, cantidad FROM libros WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':id', $id);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row) {
                return new Libro($row['titulo'], $row['autor'], $row['isbn'], $row['cantidad'], $row['id']);
            } else {
                return null; // No se encontró el libro
            }
        } catch (PDOException $e) {
            echo "Error al buscar libro: " . $e->getMessage();
            return null;
        }
    }

    // Gestión de Usuarios
    public function agregarUsuario($usuario)
    {
        try {
            // Fuerza a PDO a lanzar excepciones detalladas en caso de error de SQL
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $query = "INSERT INTO usuarios (nombre, email, telefono) VALUES (:nombre, :email, :telefono)";
            $stmt = $this->conn->prepare($query);

            $stmt->bindValue(':nombre', $usuario->getNombre());
            $stmt->bindValue(':email', $usuario->getEmail());
            $stmt->bindValue(':telefono', $usuario->getTelefono());

            $stmt->execute();
        } catch (PDOException $e) {
            // Esto detendrá la ejecución y te dirá exactamente qué columna o dato está fallando
            die("Error crítico en la Base de Datos: " . $e->getMessage());
        }
    }


    public function editarUsuario($id, $nuevosDatos)
    {
        try {
            $query = "UPDATE usuarios SET nombre = :nombre, email = :email, telefono = :telefono WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':nombre', $nuevosDatos['nombre']);
            $stmt->bindValue(':email', $nuevosDatos['email']);
            $stmt->bindValue(':telefono', $nuevosDatos['telefono']);
            $stmt->bindValue(':id', $id);
            $stmt->execute();
        } catch (PDOException $e) {
            echo "Error al editar usuario: " . $e->getMessage();
        }
    }

    // CORREGIDO: Se eliminó una llave extra '}' que rompía la clase aquí
    public function eliminarUsuario($id)
    {
        try {
            $query = "DELETE FROM usuarios WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':id', $id);
            $stmt->execute();
        } catch (PDOException $e) {
            echo "Error al eliminar usuario: " . $e->getMessage();
        }
    }

    public function obtenerUsuarios()
    {
        try {
            $query = "SELECT id, nombre, email, telefono FROM usuarios";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $usuarios = [];
            foreach ($rows as $row) {
                // ADAPTADO: Pasamos primero el ID, luego nombre, email y teléfono
                $usuario = new Usuario($row['id'], $row['nombre'], $row['email'], $row['telefono']);
                $usuarios[] = $usuario;
            }
            return $usuarios;
        } catch (PDOException $e) {
            echo "Error al obtener usuarios: " . $e->getMessage();
            return [];
        }
    }

    public function buscarUsuario($id)
    {
        try {
            $query = "SELECT id, nombre, email, telefono FROM usuarios WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':id', $id);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row) {
                // ADAPTADO: Pasamos primero el ID de la consulta SQL
                return new Usuario($row['id'], $row['nombre'], $row['email'], $row['telefono']);
            }
            return null;
        } catch (PDOException $e) {
            echo "Error al buscar usuario: " . $e->getMessage();
            return null;
        }
    }



    // Gestión de Préstamos
    public function agregarPrestamo(Prestamo $prestamo)
    {
        try {
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $query = "INSERT INTO prestamos (usuario_id, libro_id, fecha_prestamo, fecha_devolucion, estado) 
                  VALUES (:usuario_id, :libro_id, :fecha_prestamo, :fecha_devolucion, :estado)";

            $stmt = $this->conn->prepare($query);

            // DEPURACIÓN: Asegúrate de que no estén cruzados aquí
            $stmt->bindValue(':usuario_id', $prestamo->getUsuarioId()); // Debe devolver el ID del usuario
            $stmt->bindValue(':libro_id', $prestamo->getLibroId());     // Debe devolver el ID del libro
            $stmt->bindValue(':fecha_prestamo', $prestamo->getFechaPrestamo());
            $stmt->bindValue(':fecha_devolucion', $prestamo->getFechaDevolucion());
            $stmt->bindValue(':estado', $prestamo->getEstado());

            $stmt->execute();
        } catch (PDOException $e) {
            die("Error crítico al guardar el préstamo: " . $e->getMessage());
        }
    }



    public function devolverLibro($prestamo_id)
    {
        try {
            // Obtener el libro asociado al préstamo
            $query = "SELECT libro_id FROM prestamos WHERE id = :prestamo_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':prestamo_id', $prestamo_id);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row) {
                $libro_id = $row['libro_id'];

                // Eliminar el préstamo
                $query = "DELETE FROM prestamos WHERE id = :prestamo_id";
                $stmt = $this->conn->prepare($query);
                $stmt->bindValue(':prestamo_id', $prestamo_id);
                $stmt->execute();

                // Actualizar la cantidad del libro
                $query = "UPDATE libros SET cantidad = cantidad + 1 WHERE id = :libro_id";
                $stmt = $this->conn->prepare($query);
                $stmt->bindValue(':libro_id', $libro_id);
                $stmt->execute();
            } else {
                echo "Préstamo no encontrado.";
            }
        } catch (PDOException $e) {
            echo "Error al devolver libro: " . $e->getMessage();
        }
    }

    public function obtenerPrestamosActivos()
    {
        try {
            $query = "SELECT id, usuario_id, libro_id, fecha_prestamo, fecha_devolucion, estado FROM prestamos WHERE estado = 'activo'";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $prestamos = [];
            foreach ($rows as $row) {
                // CORREGIDO: Pasamos primero usuario_id, luego libro_id, y al final enviamos el ID del registro al constructor
                $prestamo = new Prestamo(
                    $row['usuario_id'],
                    $row['libro_id'],
                    $row['fecha_prestamo'],
                    $row['fecha_devolucion'],
                    $row['estado'],
                    $row['id']
                );
                $prestamos[] = $prestamo;
            }
            return $prestamos;
        } catch (PDOException $e) {
            echo "Error al obtener préstamos activos: " . $e->getMessage();
            return [];
        }
    }


    public function eliminarPrestamo($id)
    {
        try {
            $query = "DELETE FROM prestamos WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':id', $id);
            $stmt->execute();
        } catch (PDOException $e) {
            echo "Error al eliminar préstamo: " . $e->getMessage();
        }
    }
}
