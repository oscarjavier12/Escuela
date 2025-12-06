<?php
require_once 'config.php';
require_once 'Consultas.php';

$consultas = new Consultas();
// Procesar acciones
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'crear':
                $consultas->crearCarrera($_POST['nombre']);
                header("Location: carreras.php");
                exit();
                break;
                
            case 'editar':
                $consultas->editarCarrera($_POST['id_carrera'], $_POST['nombre']);
                header("Location: carreras.php");
                exit();
                break;
                
            case 'eliminar':
                $consultas->eliminarCarrera($_POST['id_carrera']);
                header("Location: carreras.php");
                exit();
                break;
        }
    }
}

// Obtener carrera para editar
$editando = null;
if (isset($_GET['editar'])) {
    $editando= $consultas->obtenerCarreraPorId($_GET['editar']);
}

// Obtener todas las carreras
$resultado = $consultas->obtenerCarreras();
$carreras = [];
while ($row = pg_fetch_assoc($resultado)) {
    $carreras[] = $row;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Carreras</title>
    <link rel="stylesheet" href="../css/carreras.css">
</head>
<body>
    <div class="container">
        <a href="../../index.php" class="btn-volver">← Volver al Menú</a>
        <h1>🎯 Gestión de Carreras</h1>
        
        <div class="form-container">
            <h2><?php echo $editando ? 'Editar Carrera' : 'Agregar Nueva Carrera'; ?></h2>
            <form method="POST">
                <input type="hidden" name="action" value="<?php echo $editando ? 'editar' : 'crear'; ?>">
                <?php if ($editando): ?>
                    <input type="hidden" name="id_carrera" value="<?php echo $editando['id_carrera']; ?>">
                <?php endif; ?>
                
                <div class="form-group">
                    <label>Nombre de la Carrera:</label>
                    <input type="text" name="nombre" required value="<?php echo $editando ? $editando['nombre'] : ''; ?>">
                </div>
                
                <button type="submit" class="btn <?php echo $editando ? 'btn-success' : 'btn-primary'; ?>">
                    <?php echo $editando ? 'Actualizar' : 'Guardar'; ?>
                </button>
                <?php if ($editando): ?>
                    <a href="carreras.php" class="btn btn-danger">Cancelar</a>
                <?php endif; ?>
            </form>
        </div>
        
        <h2>Lista de Carreras</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre de la Carrera</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($carreras as $carrera): ?>
                <tr>
                    <td><?php echo $carrera['id_carrera']; ?></td>
                    <td><?php echo $carrera['nombre']; ?></td>
                    <td class="acciones">
                        <a href="?editar=<?php echo $carrera['id_carrera']; ?>" class="btn btn-warning">Editar</a>
                        <form method="POST" style="display:inline;" onsubmit="return confirm('¿Está seguro de eliminar esta carrera?');">
                            <input type="hidden" name="action" value="eliminar">
                            <input type="hidden" name="id_carrera" value="<?php echo $carrera['id_carrera']; ?>">
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
