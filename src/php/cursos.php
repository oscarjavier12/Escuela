<?php
require_once 'config.php';
require_once 'Consultas.php';

$consultas = new Consultas();

// Procesar acciones
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'crear':
                $consultas->crearCurso($_POST['id_docente'], $_POST['id_materia'], $_POST['periodo'], $_POST['grupo']);
                header("Location: cursos.php");
                exit();
                break;
                
            case 'editar':
                $consultas->editarCurso($_POST['id_curso'], $_POST['id_docente'], $_POST['id_materia'], $_POST['periodo'], $_POST['grupo']);
                header("Location: cursos.php");
                exit();
                break;
                
            case 'eliminar':
                $consultas->eliminarCurso($_POST['id_curso']);
                header("Location: cursos.php");
                exit();
                break;
        }
    }
}

// Obtener curso para editar
$editando = null;
if (isset($_GET['editar'])) {
    $editando = $consultas->obtenerCursoPorId($_GET['editar']);
}

// Obtener todos los cursos con información de docente y materia
$resultadoC = $consultas->obtenerCursos();
$cursos = [];
while ($row = pg_fetch_assoc($resultadoC)) {
    $cursos[] = $row;
}

// Obtener docentes para el select

$resultadoD = $consultas->obtenerDocentes();
$docentes = [];
while ($row = pg_fetch_assoc($resultadoD)) {
    $docentes[] = $row;
}

// Obtener materias para el select
$resultadoM = $consultas->obtenerMaterias();
$materias = [];
while ($row = pg_fetch_assoc($resultadoM)) {
    $materias[] = $row;
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Cursos</title>
    <link rel="stylesheet" href="../css/cursos.css">
</head>
<body>
    <div class="container">
        <a href="../../index.php" class="btn-volver">← Volver al Menú</a>
        <h1>📝 Gestión de Cursos</h1>
        
        <div class="form-container">
            <h2><?php echo $editando ? 'Editar Curso' : 'Agregar Nuevo Curso'; ?></h2>
            <form method="POST">
                <input type="hidden" name="action" value="<?php echo $editando ? 'editar' : 'crear'; ?>">
                <?php if ($editando): ?>
                    <input type="hidden" name="id_curso" value="<?php echo $editando['id_curso']; ?>">
                <?php endif; ?>
                
                <div class="form-group">
                    <label>Docente:</label>
                    <select name="id_docente" required>
                        <option value="">Seleccione un docente</option>
                        <?php foreach ($docentes as $docente): ?>
                            <option value="<?php echo $docente['id_docente']; ?>"
                                <?php echo ($editando && $editando['id_docente'] == $docente['id_docente']) ? 'selected' : ''; ?>>
                                <?php echo $docente['apellido'] . ', ' . $docente['nombre']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Materia:</label>
                    <select name="id_materia" required>
                        <option value="">Seleccione una materia</option>
                        <?php foreach ($materias as $materia): ?>
                            <option value="<?php echo $materia['id_materia']; ?>"
                                <?php echo ($editando && $editando['id_materia'] == $materia['id_materia']) ? 'selected' : ''; ?>>
                                <?php echo $materia['nombre_materia']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Periodo (ej: 2024-1, ENE-JUN 2024):</label>
                    <input type="text" name="periodo" required value="<?php echo $editando ? $editando['periodo'] : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label>Grupo:</label>
                    <input type="text" name="grupo" required value="<?php echo $editando ? $editando['grupo'] : ''; ?>">
                </div>
                
                <button type="submit" class="btn <?php echo $editando ? 'btn-success' : 'btn-primary'; ?>">
                    <?php echo $editando ? 'Actualizar' : 'Guardar'; ?>
                </button>
                <?php if ($editando): ?>
                    <a href="cursos.php" class="btn btn-danger">Cancelar</a>
                <?php endif; ?>
            </form>
        </div>
        
        <h2>Lista de Cursos</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Materia</th>
                    <th>Docente</th>
                    <th>Periodo</th>
                    <th>Grupo</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cursos as $curso): ?>
                <tr>
                    <td><?php echo $curso['id_curso']; ?></td>
                    <td><?php echo $curso['nombre_materia']; ?></td>
                    <td><?php echo $curso['nombre_docente']; ?></td>
                    <td><?php echo $curso['periodo']; ?></td>
                    <td><?php echo $curso['grupo']; ?></td>
                    <td class="acciones">
                        <a href="?editar=<?php echo $curso['id_curso']; ?>" class="btn btn-warning">Editar</a>
                        <form method="POST" style="display:inline;" onsubmit="return confirm('¿Está seguro de eliminar este curso?');">
                            <input type="hidden" name="action" value="eliminar">
                            <input type="hidden" name="id_curso" value="<?php echo $curso['id_curso']; ?>">
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
