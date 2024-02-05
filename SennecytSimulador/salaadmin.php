<?php
session_start();

// Verificar si el usuario es el administrador
if (!isset($_SESSION['usuario']) || $_SESSION['usuario'] != 'admin@gmail.com') {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Sala de Administración</title>
    <link rel="stylesheet" href="salaadmin.css">
</head>
<body>

<div class="menu-admin">
    <h2>Panel de Administración</h2>
    <nav class="menu-nav">
        <ul>
            <li class="dropdown">
                <a href="javascript:void(0)">Editar Materias</a>
                <div class="dropdown-content">
                    <a href="biologiacrud.php">Biologia</a>
                    <a href="estrategiascrud.php">Estrategias Cognitivas</a>
                    <a href="fisicacrud.php">Fisica</a>
                    <a href="historiacrud.php">Historia</a>
                    <a href="lenguacrud.php">Lengua</a>
                    <a href="matematicascrud.php">Matematicas</a>
                    <a href="quimicacrud.php">Quimica</a>
                </div>
            </li>
            <li><a href="gestionusuarios.php">Gestionar Usuarios</a></li>
        </ul>
    </nav>

    <div class="cerrar-sesion">
        <form method="post" action="logout.php">
            <input type="submit" value="Cerrar Sesión">
        </form>
       
    </div>
    
</div>
<img src="imagenes/logo.jpg" alt="Imagen de cierre de sesión" class="logo-img" style="width: 430px; display: block; margin: auto;">

</body>
</html>
