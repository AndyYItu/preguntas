<?php
session_start();
require_once "conexion.php";

if (isset($_SESSION['usuario'])) {
    header("Location: materias.php");
    exit();
}

if (isset($_POST['registro'])) {
    $usuario = $_POST['usuario'];
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $correo = $_POST['correo'];
    $contrasena = $_POST['contrasena'];

    $insertar = "INSERT INTO usuarios (nombre, apellido, correo, usuario, contrasena) VALUES ('$nombre', '$apellido', '$correo', '$usuario', '$contrasena')";

    if ($conexion->query($insertar) === TRUE) {
        $mensajeRegistro = "Usuario registrado con éxito. ¡Inicia sesión!";
        header("Location: index.php");
        exit();
    } else {
        $errorRegistro = "Error al registrar el usuario: " . $conexion->error;
    }
}

$conexion->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<div class="container">
    <h2>Registro</h2>
    <form method="post" action="">
        <div class="icon"><i class="fas fa-user"></i></div>
        Usuario: <input type="text" name="usuario" required><br>
        <div class="icon"><i class="fas fa-user"></i></div>
        Nombre: <input type="text" name="nombre" required><br>
        <div class="icon"><i class="fas fa-user"></i></div>
        Apellido: <input type="text" name="apellido" required><br>
        <div class="icon"><i class="fas fa-envelope"></i></div>
        Correo: <input type="text" name="correo" required><br>
        <div class="icon"><i class="fas fa-lock"></i></div>
        Contraseña: <input type="password" name="contrasena" required><br>
        <input type="submit" name="registro" value="Registrarte">
    </form>
    <p>¿Ya tienes una cuenta? <a href="index.php">Inicia sesión aquí</a>.</p>
    <?php
    if (isset($mensajeRegistro)) echo "<p class='success-message'>$mensajeRegistro</p>";
    elseif (isset($errorRegistro)) echo "<p class='error-message'>$errorRegistro</p>";
    ?>
</div>

</body>
</html>



