<?php
require_once 'config.php';

$db = new Database();
$conn = $db->connect();

// Procesar acciones
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'crear':
                $stmt = $conn->prepare("INSERT INTO Cursos (id_docente, id_materia, periodo, grupo) VALUES (?, ?, ?, ?)");
                $stmt->execute([$_POST['id_docente'], $_POST['id_materia'], $_POST['periodo'], $_POST['grupo']]);
                header("Location: cursos.php");
                exit();
                break;
                
            case 'editar':
                $stmt = $conn->prepare("UPDATE Cursos SET id_docente=?, id_materia=?, periodo=?, grupo=? WHERE id_curso=?");
                $stmt->execute([$_POST['id_docente'], $_POST['id_materia'], $_POST['periodo'], $_POST['grupo'], $_POST['id_curso']]);
                header("Location: cursos.php");
                exit();
                break;
                
            case 'eliminar':
                $stmt = $conn->prepare("DELETE FROM Cursos WHERE id_curso=?");
                $stmt->execute([$_POST['id_curso']]);
                header("Location: cursos.php");
                exit();
                break;
        }
    }
}

// Obtener curso para editar
$editando = null;
if (isset($_GET['editar'])) {
    $stmt = $conn->prepare("SELECT * FROM Cursos WHERE id_curso=?");
    $stmt->execute([$_GET['editar']]);
    $editando = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Obtener todos los cursos con información de docente y materia
$stmt = $conn->query("
    SELECT c.*, 
           d.nombre || ' ' || d.apellido as nombre_docente,
           m.nombre_materia
    FROM Cursos c
    JOIN Docente d ON c.id_docente = d.id_docente
    JOIN Materia m ON c.id_materia = m.id_materia
    ORDER BY c.periodo DESC, c.id_curso
");
$cursos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener docentes para el select
$stmt = $conn->query("SELECT id_docente, nombre, apellido FROM Docente ORDER BY apellido, nombre");
$docentes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener materias para el select
$stmt = $conn->query("SELECT id_materia, nombre_materia FROM Materia ORDER BY nombre_materia");
$materias = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Cursos</title>
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
            max-width: 1400px;
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
