<?php
session_start();

$tiempoLimite = 20 * 60; 
$nombreUsuario = $_SESSION['usuario'];


require_once "conexion.php";

if (!isset($_SESSION['tiempo_limite_biologia']) || time() > $_SESSION['tiempo_limite_biologia']) {
    $_SESSION['tiempo_limite_biologia'] = time() + $tiempoLimite;
}

$materia = 'biologia';

// Verifica si el usuario ya vio la nota final
if (isset($_SESSION['nota_vista_biologia']) && $_SESSION['nota_vista_biologia']) {
    mostrarNota($_SESSION['nota_biologia']);
    exit();
}

if (empty($_SESSION['preguntas_seleccionadas'][$materia]) || count($_SESSION['preguntas_seleccionadas'][$materia]) === 0) {
    $_SESSION['preguntas_seleccionadas'][$materia] = seleccionarPreguntasAleatorias($conexion, "preguntas_$materia", 50);
}

if (!isset($_SESSION['respuestas'][$materia])) {
    $_SESSION['respuestas'][$materia] = array_fill(0, count($_SESSION['preguntas_seleccionadas'][$materia]), null);
}

if (isset($_POST['cerrar_sesion'])) {
    session_unset();
    session_destroy();
    header("Location: index.php");
    exit();
}

// Comprueba si ya se ha terminado el intento
if (time() > $_SESSION['tiempo_limite_biologia'] || todosContestados($_SESSION['respuestas'][$materia])) {
    $nota = calcularNota($_SESSION['preguntas_seleccionadas'][$materia], $_SESSION['respuestas'][$materia]);
    $_SESSION['nota_biologia'] = $nota;
    $_SESSION['nota_vista_biologia'] = true;
    mostrarNota($nota);
    exit();
}

if (isset($_POST['evaluar'])) {
    $preguntaIndex = isset($_GET['pregunta']) ? intval($_GET['pregunta']) : 0;

    // Verificar si se ha seleccionado una respuesta para la pregunta actual
    if (isset($_POST['respuesta_' . $preguntaIndex])) {
        // Almacenar la respuesta seleccionada en la sesión
        $_SESSION['respuestas'][$materia][$preguntaIndex] = $_POST['respuesta_' . $preguntaIndex];

        // Incrementar el índice de la pregunta para pasar a la siguiente
        $preguntaIndex++;

        // Redirigir a la siguiente pregunta o finalizar si es la última pregunta
        if ($preguntaIndex < count($_SESSION['preguntas_seleccionadas'][$materia])) {
            header("Location: preguntas_biologia.php?pregunta=$preguntaIndex");
            exit();
        } else {
            header("Location: preguntas_biologia.php?terminar_intento=1");
            exit();
        }
    }
}

if (isset($_POST['terminar_intento'])) {
    // Mostrar la nota final si se ha hecho clic en "Terminar Intento"
    $nota = calcularNota($_SESSION['preguntas_seleccionadas'][$materia], $_SESSION['respuestas'][$materia]);
    $_SESSION['nota_biologia'] = $nota;
    $_SESSION['nota_vista_biologia'] = true;
    mostrarNota($nota);
    exit();
}

$preguntaIndex = isset($_GET['pregunta']) ? intval($_GET['pregunta']) : 0;
$preguntaActual = $_SESSION['preguntas_seleccionadas'][$materia][$preguntaIndex];

$conexion->close();

function seleccionarPreguntasAleatorias($conexion, $tabla, $cantidad)
{
    $preguntas = [];
    // Obtener preguntas aleatorias de la tabla
    $query = "SELECT * FROM $tabla ORDER BY RAND() LIMIT $cantidad";
    $result = $conexion->query($query);

    while ($row = $result->fetch_assoc()) {
        $preguntas[] = $row;
    }

    return $preguntas;
}

function calcularNota($preguntas, $respuestasUsuario)
{
    $preguntasCorrectas = 0;

    foreach ($preguntas as $key => $pregunta) {
        if (isset($respuestasUsuario[$key]) && $respuestasUsuario[$key] == $pregunta['respuesta_correcta']) {
            $preguntasCorrectas++;
        }
    }

    $nota = ($preguntasCorrectas / count($preguntas)) * 10;

    return round($nota, 2);
}

function mostrarNota($nota)
{
    echo "<div class='resultado-container' style='text-align: center;'>";
    echo "<p id='agradecimiento' style='font-size: 25px; font-weight: bold; color: #ff0000;'>Tu intento ha concluido</p>";
    echo "<p style='font-size: 18px; font-weight: bold; color: #000000; '>¡Gracias por completar el simulador!</p>";
    echo "<img src='imagenes/final.png' alt='final' style='width: 20%; height: auto;'>";
    echo "<p style='font-size: 18px; '>TU NOTA ES:</p>";
    echo "<p class='nota-final' style='font-size: 36px;'>$nota / 10</p>";
    echo "<a href='materias.php' class='boton regresar-materias-boton' style='background-color: tomato; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Regresar a Materias</a>";
    echo "</div>";

    // Agregar el script para hacer que el texto parpadee
    echo "<script>";
    echo "function parpadear() {";
    echo "  var elemento = document.getElementById('agradecimiento');";
    echo "  setInterval(function() {";
    echo "    elemento.style.visibility = (elemento.style.visibility === 'hidden') ? 'visible' : 'hidden';";
    echo "  }, 500);";  // Cambia la visibilidad cada 500 milisegundos (0.5 segundos)
    echo "}";
    echo "parpadear();";  // Iniciar la función de parpadeo cuando se carga la página
    echo "</script>";
}


