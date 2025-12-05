<?php
require_once 'config.php';

$db = new Database();
$conn = $db->connect();

// Procesar acciones
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'crear':
                $stmt = $conn->prepare("INSERT INTO Calificaciones (num_control, id_curso, tipo_evaluacion, calificacion) VALUES (?, ?, ?, ?)");
                $stmt->execute([$_POST['num_control'], $_POST['id_curso'], $_POST['tipo_evaluacion'], $_POST['calificacion']]);
                header("Location: calificaciones.php");
                exit();
                break;
                
            case 'editar':
                $stmt = $conn->prepare("UPDATE Calificaciones SET num_control=?, id_curso=?, tipo_evaluacion=?, calificacion=? WHERE id_calificacion=?");
                $stmt->execute([$_POST['num_control'], $_POST['id_curso'], $_POST['tipo_evaluacion'], $_POST['calificacion'], $_POST['id_calificacion']]);
                header("Location: calificaciones.php");
                exit();
                break;
                
            case 'eliminar':
                $stmt = $conn->prepare("DELETE FROM Calificaciones WHERE id_calificacion=?");
                $stmt->execute([$_POST['id_calificacion']]);
                header("Location: calificaciones.php");
                exit();
                break;
        }
    }
}

// Obtener calificación para editar
$editando = null;
if (isset($_GET['editar'])) {
    $stmt = $conn->prepare("SELECT * FROM Calificaciones WHERE id_calificacion=?");
    $stmt->execute([$_GET['editar']]);
    $editando = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Obtener todas las calificaciones con información relacionada
$stmt = $conn->query("
    SELECT cal.*, 
           a.nombre || ' ' || a.apellido as nombre_alumno,
           m.nombre_materia,
           c.periodo,
           c.grupo
    FROM Calificaciones cal
    JOIN Alumno a ON cal.num_control = a.num_control
    JOIN Cursos c ON cal.id_curso = c.id_curso
    JOIN Materia m ON c.id_materia = m.id_materia
    ORDER BY cal.fecha_registro DESC
");
$calificaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener alumnos para el select
$stmt = $conn->query("SELECT num_control, nombre, apellido FROM Alumno ORDER BY apellido, nombre");
$alumnos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener cursos para el select
$stmt = $conn->query("
    SELECT c.id_curso, m.nombre_materia, c.periodo, c.grupo
    FROM Cursos c
    JOIN Materia m ON c.id_materia = m.id_materia
    ORDER BY c.periodo DESC, m.nombre_materia
");
$cursos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Calificaciones</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 1500px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        
        h1 {
            color: #333;
            margin-bottom: 10px;
        }
        
        .btn-volver {
            display: inline-block;
            padding: 10px 20px;
            background: #6c757d;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .btn-volver:hover {
            background: #5a6268;
        }
        
        .form-container {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        label {
            display: block;
            margin-bottom: 5px;
            color: #333;
            font-weight: bold;
        }
        
        input, select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }
        
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
        }
        
        .btn-primary {
            background: #667eea;
            color: white;
        }
        
        .btn-primary:hover {
            background: #5568d3;
        }
        
        .btn-success {
            background: #28a745;
            color: white;
        }
        
        .btn-danger {
            background: #dc3545;
            color: white;
        }
        
        .btn-warning {
            background: #ffc107;
            color: #333;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 14px;
        }
        
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        th {
            background: #667eea;
            color: white;
        }
        
        tr:hover {
            background: #f5f5f5;
        }
        
        .acciones {
            display: flex;
            gap: 5px;
        }
        
        .calificacion-alta {
            color: #28a745;
            font-weight: bold;
        }
        
        .calificacion-media {
            color: #ffc107;
            font-weight: bold;
        }
        
        .calificacion-baja {
            color: #dc3545;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="index.php" class="btn-volver">← Volver al Menú</a>
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
