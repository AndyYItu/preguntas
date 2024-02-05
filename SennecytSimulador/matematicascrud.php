<?php
session_start();
require_once "conexion.php";

function obtenerPreguntas($conexion) {
    $result = $conexion->query("SELECT * FROM preguntas_matematicas");
    return $result->fetch_all(MYSQLI_ASSOC);
}

function crearPregunta($conexion, $enunciado, $respuesta_correcta, $respuesta_incorrecta1, $respuesta_incorrecta2, $respuesta_incorrecta3) {
    $stmt = $conexion->prepare("INSERT INTO preguntas_matematicas (enunciado, respuesta_correcta, respuesta_incorrecta1, respuesta_incorrecta2, respuesta_incorrecta3) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $enunciado, $respuesta_correcta, $respuesta_incorrecta1, $respuesta_incorrecta2, $respuesta_incorrecta3);
    return $stmt->execute();
}

function actualizarPregunta($conexion, $id, $enunciado, $respuesta_correcta, $respuesta_incorrecta1, $respuesta_incorrecta2, $respuesta_incorrecta3) {
    $stmt = $conexion->prepare("UPDATE preguntas_matematicas SET enunciado = ?, respuesta_correcta = ?, respuesta_incorrecta1 = ?, respuesta_incorrecta2 = ?, respuesta_incorrecta3 = ? WHERE id = ?");
    $stmt->bind_param("sssssi", $enunciado, $respuesta_correcta, $respuesta_incorrecta1, $respuesta_incorrecta2, $respuesta_incorrecta3, $id);
    return $stmt->execute();
}

function eliminarPregunta($conexion, $id) {
    $stmt = $conexion->prepare("DELETE FROM preguntas_matematicas WHERE id = ?");
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['crear'])) {
        crearPregunta($conexion, $_POST['enunciado'], $_POST['respuesta_correcta'], $_POST['respuesta_incorrecta1'], $_POST['respuesta_incorrecta2'], $_POST['respuesta_incorrecta3']);
    } elseif (isset($_POST['actualizar'])) {
        actualizarPregunta($conexion, $_POST['id'], $_POST['enunciado'], $_POST['respuesta_correcta'], $_POST['respuesta_incorrecta1'], $_POST['respuesta_incorrecta2'], $_POST['respuesta_incorrecta3']);
    } elseif (isset($_POST['eliminar'])) {
        eliminarPregunta($conexion, $_POST['id']);
    }
}

$preguntas = obtenerPreguntas($conexion);
$conexion->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>CRUD Matemáticas</title>
    <link rel="stylesheet" href="crud.css">
    <script>
        function editarPregunta(pregunta) {
            document.getElementById('formularioEdicion').style.display = 'block';
            document.getElementById('editarId').value = pregunta.id;
            document.getElementById('editarEnunciado').value = pregunta.enunciado;
            document.getElementById('editarRespuestaCorrecta').value = pregunta.respuesta_correcta;
            document.getElementById('editarRespuestaIncorrecta1').value = pregunta.respuesta_incorrecta1;
            document.getElementById('editarRespuestaIncorrecta2').value = pregunta.respuesta_incorrecta2;
            document.getElementById('editarRespuestaIncorrecta3').value = pregunta.respuesta_incorrecta3;
        }
    </script>
</head>
<body>

<div class="form-container" style="text-align: left; margin-top: 20px;">
    <a href="salaadmin.php" class="boton-volver" style="display: inline-block; padding: 10px 15px; font-size: 16px; text-align: center; text-decoration: none; cursor: pointer; border-radius: 5px; background-color: #3498db; color: #fff; transition: background-color 0.3s ease;">Volver</a>
    <h2>Crear Nueva Pregunta</h2>
    <form method="post" action="matematicascrud.php">
        <label for="enunciado">Enunciado:</label>
        <input type="text" name="enunciado" id="enunciado" required>

        <label for="respuesta_correcta">Respuesta Correcta:</label>
        <input type="text" name="respuesta_correcta" id="respuesta_correcta" required>

        <label for="respuesta_incorrecta1">Respuesta Incorrecta 1:</label>
        <input type="text" name="respuesta_incorrecta1" id="respuesta_incorrecta1" required>

        <label for="respuesta_incorrecta2">Respuesta Incorrecta 2:</label>
        <input type="text" name="respuesta_incorrecta2" id="respuesta_incorrecta2" required>

        <label for="respuesta_incorrecta3">Respuesta Incorrecta 3:</label>
        <input type="text" name="respuesta_incorrecta3" id="respuesta_incorrecta3" required>

        <input type="submit" name="crear" value="Crear Pregunta">
    </form>
</div>

<div class="table-container">
    <table>
        <tr>
            <th>Enunciado</th>
            <th>Respuesta Correcta</th>
            <th>Respuesta Incorrecta 1</th>
            <th>Respuesta Incorrecta 2</th>
            <th>Respuesta Incorrecta 3</th>
            <th>Acciones</th>
        </tr>
        <?php foreach ($preguntas as $pregunta): ?>
        <tr>
            <td><?php echo htmlspecialchars($pregunta['enunciado']); ?></td>
            <td><?php echo htmlspecialchars($pregunta['respuesta_correcta']); ?></td>
            <td><?php echo htmlspecialchars($pregunta['respuesta_incorrecta1']); ?></td>
            <td><?php echo htmlspecialchars($pregunta['respuesta_incorrecta2']); ?></td>
            <td><?php echo htmlspecialchars($pregunta['respuesta_incorrecta3']); ?></td>
            <td>
                <button onclick="editarPregunta(<?php echo htmlspecialchars(json_encode($pregunta)); ?>)" class="boton editar">Editar</button>
                <form method="post" action="matematicascrud.php" onsubmit="return confirm('¿Estás seguro de que deseas eliminar esta pregunta?');">
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($pregunta['id']); ?>">
                    <input type="submit" name="eliminar" value="Eliminar" class="boton eliminar">
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>

<div id="formularioEdicion" style="display:none;">
    <form method="post" action="matematicascrud.php">
        <input type="hidden" name="id" id="editarId">
        <label for="editarEnunciado">Enunciado:</label>
        <input type="text" name="enunciado" id="editarEnunciado" required>
        
        <label for="editarRespuestaCorrecta">Respuesta Correcta:</label>
        <input type="text" name="respuesta_correcta" id="editarRespuestaCorrecta" required>

        <label for="editarRespuestaIncorrecta1">Respuesta Incorrecta 1:</label>
        <input type="text" name="respuesta_incorrecta1" id="editarRespuestaIncorrecta1" required>

        <label for="editarRespuestaIncorrecta2">Respuesta Incorrecta 2:</label>
        <input type="text" name="respuesta_incorrecta2" id="editarRespuestaIncorrecta2" required>

        <label for="editarRespuestaIncorrecta3">Respuesta Incorrecta 3:</label>
        <input type="text" name="respuesta_incorrecta3" id="editarRespuestaIncorrecta3" required>

        <input type="submit" name="actualizar" value="Actualizar Pregunta">
    </form>
</div>

</body>
</html>
