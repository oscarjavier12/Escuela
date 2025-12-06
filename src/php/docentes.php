<?php
require_once 'config.php';
require_once 'Consultas.php';

$consultas = new Consultas();

// Procesar acciones
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'crear':
                $consultas->crearDocente($_POST['nombre'], $_POST['apellido'], $_POST['email']);
                header("Location: docentes.php");
                exit();
                break;
                
            case 'editar':
                $consultas->editarDocente($_POST['id_docente'], $_POST['nombre'], $_POST['apellido'], $_POST['email']);
                header("Location: docentes.php");
                exit();
                break;
                
            case 'eliminar':
                $consultas->eliminarDocente($_POST['id_docente']);
                header("Location: docentes.php");
                exit();
                break;
        }
    }
}

// Obtener docente para editar
$editando = null;
if (isset($_GET['editar'])) {
    $editando = $consultas->obtenerDocentePorId($_GET['editar']);
}

// Obtener todos los docentes
$resultado = $consultas->obtenerDocentes();
$docentes = [];
while ($row = pg_fetch_assoc($resultado)) {
    $docentes[] = $row;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Docentes</title>
    <link rel="stylesheet" href="../css/docentes.css">
</head>
<body>
    <div class="container">
        <a href="../../index.php" class="btn-volver">← Volver al Menú</a>
        <h1>👨‍🏫 Gestión de Docentes</h1>
        
        <div class="form-container">
            <h2><?php echo $editando ? 'Editar Docente' : 'Agregar Nuevo Docente'; ?></h2>
            <form method="POST">
                <input type="hidden" name="action" value="<?php echo $editando ? 'editar' : 'crear'; ?>">
                <?php if ($editando): ?>
                    <input type="hidden" name="id_docente" value="<?php echo $editando['id_docente']; ?>">
                <?php endif; ?>
                
                <div class="form-group">
                    <label>Nombre:</label>
                    <input type="text" name="nombre" required value="<?php echo $editando ? $editando['nombre'] : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label>Apellido:</label>
                    <input type="text" name="apellido" required value="<?php echo $editando ? $editando['apellido'] : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label>Email:</label>
                    <input type="email" name="email" required value="<?php echo $editando ? $editando['email'] : ''; ?>">
                </div>
                
                <button type="submit" class="btn <?php echo $editando ? 'btn-success' : 'btn-primary'; ?>">
                    <?php echo $editando ? 'Actualizar' : 'Guardar'; ?>
                </button>
                <?php if ($editando): ?>
                    <a href="docentes.php" class="btn btn-danger">Cancelar</a>
                <?php endif; ?>
            </form>
        </div>
        
        <h2>Lista de Docentes</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>Email</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($docentes as $docente): ?>
                <tr>
                    <td><?php echo $docente['id_docente']; ?></td>
                    <td><?php echo $docente['nombre']; ?></td>
                    <td><?php echo $docente['apellido']; ?></td>
                    <td><?php echo $docente['email']; ?></td>
                    <td class="acciones">
                        <a href="?editar=<?php echo $docente['id_docente']; ?>" class="btn btn-warning">Editar</a>
                        <form method="POST" style="display:inline;" onsubmit="return confirm('¿Está seguro de eliminar este docente?');">
                            <input type="hidden" name="action" value="eliminar">
                            <input type="hidden" name="id_docente" value="<?php echo $docente['id_docente']; ?>">
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
