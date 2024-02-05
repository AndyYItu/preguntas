<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}

$nombreUsuario = $_SESSION['usuario'];

if (isset($_POST['cerrar_sesion'])) {
    session_unset();
    session_destroy();
    header("Location: index.php");
    exit();
}

if (isset($_POST['ir_a_index'])) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Página de Materias</title>
    <link rel="stylesheet" href="materias.css">
</head>
<body>

<div class="cerrar-sesion">
    <form method="post" action="">
        <input class="boton" type="submit" name="cerrar_sesion" value="Cerrar Sesión">
    </form>
</div>

<div class="bienvenido">
    <h2><img src="imagenes/usuario.png" alt="usuario" style="width: 55px; height: 55px; vertical-align: middle;">Bienvenido, <?php echo $nombreUsuario; ?>! </h2>
   
</div>

<div class="letras">
<h3>Bienvenido al simulador! Selecciona un simulador de una de las materias, elige una respuesta entre cuatro opciones por pregunta. <br>Finaliza antes del tiempo límite establecido y al finalizar revisa tu puntaje.</br></h3>
</div>

<div class="materias-container">
<div class="materia">
<img src="imagenes/mate.png" alt="Descripción de la imagen" style="width: 55px; height: 55px;">
    <h3>Matemáticas</h3>
    <a href="preguntas_matematicas.php">
        <button class="empezar">Empezar ➡</button>
    </a>
</div>




    <div class="materia">
    <img src="imagenes/fisi.png" alt="Descripción de la imagen" style="width: 55px; height: 55px;">
        <h3>Física</h3>
        <a href="preguntas_fisica.php">
        <button class="empezar">Empezar ➡</button>
    </a>
    </div>

    <div class="materia">
    <img src="imagenes/lengua.png" alt="Descripción de la imagen" style="width: 55px; height: 55px;">
        <h3>Lengua y Literatura</h3>
        <a href="preguntas_lengua.php">
        <button class="empezar">Empezar ➡</button>
    </a>
    </div>

    <div class="materia">
    <img src="imagenes/biologia.png" alt="Descripción de la imagen" style="width: 55px; height: 55px;">
        <h3>Biología</h3>
        <a href="preguntas_biologia.php">
        <button class="empezar">Empezar ➡</button>
    </a>
    </div>

    <div class="materia">
    <img src="imagenes/quimica.png" alt="Descripción de la imagen" style="width: 55px; height: 55px;">
        <h3>Química</h3>
        <a href="preguntas_quimica.php">
        <button class="empezar">Empezar ➡</button>
    </a>
    </div>

    <div class="materia">
    <img src="imagenes/historia.png" alt="Descripción de la imagen" style="width: 55px; height: 55px;">
        <h3>Historia</h3>
        <a href="preguntas_historia.php">
        <button class="empezar">Empezar ➡</button>
    </a>
    </div>

    <div class="materia">
    <img src="imagenes/cognitiva.png" alt="Descripción de la imagen" style="width: 55px; height: 55px;">
        <h3>Estrategias Cognitivas</h3>
        <a href="preguntas_estrategias.php">
        <button class="empezar">Empezar ➡</button>
    </a>
    </div>
</div>

</body>
</html>






