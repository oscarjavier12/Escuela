<?php
require_once 'config.php';
require_once 'Consultas.php';

$consultas = new Consultas();

// Procesar acciones
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'crear':
                $consultas->crearCalificacion($_POST['num_control'], $_POST['id_curso'], $_POST['tipo_evaluacion'], $_POST['calificacion']);
                header("Location: calificaciones.php");
                exit();
                break;
                
            case 'editar':
                $consultas->editarCalificacion()($_POST['id_calificacion'], $_POST['num_control'], $_POST['id_curso'], $_POST['tipo_evaluacion'], $_POST['calificacion']);
                header("Location: calificaciones.php");
                exit();
                break;
                
            case 'eliminar':
                $consultas->eliminarCalificacion($_POST['id_calificacion']);
                header("Location: calificaciones.php");
                exit();
                break;
        }
    }
}

// Obtener calificación para editar
$editando = null;
if (isset($_GET['editar'])) {
    $editando = $consultas->obtenerCalificacionPorId($_GET['editar']);
}

// Obtener todas las calificaciones con información relacionada
$resultadoC = $consultas->obtenerCalificaciones();
$calificaciones = [];
while ($row = pg_fetch_assoc($resultadoC)) {
    $calificaciones[] = $row;
}

// Obtener alumnos para el select
$resultadoA = $consultas->obtenerAlumnos();
$alumnos = [];
while ($row = pg_fetch_assoc($resultadoA)) {
    $alumnos[] = $row;
}

// Obtener cursos para el select
$resultadoCu = $consultas->obtenerCursos();
$cursos = [];
while ($row = pg_fetch_assoc($resultadoCu)) {
    $cursos[] = $row;
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Calificaciones</title>
    <link rel="stylesheet" href="../css/calificaciones.css">
</head>
<body>
    <div class="container">
        <a href="../../index.php" class="btn-volver">← Volver al Menú</a>
        <h1>📊 Gestión de Calificaciones</h1>
        
        <div class="form-container">
            <h2><?php echo $editando ? 'Editar Calificación' : 'Agregar Nueva Calificación'; ?></h2>
            <form method="POST">
                <input type="hidden" name="action" value="<?php echo $editando ? 'editar' : 'crear'; ?>">
                <?php if ($editando): ?>
                    <input type="hidden" name="id_calificacion" value="<?php echo $editando['id_calificacion']; ?>">
                <?php endif; ?>
                
                <div class="form-group">
                    <label>Alumno:</label>
                    <select name="num_control" required>
                        <option value="">Seleccione un alumno</option>
                        <?php foreach ($alumnos as $alumno): ?>
                            <option value="<?php echo $alumno['num_control']; ?>"
                                <?php echo ($editando && $editando['num_control'] == $alumno['num_control']) ? 'selected' : ''; ?>>
                                <?php echo $alumno['num_control'] . ' - ' . $alumno['apellido'] . ', ' . $alumno['nombre']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Curso:</label>
                    <select name="id_curso" required>
                        <option value="">Seleccione un curso</option>
                        <?php foreach ($cursos as $curso): ?>
                            <option value="<?php echo $curso['id_curso']; ?>"
                                <?php echo ($editando && $editando['id_curso'] == $curso['id_curso']) ? 'selected' : ''; ?>>
                                <?php echo $curso['nombre_materia'] . ' - ' . $curso['periodo'] . ' - Grupo ' . $curso['grupo']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Tipo de Evaluación (ej: Parcial 1, Final, Tarea, etc.):</label>
                    <input type="text" name="tipo_evaluacion" required value="<?php echo $editando ? $editando['tipo_evaluacion'] : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label>Calificación (0-100):</label>
                    <input type="number" name="calificacion" min="0" max="100" step="0.01" required 
                           value="<?php echo $editando ? $editando['calificacion'] : ''; ?>">
                </div>
                
                <button type="submit" class="btn <?php echo $editando ? 'btn-success' : 'btn-primary'; ?>">
                    <?php echo $editando ? 'Actualizar' : 'Guardar'; ?>
                </button>
                <?php if ($editando): ?>
                    <a href="calificaciones.php" class="btn btn-danger">Cancelar</a>
                <?php endif; ?>
            </form>
        </div>
        
        <h2>Lista de Calificaciones</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Alumno</th>
                    <th>Materia</th>
                    <th>Periodo</th>
                    <th>Grupo</th>
                    <th>Tipo Eval.</th>
                    <th>Calif.</th>
                    <th>Fecha</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($calificaciones as $cal): ?>
                <?php
                    $clase_calif = '';
                    if ($cal['calificacion'] >= 80) $clase_calif = 'calificacion-alta';
                    elseif ($cal['calificacion'] >= 60) $clase_calif = 'calificacion-media';
                    else $clase_calif = 'calificacion-baja';
                ?>
                <tr>
                    <td><?php echo $cal['id_calificacion']; ?></td>
                    <td><?php echo $cal['nombre_alumno']; ?></td>
                    <td><?php echo $cal['nombre_materia']; ?></td>
                    <td><?php echo $cal['periodo']; ?></td>
                    <td><?php echo $cal['grupo']; ?></td>
                    <td><?php echo $cal['tipo_evaluacion']; ?></td>
                    <td class="<?php echo $clase_calif; ?>"><?php echo number_format($cal['calificacion'], 2); ?></td>
                    <td><?php echo date('d/m/Y', strtotime($cal['fecha_registro'])); ?></td>
                    <td class="acciones">
                        <a href="?editar=<?php echo $cal['id_calificacion']; ?>" class="btn btn-warning">Editar</a>
                        <form method="POST" style="display:inline;" onsubmit="return confirm('¿Está seguro de eliminar esta calificación?');">
                            <input type="hidden" name="action" value="eliminar">
                            <input type="hidden" name="id_calificacion" value="<?php echo $cal['id_calificacion']; ?>">
                            <button type="submit" class="btn btn-danger">Eliminar</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
