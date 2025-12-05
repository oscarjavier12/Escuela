<?php
require_once 'config.php';

$db = new Database();
$conn = $db->connect();

// Procesar acciones
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'crear':
                $stmt = $conn->prepare("INSERT INTO Carrera (nombre) VALUES (?)");
                $stmt->execute([$_POST['nombre']]);
                header("Location: carreras.php");
                exit();
                break;
                
            case 'editar':
                $stmt = $conn->prepare("UPDATE Carrera SET nombre=? WHERE id_carrera=?");
                $stmt->execute([$_POST['nombre'], $_POST['id_carrera']]);
                header("Location: carreras.php");
                exit();
                break;
                
            case 'eliminar':
                $stmt = $conn->prepare("DELETE FROM Carrera WHERE id_carrera=?");
                $stmt->execute([$_POST['id_carrera']]);
                header("Location: carreras.php");
                exit();
                break;
        }
    }
}

// Obtener carrera para editar
$editando = null;
if (isset($_GET['editar'])) {
    $stmt = $conn->prepare("SELECT * FROM Carrera WHERE id_carrera=?");
    $stmt->execute([$_GET['editar']]);
    $editando = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Obtener todas las carreras
$stmt = $conn->query("SELECT * FROM Carrera ORDER BY id_carrera");
$carreras = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Carreras</title>
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
