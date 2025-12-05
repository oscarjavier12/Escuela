<?php
require_once 'config.php';

$db = new Database();
$conn = $db->connect();

// Procesar acciones
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'crear':
                $stmt = $conn->prepare("INSERT INTO Materia (nombre_materia, creditos) VALUES (?, ?)");
                $stmt->execute([$_POST['nombre_materia'], $_POST['creditos']]);
                header("Location: materias.php");
                exit();
                break;
                
            case 'editar':
                $stmt = $conn->prepare("UPDATE Materia SET nombre_materia=?, creditos=? WHERE id_materia=?");
                $stmt->execute([$_POST['nombre_materia'], $_POST['creditos'], $_POST['id_materia']]);
                header("Location: materias.php");
                exit();
                break;
                
            case 'eliminar':
                $stmt = $conn->prepare("DELETE FROM Materia WHERE id_materia=?");
                $stmt->execute([$_POST['id_materia']]);
                header("Location: materias.php");
                exit();
                break;
        }
    }
}

// Obtener materia para editar
$editando = null;
if (isset($_GET['editar'])) {
    $stmt = $conn->prepare("SELECT * FROM Materia WHERE id_materia=?");
    $stmt->execute([$_GET['editar']]);
    $editando = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Obtener todas las materias
$stmt = $conn->query("SELECT * FROM Materia ORDER BY nombre_materia");
$materias = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Materias</title>
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
            max-width: 1000px;
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
        
        input {
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
        }
        
        th, td {
            padding: 12px;
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
            gap: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="index.php" class="btn-volver">← Volver al Menú</a>
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
