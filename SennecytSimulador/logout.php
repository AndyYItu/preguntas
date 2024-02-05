<?php
session_start();

// Cerrar sesión
session_destroy();

// Redirigir a la página de inicio
header("Location: index.php");
exit();
?>
