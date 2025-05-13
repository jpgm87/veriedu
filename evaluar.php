<?php
// Cargar automáticamente las dependencias de Composer
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/funciones.php';
use Smalot\PdfParser\Parser;

$resultado = "";

$preguntasRespuestasVector=[];


// Recorremos todos los valores del formulario
foreach ($_POST as $clave => $valor) {
    // Detectar si es una clave del tipo "pregunta_"
    if (preg_match('/^pregunta_(\d+)$/', $clave, $matches)) {
        $indice = $matches[1];

        // Recuperar la pregunta y su respuesta correspondiente
        $pregunta = trim($_POST["pregunta_$indice"]);
        $respuesta = isset($_POST["respuesta_$indice"]) ? trim($_POST["respuesta_$indice"]) : "[Sin respuesta]";

        // Añadir al string resultado
        $resultado .= str_replace('"', "'", "-== ".($indice+1).". PREGUNTA: \'$pregunta\' -> RESPUESTA: \' $respuesta \'. ==-\n\n\n");
		$preguntasRespuestasVector[]=[$pregunta,$respuesta];
    }
}

// Mostrar el resultado (o puedes guardarlo, enviarlo, etc.)
$preguntasRespuestasCadena=$resultado;

$nlista=$_POST["nlista"];

//obtenemos el texto del PDF desde el archivo txt temporal
$texto=extraerDeArchivoTemporal($nlista);



//Definicion peticion a IA para evaluar las respuestas a las preguntas según el contenido de 0 a 100 puntos. He tenido que meter una pregunta incial OCULTA, ya que a veces tenía comportamientos extraños con la primera pregunta

$cadena="=== PARTIMOS DEL SIGUIENTE CONTENIDO ===\n".
"==================================\n".
"$texto\n".
"==================================\n\n\n".
"=== Y PREGUNTAS Y RESPUESTAS SOBRE DICHO CONTENIDO CONTENIDO CONTESTADAS POR EL ALUMNO ===\n".
"==================================\n".
"-== 0. PREGUNTA: '¿Qué color es un caballo blanco?' -> RESPUESTA: ' blanco '. ==-\n\n\n".
preg_replace("/[^a-zA-Z0-9áéíóúÁÉÍÓÚ :¿?ñÑ\'\.,->\n]/", "",$preguntasRespuestasCadena)."\n".
"==================================\n\n".
"=== INSTRUCCIONES IMPORTANTES ===\n".
"Dime para cada pregunta, la puntuación que tendría cada respuesta individualmente de 0 a 100 en funcion de lo correctas que sean segun el contenido. ".
"Las puntuaciones que has calculado quiero que me las devuelvas en un vector separado por coma, sin nada de texto adicional y en el orden correspondiente.";

//enviamos la peticion de evaluacion a la IA
$respuesta = enviarAOllama($cadena);


$aux=htmlspecialchars($respuesta['response']);
//cogemos puntuaciones de cada pregunta
$evaluacionCrudo = explode(",", $aux);

//quitamos espacios de cada cadena
$evaluacion = array_map('trim', $evaluacionCrudo);
?>

<!DOCTYPE html>
<html lang="es">
	<head>
		<meta charset="UTF-8">
		<title>VeriEdu Asistente de Verificación de Aprendizaje</title>
		<link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
		<link rel="stylesheet" href="css/estilo.css">
	</head>
	<body>
		<div class="container">
			<h2>VeriEdu - Asistente de Verificación de Aprendizaje</h2>
			<h3>Evaluación por la IA</h3>
				<?php
				$puntuacionFinal=0;
				foreach ($preguntasRespuestasVector as $index => $par) 
				{ ?>
				
				<div class="list-group-item items-questions">
					<strong><?php echo ($index+1); ?>. </strong><?php echo $par[0];?><br>
					<strong>Respuesta:</strong> <?php echo $par[1];?><br>
					<p class="text-<?php if ($evaluacion[$index+1]>=50){ echo "success"; }else{ echo "danger";}?> points">(PUNTOS : <?php echo $evaluacion[$index+1]; ?>)</p>
				</div>
				
				<?php 
					$puntuacionFinal+=$evaluacion[$index+1];
				} ?>
				<div class="final-score text-<?php if(round(($puntuacionFinal/count($preguntasRespuestasVector)),2)>=50){ echo "success"; }else{ echo "danger"; } ?> text-primary">
					PUNTUACIÓN FINAL: <?php echo round(($puntuacionFinal/count($preguntasRespuestasVector)),2); ?>/100
				</div>		
		</div>
	</body>
</html>