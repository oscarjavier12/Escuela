<?php
require_once './src/php/config.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Universitario - CRUD</title>
    <link rel="stylesheet" href="./src/css/index_page.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <h1>🎓 Sistema de Gestión Universitaria</h1>
        <p style="text-align: center; color: #666; margin-bottom: 30px;">
            Seleccione el módulo que desea gestionar
        </p>
        
        <div class="menu">
            <div class="menu-item">
                <div class="icon"><i class="fa-solid fa-chalkboard-user"></i></div>
                <a href="./src/php/docentes.php">Docentes</a>
            </div>
            <div class="menu-item">
                <div class="icon"><i class="fa-solid fa-user-graduate"></i></div>
                <a href="./src/php/alumnos.php">Alumnos</a>
            </div>
            <div class="menu-item">
                <div class="icon"><i class="fa-solid fa-book"></i></div>
                <a href="./src/php/materias.php">Materias</a>
            </div>
            <div class="menu-item">
                <div class="icon"><i class="fa-solid fa-bullseye"></i></div>
                <a href="./src/php/carreras.php">Carreras</a>
            </div>
            <div class="menu-item">
                <div class="icon"><i class="fa-solid fa-pen-to-square"></i></div>
                <a href="./src/php/cursos.php">Cursos</a>
            </div>
            <div class="menu-item">
                <div class="icon"><i class="fa-solid fa-chart-column"></i></div>
                <a href="./src/php/calificaciones.php">Calificaciones</a>
            </div>
        </div>
    </div>
</body>
</html>
