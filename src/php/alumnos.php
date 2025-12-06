<?php
require_once 'config.php';
require_once 'Consultas.php';

$consultas = new Consultas();

// Procesar acciones
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'crear':
                $consultas->crearAlumno($_POST['num_control'], $_POST['nombre'], $_POST['apellido'], $_POST['id_carrera']);
                header("Location: alumnos.php");
                exit();
                break;
                
            case 'editar':
                $consultas->editarAlumno($_POST['num_control'], $_POST['nombre'], $_POST['apellido'], $_POST['id_carrera']);
                header("Location: alumnos.php");
                exit();
                break;
                
            case 'eliminar':
                $consultas->eliminarAlumno($_POST['num_control']);
                header("Location: alumnos.php");
                exit();
                break;
        }
    }
}

// Obtener alumno para editar
$editando = null;
if (isset($_GET['editar'])) {
    $editando = $consultas->obtenerAlumnoPorControl($_GET['editar']);
}

// Obtener todos los alumnos con su carrera
$resultado = $consultas->obtenerAlumnos();
$alumnos = [];
while ($row = pg_fetch_assoc($resultado)) {
    $alumnos[] = $row;
}
// Obtener carreras para el select
$resultadoC = $consultas->obtenerCarreras();
$carreras = [];
while ($row = pg_fetch_assoc($resultadoC)) {
    $carreras[] = $row;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Alumnos</title>
    <link rel="stylesheet" href="../css/alumnos.css">
</head>
<body>
    <div class="container">
        <a href="../../index.php" class="btn-volver">← Volver al Menú</a>
        <h1>👨‍🎓 Gestión de Alumnos</h1>
        
        <div class="form-container">
            <h2><?php echo $editando ? 'Editar Alumno' : 'Agregar Nuevo Alumno'; ?></h2>
            <form method="POST">
                <input type="hidden" name="action" value="<?php echo $editando ? 'editar' : 'crear'; ?>">
                
                <div class="form-group">
                    <label>Número de Control:</label>
                    <input type="text" name="num_control" required 
                           value="<?php echo $editando ? $editando['num_control'] : ''; ?>"
                           <?php echo $editando ? 'readonly' : ''; ?>>
                </div>
                
                <div class="form-group">
                    <label>Nombre:</label>
                    <input type="text" name="nombre" required value="<?php echo $editando ? $editando['nombre'] : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label>Apellido:</label>
                    <input type="text" name="apellido" required value="<?php echo $editando ? $editando['apellido'] : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label>Carrera:</label>
                    <select name="id_carrera" required>
                        <option value="">Seleccione una carrera</option>
                        <?php foreach ($carreras as $carrera): ?>
                            <option value="<?php echo $carrera['id_carrera']; ?>"
                                <?php echo ($editando && $editando['id_carrera'] == $carrera['id_carrera']) ? 'selected' : ''; ?>>
                                <?php echo $carrera['nombre']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <button type="submit" class="btn <?php echo $editando ? 'btn-success' : 'btn-primary'; ?>">
                    <?php echo $editando ? 'Actualizar' : 'Guardar'; ?>
                </button>
                <?php if ($editando): ?>
                    <a href="alumnos.php" class="btn btn-danger">Cancelar</a>
                <?php endif; ?>
            </form>
        </div>
        
        <h2>Lista de Alumnos</h2>
        <table>
            <thead>
                <tr>
                    <th>Número Control</th>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>Carrera</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($alumnos as $alumno): ?>
                <tr>
                    <td><?php echo $alumno['num_control']; ?></td>
                    <td><?php echo $alumno['nombre']; ?></td>
                    <td><?php echo $alumno['apellido']; ?></td>
                    <td><?php echo $alumno['nombre_carrera']; ?></td>
                    <td class="acciones">
                        <a href="?editar=<?php echo $alumno['num_control']; ?>" class="btn btn-warning">Editar</a>
                        <form method="POST" style="display:inline;" onsubmit="return confirm('¿Está seguro de eliminar este alumno?');">
                            <input type="hidden" name="action" value="eliminar">
                            <input type="hidden" name="num_control" value="<?php echo $alumno['num_control']; ?>">
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
