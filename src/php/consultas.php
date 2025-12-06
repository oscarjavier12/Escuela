<?php
// Consultas.php - Clase para manejar todas las consultas a la base de datos

require_once 'config.php';

class Consultas {
    private $conexion;
    
    public function __construct() {
        $this->conexion = getConexion();
    }
    
    // ==================== DOCENTES ====================
    
    public function crearDocente($nombre, $apellido, $email) {
        $nombre = pg_escape_string($nombre);
        $apellido = pg_escape_string($apellido);
        $email = pg_escape_string($email);
        
        $sql = "INSERT INTO Docente (nombre, apellido, email) VALUES ('$nombre', '$apellido', '$email')";
        return $this->conexion->ejecutar($sql);
    }
    
    public function obtenerDocentes() {
        $sql = "SELECT * FROM Docente ORDER BY id_docente";
        return $this->conexion->ejecutar($sql);
    }
    
    public function obtenerDocentePorId($id) {
        $id = pg_escape_string($id);
        $sql = "SELECT * FROM Docente WHERE id_docente = $id";
        $resultado = $this->conexion->ejecutar($sql);
        return pg_fetch_assoc($resultado);
    }
    
    public function editarDocente($id, $nombre, $apellido, $email) {
        $id = pg_escape_string($id);
        $nombre = pg_escape_string($nombre);
        $apellido = pg_escape_string($apellido);
        $email = pg_escape_string($email);
        
        $sql = "UPDATE Docente SET nombre='$nombre', apellido='$apellido', email='$email' WHERE id_docente=$id";
        return $this->conexion->ejecutar($sql);
    }
    
    public function eliminarDocente($id) {
        $id = pg_escape_string($id);
        $sql = "DELETE FROM Docente WHERE id_docente = $id";
        return $this->conexion->ejecutar($sql);
    }
    
    // ==================== ALUMNOS ====================
    
    public function crearAlumno($num_control, $nombre, $apellido, $id_carrera) {
        $num_control = pg_escape_string($num_control);
        $nombre = pg_escape_string($nombre);
        $apellido = pg_escape_string($apellido);
        $id_carrera = pg_escape_string($id_carrera);
        
        $sql = "INSERT INTO Alumno (num_control, nombre, apellido, id_carrera) VALUES ('$num_control', '$nombre', '$apellido', $id_carrera)";
        return $this->conexion->ejecutar($sql);
    }
    
    public function obtenerAlumnos() {
        $sql = "SELECT a.*, c.nombre as nombre_carrera FROM Alumno a JOIN Carrera c ON a.id_carrera = c.id_carrera ORDER BY a.num_control";
        return $this->conexion->ejecutar($sql);
    }
    
    public function obtenerAlumnoPorControl($num_control) {
        $num_control = pg_escape_string($num_control);
        $sql = "SELECT * FROM Alumno WHERE num_control = '$num_control'";
        $resultado = $this->conexion->ejecutar($sql);
        return pg_fetch_assoc($resultado);
    }
    
    public function editarAlumno($num_control, $nombre, $apellido, $id_carrera) {
        $num_control = pg_escape_string($num_control);
        $nombre = pg_escape_string($nombre);
        $apellido = pg_escape_string($apellido);
        $id_carrera = pg_escape_string($id_carrera);
        
        $sql = "UPDATE Alumno SET nombre='$nombre', apellido='$apellido', id_carrera=$id_carrera WHERE num_control='$num_control'";
        return $this->conexion->ejecutar($sql);
    }
    
    public function eliminarAlumno($num_control) {
        $num_control = pg_escape_string($num_control);
        $sql = "DELETE FROM Alumno WHERE num_control = '$num_control'";
        return $this->conexion->ejecutar($sql);
    }
    
    // ==================== MATERIAS ====================
    
    public function crearMateria($nombre_materia, $creditos) {
        $nombre_materia = pg_escape_string($nombre_materia);
        $creditos = pg_escape_string($creditos);
        
        $sql = "INSERT INTO Materia (nombre_materia, creditos) VALUES ('$nombre_materia', $creditos)";
        return $this->conexion->ejecutar($sql);
    }
    
    public function obtenerMaterias() {
        $sql = "SELECT * FROM Materia ORDER BY nombre_materia";
        return $this->conexion->ejecutar($sql);
    }
    
    public function obtenerMateriaPorId($id) {
        $id = pg_escape_string($id);
        $sql = "SELECT * FROM Materia WHERE id_materia = $id";
        $resultado = $this->conexion->ejecutar($sql);
        return pg_fetch_assoc($resultado);
    }
    
    public function editarMateria($id, $nombre_materia, $creditos) {
        $id = pg_escape_string($id);
        $nombre_materia = pg_escape_string($nombre_materia);
        $creditos = pg_escape_string($creditos);
        
        $sql = "UPDATE Materia SET nombre_materia='$nombre_materia', creditos=$creditos WHERE id_materia=$id";
        return $this->conexion->ejecutar($sql);
    }
    
    public function eliminarMateria($id) {
        $id = pg_escape_string($id);
        $sql = "DELETE FROM Materia WHERE id_materia = $id";
        return $this->conexion->ejecutar($sql);
    }
    
    // ==================== CARRERAS ====================
    
    public function crearCarrera($nombre) {
        $nombre = pg_escape_string($nombre);
        $sql = "INSERT INTO Carrera (nombre) VALUES ('$nombre')";
        return $this->conexion->ejecutar($sql);
    }
    
