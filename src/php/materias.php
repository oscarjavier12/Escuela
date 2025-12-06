<?php
require_once 'config.php';
require_once 'Consultas.php';

$consultas = new Consultas();

// Procesar acciones
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'crear':
                $consultas->crearMateria($_POST['nombre_materia'], $_POST['creditos']);
                header("Location: materias.php");
                exit();
                break;
                
            case 'editar':
                $consultas->editarMateria($_POST['id_materia'], $_POST['nombre_materia'], $_POST['creditos']);
                header("Location: materias.php");
                exit();
                break;
                
            case 'eliminar':
                $consultas->eliminarMateria($_POST['id_materia']);
                header("Location: materias.php");
                exit();
                break;
        }
    }
}

// Obtener materia para editar
$editando = null;
if (isset($_GET['editar'])) {
    $editando = $consultas->obtenerMateriaPorId($_GET['editar']);
}

// Obtener todas las materias
$resultado = $consultas->obtenerMaterias();
$materias = [];
while ($row = pg_fetch_assoc($resultado)) {
    $materias[] = $row;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Materias</title>
    <link rel="stylesheet" href="../css/materias.css">
</head>
<body>
    <div class="container">
        <a href="../../index.php" class="btn-volver">← Volver al Menú</a>
        <h1>📚 Gestión de Materias</h1>
        
        <div class="form-container">
            <h2><?php echo $editando ? 'Editar Materia' : 'Agregar Nueva Materia'; ?></h2>
            <form method="POST">
                <input type="hidden" name="action" value="<?php echo $editando ? 'editar' : 'crear'; ?>">
                <?php if ($editando): ?>
                    <input type="hidden" name="id_materia" value="<?php echo $editando['id_materia']; ?>">
                <?php endif; ?>
                
                <div class="form-group">
                    <label>Nombre de la Materia:</label>
                    <input type="text" name="nombre_materia" required value="<?php echo $editando ? $editando['nombre_materia'] : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label>Créditos:</label>
                    <input type="number" name="creditos" min="1" required value="<?php echo $editando ? $editando['creditos'] : ''; ?>">
                </div>
                
                <button type="submit" class="btn <?php echo $editando ? 'btn-success' : 'btn-primary'; ?>">
                    <?php echo $editando ? 'Actualizar' : 'Guardar'; ?>
                </button>
                <?php if ($editando): ?>
                    <a href="materias.php" class="btn btn-danger">Cancelar</a>
                <?php endif; ?>
            </form>
        </div>
        
        <h2>Lista de Materias</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre de la Materia</th>
                    <th>Créditos</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($materias as $materia): ?>
                <tr>
                    <td><?php echo $materia['id_materia']; ?></td>
                    <td><?php echo $materia['nombre_materia']; ?></td>
                    <td><?php echo $materia['creditos']; ?></td>
                    <td class="acciones">
                        <a href="?editar=<?php echo $materia['id_materia']; ?>" class="btn btn-warning">Editar</a>
                        <form method="POST" style="display:inline;" onsubmit="return confirm('¿Está seguro de eliminar esta materia?');">
                            <input type="hidden" name="action" value="eliminar">
                            <input type="hidden" name="id_materia" value="<?php echo $materia['id_materia']; ?>">
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
