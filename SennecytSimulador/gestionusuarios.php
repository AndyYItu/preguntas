<?php
session_start();
require_once "conexion.php";

// Función para obtener todos los usuarios
function obtenerUsuarios($conexion) {
    $result = $conexion->query("SELECT id, usuario, nombre, apellido, correo FROM usuarios");
    return ($result) ? $result->fetch_all(MYSQLI_ASSOC) : array();
}

// Función para obtener un usuario por ID
function obtenerUsuarioPorId($conexion, $id) {
    $stmt = $conexion->prepare("SELECT id, usuario, nombre, apellido, correo FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    return ($result) ? $result->fetch_assoc() : null;
}

// Función para actualizar un usuario
function actualizarUsuario($conexion, $id, $usuario, $nombre, $apellido, $correo) {
    $stmt = $conexion->prepare("UPDATE usuarios SET usuario = ?, nombre = ?, apellido = ?, correo = ? WHERE id = ?");
    $stmt->bind_param("ssssi", $usuario, $nombre, $apellido, $correo, $id);
    return $stmt->execute();
}

// Función para eliminar un usuario
function eliminarUsuario($conexion, $id) {
    $stmt = $conexion->prepare("DELETE FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['agregar'])) {
        $usuarioNuevo = $_POST['usuario'];
        $nombreNuevo = $_POST['nombre'];
        $apellidoNuevo = $_POST['apellido'];
        $correoNuevo = $_POST['correo'];
        $contrasenaNueva = $_POST['contrasena'];
    
        // Asegúrate de realizar las validaciones necesarias antes de insertar el usuario en la base de datos
    
        // Ejemplo de hash de contraseña
        $hashedPassword = password_hash($contrasenaNueva, PASSWORD_DEFAULT);
    
        $stmt = $conexion->prepare("INSERT INTO usuarios (usuario, nombre, apellido, correo, contrasena) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $usuarioNuevo, $nombreNuevo, $apellidoNuevo, $correoNuevo, $hashedPassword);
    
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Usuario agregado con éxito.";
        } else {
            $_SESSION['error_message'] = "Error al agregar el usuario.";
        }
    
        $stmt->close();
        header("Location: gestionusuarios.php");
        exit();
    }
    
    if (isset($_POST['editar'])) {
        $usuario = obtenerUsuarioPorId($conexion, $_POST['editar']);
        if ($usuario && $usuario['id'] != 4 /* ID del usuario 'admin' */) {
            // Mostrar el formulario de edición solo si no es el usuario 'admin'
            echo '<h2>Editar Usuario</h2>';
            echo '<form method="post" action="gestionusuarios.php">';
            echo '<input type="hidden" name="id_editar" value="' . $usuario['id'] . '">';
            echo '<input type="text" name="usuario_editar" placeholder="Usuario" value="' . $usuario['usuario'] . '" required>';
            echo '<input type="text" name="nombre_editar" placeholder="Nombre" value="' . $usuario['nombre'] . '" required>';
            echo '<input type="text" name="apellido_editar" placeholder="Apellido" value="' . $usuario['apellido'] . '" required>';
            echo '<input type="email" name="correo_editar" placeholder="Correo Electrónico" value="' . $usuario['correo'] . '" required>';
            echo '<input type="submit" name="guardar_edicion" value="Guardar Edición">';
            echo '</form>';
            
        }
        
    } elseif (isset($_POST['eliminar']) && $_POST['eliminar'] != 4 /* ID del usuario 'admin' */) {
        // Eliminar usuario
        if (eliminarUsuario($conexion, $_POST['eliminar'])) {
            $_SESSION['success_message'] = "Usuario eliminado con éxito.";
        } else {
            $_SESSION['error_message'] = "Error al eliminar el usuario.";
        }
        header("Location: gestionusuarios.php");
        exit();
    } elseif (isset($_POST['guardar_edicion'])) {
        // Guardar la edición
        if (actualizarUsuario($conexion, $_POST['id_editar'], $_POST['usuario_editar'], $_POST['nombre_editar'], $_POST['apellido_editar'], $_POST['correo_editar'])) {
            $_SESSION['success_message'] = "Usuario editado con éxito.";
        } else {
            $_SESSION['error_message'] = "Error al editar el usuario.";
        }
        header("Location: gestionusuarios.php");
        exit();
    }
}

$usuarios = obtenerUsuarios($conexion);
$conexion->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Usuarios</title>
    <link rel="stylesheet" href="usuarios.css">
</head>
<body>

<div class="form-container">
<a href="salaadmin.php" class="boton-volver" style="display: inline-block; padding: 10px 15px; font-size: 16px; text-align: center; text-decoration: none; cursor: pointer; border-radius: 5px; background-color: #3498db; color: #fff; transition: background-color 0.3s ease;">Volver</a>
    <h2>Agregar Nuevo Usuario</h2>
    <?php
    if (isset($_SESSION['error_message'])) {
        echo '<p class="error">' . $_SESSION['error_message'] . '</p>';
        unset($_SESSION['error_message']);
    }
    if (isset($_SESSION['success_message'])) {
        echo '<p class="success">' . $_SESSION['success_message'] . '</p>';
        unset($_SESSION['success_message']);
    }
    ?>
    <form method="post" action="gestionusuarios.php">
        <input type="text" name="usuario" placeholder="Usuario" required>
        <input type="text" name="nombre" placeholder="Nombre" required>
        <input type="text" name="apellido" placeholder="Apellido" required>
        <input type="email" name="correo" placeholder="Correo Electrónico" required>
        <input type="password" name="contrasena" placeholder="Contraseña" required>
        <input type="submit" name="agregar" value="Agregar Usuario">
    </form>
</div>

<div class="usuarios-container">
    <h2>Lista de Usuarios</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Usuario</th>
            <th>Nombre</th>
            <th>Apellido</th>
            <th>Correo Electrónico</th>
            <th>Acciones</th>
        </tr>
        <?php foreach ($usuarios as $usuario): ?>
            <tr>
                <td><?php echo $usuario['id']; ?></td>
                <td><?php echo $usuario['usuario']; ?></td>
                <td><?php echo $usuario['nombre']; ?></td>
                <td><?php echo $usuario['apellido']; ?></td>
                <td><?php echo $usuario['correo']; ?></td>
                <td>
                    <form method="post" action="gestionusuarios.php">
                        <input type="hidden" name="editar" value="<?php echo $usuario['id']; ?>">
                        <input type="submit" value="Editar">
                    </form>
                    <?php if ($usuario['id'] != 4): // No permitir eliminar al usuario 'admin' ?>
                        <form method="post" action="gestionusuarios.php">
                            <input type="hidden" name="eliminar" value="<?php echo $usuario['id']; ?>">
                            <input type="submit" value="Eliminar">
                        </form>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>

</body>
</html>


