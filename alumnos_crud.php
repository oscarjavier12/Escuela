<?php
require_once 'config.php';

$db = new Database();
$conn = $db->connect();

// Procesar acciones
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'crear':
                $stmt = $conn->prepare("INSERT INTO Alumno (num_control, nombre, apellido, id_carrera) VALUES (?, ?, ?, ?)");
                $stmt->execute([$_POST['num_control'], $_POST['nombre'], $_POST['apellido'], $_POST['id_carrera']]);
                header("Location: alumnos.php");
                exit();
                break;
                
            case 'editar':
                $stmt = $conn->prepare("UPDATE Alumno SET nombre=?, apellido=?, id_carrera=? WHERE num_control=?");
                $stmt->execute([$_POST['nombre'], $_POST['apellido'], $_POST['id_carrera'], $_POST['num_control']]);
                header("Location: alumnos.php");
                exit();
                break;
                
            case 'eliminar':
                $stmt = $conn->prepare("DELETE FROM Alumno WHERE num_control=?");
                $stmt->execute([$_POST['num_control']]);
                header("Location: alumnos.php");
                exit();
                break;
        }
    }
}

// Obtener alumno para editar
$editando = null;
if (isset($_GET['editar'])) {
    $stmt = $conn->prepare("SELECT * FROM Alumno WHERE num_control=?");
    $stmt->execute([$_GET['editar']]);
    $editando = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Obtener todos los alumnos con su carrera
$stmt = $conn->query("
    SELECT a.*, c.nombre as nombre_carrera 
    FROM Alumno a 
    JOIN Carrera c ON a.id_carrera = c.id_carrera 
    ORDER BY a.num_control
");
$alumnos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener carreras para el select
$stmt = $conn->query("SELECT * FROM Carrera ORDER BY nombre");
$carreras = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Alumnos</title>
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
            max-width: 1200px;
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
