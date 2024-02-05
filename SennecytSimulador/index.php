<?php
session_start();
require_once "conexion.php";

// Redirigir si el usuario ya ha iniciado sesión
if (isset($_SESSION['usuario'])) {
    if ($_SESSION['usuario'] == 'admin@gmail.com') {
        header("Location: salaadmin.php"); // Redirige al administrador a su sala
    } else {
        header("Location: materias.php"); // Redirige a los usuarios comunes a la página de materias
    }
    exit();
}

$errorLogin = '';

if (isset($_POST['login'])) {
    $correo = $_POST['correo'];
    $contrasena = $_POST['contrasena'];

    // Protección básica contra inyecciones SQL
    $correo = mysqli_real_escape_string($conexion, $correo);
    $contrasena = mysqli_real_escape_string($conexion, $contrasena);

    $consulta = "SELECT * FROM usuarios WHERE correo='$correo' AND contrasena='$contrasena'";
    $resultado = $conexion->query($consulta);

    if ($resultado->num_rows > 0) {
        $usuario = $resultado->fetch_assoc();
        $_SESSION['usuario'] = $usuario['correo'];

        if ($usuario['correo'] == 'admin@gmail.com') {
            header("Location: salaadmin.php"); // Redirige al administrador a su sala
        } else {
            header("Location: materias.php"); // Redirige a los usuarios comunes a la página de materias
        }
        exit();
    } else {
        $errorLogin = "Correo o contraseña incorrectos";
    }
}

$conexion->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<div class="container">
    <h2>Login</h2>
    <form method="post" action="">
        <center><img src="imagenes/logo.jpg" alt="Descripción de la imagen" style="width: 25%; height: auto;"></center>
        <div class="icon"><i class="fa fa-envelope"></i></div>
        Correo: <input type="text" name="correo" required><br>
        <div class="icon"><i class="fa fa-lock"></i></div>
        Contraseña: <input type="password" name="contrasena" required><br>
        <input type="submit" name="login" value="Iniciar sesión">
    </form>
    <p>¿No tienes una cuenta? <a href="registro.php">Regístrate aquí</a>.</p>
    <?php if (!empty($errorLogin)) echo "<p class='error-message'>$errorLogin</p>"; ?>
</div>

</body>
</html>