    public function obtenerCarreras() {
        $sql = "SELECT * FROM Carrera ORDER BY id_carrera";
        return $this->conexion->ejecutar($sql);
    }
    
    public function obtenerCarreraPorId($id) {
        $id = pg_escape_string($id);
        $sql = "SELECT * FROM Carrera WHERE id_carrera = $id";
        $resultado = $this->conexion->ejecutar($sql);
        return pg_fetch_assoc($resultado);
    }
    
    public function editarCarrera($id, $nombre) {
        $id = pg_escape_string($id);
        $nombre = pg_escape_string($nombre);
        
        $sql = "UPDATE Carrera SET nombre='$nombre' WHERE id_carrera=$id";
        return $this->conexion->ejecutar($sql);
    }
    
    public function eliminarCarrera($id) {
        $id = pg_escape_string($id);
        $sql = "DELETE FROM Carrera WHERE id_carrera = $id";
        return $this->conexion->ejecutar($sql);
    }
    
    // ==================== CURSOS ====================
    
    public function crearCurso($id_docente, $id_materia, $periodo, $grupo) {
        $id_docente = pg_escape_string($id_docente);
        $id_materia = pg_escape_string($id_materia);
        $periodo = pg_escape_string($periodo);
        $grupo = pg_escape_string($grupo);
        
        $sql = "INSERT INTO Cursos (id_docente, id_materia, periodo, grupo) VALUES ($id_docente, $id_materia, '$periodo', '$grupo')";
        return $this->conexion->ejecutar($sql);
    }
    
    public function obtenerCursos() {
        $sql = "SELECT c.*, d.nombre || ' ' || d.apellido as nombre_docente, m.nombre_materia 
                FROM Cursos c 
                JOIN Docente d ON c.id_docente = d.id_docente 
                JOIN Materia m ON c.id_materia = m.id_materia 
                ORDER BY c.periodo DESC, c.id_curso";
        return $this->conexion->ejecutar($sql);
    }
    
    public function obtenerCursoPorId($id) {
        $id = pg_escape_string($id);
        $sql = "SELECT * FROM Cursos WHERE id_curso = $id";
        $resultado = $this->conexion->ejecutar($sql);
        return pg_fetch_assoc($resultado);
    }
    
    public function editarCurso($id, $id_docente, $id_materia, $periodo, $grupo) {
        $id = pg_escape_string($id);
        $id_docente = pg_escape_string($id_docente);
        $id_materia = pg_escape_string($id_materia);
        $periodo = pg_escape_string($periodo);
        $grupo = pg_escape_string($grupo);
        
        $sql = "UPDATE Cursos SET id_docente=$id_docente, id_materia=$id_materia, periodo='$periodo', grupo='$grupo' WHERE id_curso=$id";
        return $this->conexion->ejecutar($sql);
    }
    
    public function eliminarCurso($id) {
        $id = pg_escape_string($id);
        $sql = "DELETE FROM Cursos WHERE id_curso = $id";
        return $this->conexion->ejecutar($sql);
    }
    
    // ==================== CALIFICACIONES ====================
    
    public function crearCalificacion($num_control, $id_curso, $tipo_evaluacion, $calificacion) {
        $num_control = pg_escape_string($num_control);
        $id_curso = pg_escape_string($id_curso);
        $tipo_evaluacion = pg_escape_string($tipo_evaluacion);
        $calificacion = pg_escape_string($calificacion);
        
        $sql = "INSERT INTO Calificaciones (num_control, id_curso, tipo_evaluacion, calificacion) 
                VALUES ('$num_control', $id_curso, '$tipo_evaluacion', $calificacion)";
        return $this->conexion->ejecutar($sql);
    }
    
    public function obtenerCalificaciones() {
        $sql = "SELECT cal.*, a.nombre || ' ' || a.apellido as nombre_alumno, m.nombre_materia, c.periodo, c.grupo 
                FROM Calificaciones cal 
                JOIN Alumno a ON cal.num_control = a.num_control 
                JOIN Cursos c ON cal.id_curso = c.id_curso 
                JOIN Materia m ON c.id_materia = m.id_materia 
                ORDER BY cal.fecha_registro DESC";
        return $this->conexion->ejecutar($sql);
    }
    
    public function obtenerCalificacionPorId($id) {
        $id = pg_escape_string($id);
        $sql = "SELECT * FROM Calificaciones WHERE id_calificacion = $id";
        $resultado = $this->conexion->ejecutar($sql);
        return pg_fetch_assoc($resultado);
    }
    
    public function editarCalificacion($id, $num_control, $id_curso, $tipo_evaluacion, $calificacion) {
        $id = pg_escape_string($id);
        $num_control = pg_escape_string($num_control);
        $id_curso = pg_escape_string($id_curso);
        $tipo_evaluacion = pg_escape_string($tipo_evaluacion);
        $calificacion = pg_escape_string($calificacion);
        
        $sql = "UPDATE Calificaciones SET num_control='$num_control', id_curso=$id_curso, 
                tipo_evaluacion='$tipo_evaluacion', calificacion=$calificacion WHERE id_calificacion=$id";
        return $this->conexion->ejecutar($sql);
    }
    
    public function eliminarCalificacion($id) {
        $id = pg_escape_string($id);
        $sql = "DELETE FROM Calificaciones WHERE id_calificacion = $id";
        return $this->conexion->ejecutar($sql);
    }
}
?>