function formatoTiempo($segundos)
{
    $minutos = floor($segundos / 60);
    $segundosRestantes = $segundos % 60;
    return $minutos . "m " . $segundosRestantes . "s";
}

function todosContestados($respuestas)
{
    return !in_array(null, $respuestas, true);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Preguntas de Biología</title>
    <link rel="stylesheet" href="preguntas.css">

    <script>
        var tiempoRestante = <?php echo $_SESSION['tiempo_limite_biologia'] - time(); ?>;
        function actualizarTemporizador() {
            if (tiempoRestante > 0) {
                tiempoRestante--;
                document.getElementById('temporizador').innerHTML = "Tiempo restante: " + formatoTiempo(tiempoRestante);
                setTimeout(actualizarTemporizador, 1000);
            } else {
                window.location.href = 'preguntas_biologia.php?terminar_intento=1';
            }
        }

        function formatoTiempo(segundos) {
            var minutos = Math.floor(segundos / 60);
            var segundosRestantes = segundos % 60;
            return minutos + "m " + segundosRestantes + "s";
        }

        window.onload = function () {
            actualizarTemporizador();
        };
    </script>
</head>
<body>

<div class="cerrar-sesion">
    <form method="post" action="">
        <input class="boton" type="submit" name="cerrar_sesion" value="Cerrar Sesión">
    </form>
</div>

<div class="bienvenido">
    <h2>Bienvenido al Test de Biología</h2>
    <img src="imagenes/imagenesbiologia.png" alt="Descripción de la imagen" style="width: 5%; height: auto;">
</div>

<div class="temporizador" id="temporizador" style="width: 300px; margin: 20px; background-color: #3498db; color: #ffffff; border-radius: 500px; padding: 20px; text-align: center; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">
    <h2 style="font-size: 160px; margin-bottom: 20px;">Tiempo Restante <img src="imagenes/imagenesquimica.png" alt="Descripción de la imagen" style="width: 5%; height: auto;"></h2>
    <p style="font-size: 20px; font-weight: bold;"><?php echo formatoTiempo($tiempoLimite); ?></p>
</div>




<div class="simulador-container">
    <?php
    if (!empty($_SESSION['preguntas_seleccionadas'][$materia])) {
        $preguntaIndex = isset($_GET['pregunta']) ? intval($_GET['pregunta']) : 0;
        $preguntaActual = $_SESSION['preguntas_seleccionadas'][$materia][$preguntaIndex];

        echo "<div class='pregunta'>";
        echo "<p>Pregunta " . ($preguntaIndex + 1) . ": " . $preguntaActual['enunciado'] . "</p>";
        echo "<form method='post' action='?pregunta=$preguntaIndex'>";

        $opciones = [
            $preguntaActual['respuesta_correcta'],
            $preguntaActual['respuesta_incorrecta1'],
            $preguntaActual['respuesta_incorrecta2'],
            $preguntaActual['respuesta_incorrecta3'],
        ];

        shuffle($opciones);

        foreach ($opciones as $opcion) {
            $checked = ($opcion == $_SESSION['respuestas'][$materia][$preguntaIndex]) ? 'checked' : '';
            echo "<input type='radio' name='respuesta_$preguntaIndex' value='$opcion' $checked> $opcion<br>";
        }

        echo "<input class='boton' type='submit' name='evaluar' value='Siguiente'>";
        echo "</form>";
        echo "</div>";

        echo "<div class='tabla-preguntas'>";
        echo "<table>";
        echo "<tr><th>Preguntas</th></tr>";
        echo "<tr><td>";
        foreach ($_SESSION['preguntas_seleccionadas'][$materia] as $key => $pregunta) {
            $respuestaSeleccionada = '';
            if (isset($_SESSION['respuestas'][$materia][$key]) && $_SESSION['respuestas'][$materia][$key] !== null) {
                $respuestaSeleccionada = $_SESSION['respuestas'][$materia][$key];
            }
            $claseContestada = ($respuestaSeleccionada !== '') ? ' contestada' : '';
            echo "<a href='preguntas_biologia.php?pregunta=$key' class='cuadro-pregunta$claseContestada'>" . ($key + 1) . "</a>";
        }
        echo "<form method='post' action=''>";
        echo "<input class='boton terminar' type='submit' name='terminar_intento' value='Terminar Intento'>";
        echo "</form>";
        echo "</td></tr>";

        echo "</table>";
        echo "</div>";

    } else {
        echo "<p>No hay preguntas disponibles en este momento.</p>";
    }
    ?>
</div>

</body>
</html>







