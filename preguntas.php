<?php
// Cargar automáticamente las dependencias de Composer
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/funciones.php';
use Smalot\PdfParser\Parser;


// Verifica si se ha subido un archivo PDF
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['pdf']) && validarNLISTA($_POST['nlista'])) {
	
	$nlista= $_POST['nlista'];
	
    $archivoTmp = $_FILES['pdf']['tmp_name'];
    $nombreOriginal = $_FILES['pdf']['name'];

    try {
        // Parsear el PDF y extraer texto
        $parser = new Parser();
        $pdf = $parser->parseFile($archivoTmp);
        $texto = $pdf->getText();

		//Guardamos contenido texto PDF en archivo txt temporal con el nombre numero  de lista
		guardarEnArchivoTemporal($nlista,$texto);

        // enviamos peticion de generacion de preguntas a la IA
        $respuesta = enviarAOllama(" === INSTRUCCIONES IMPORTANTES ===\nGenerame ".$NPREGUNTAS." preguntas básicas para comprobar si he sido yo realmente quien ha realizado el contenido que te paso.  El formato de las preguntas que me generas quiero que sean todas en una sola linea, separadas por \"#\" y sin numeración. \n\n=== CONTENIDO ===\nEl contenido es el siguiente : ".$texto);
		

		//sacamos las preguntas separadas por #
		$preguntas = explode("#", $respuesta['response']);
		

    } catch (Exception $e) {
        echo "<h3> Error al procesar el PDF:</h3>";
        echo "<pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
    }
} else {
    echo "<h3> El Nº LIsta o el archivo PDF no son correctos.</h3>";
}
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
		<h3>Preguntas generadas por la IA - Contenido: <?php echo htmlspecialchars($nombreOriginal); ?></h3>
		<form action="evaluar.php" method="post">
			<input type="hidden" name="nlista" value="<?php echo $nlista; ?>">
			<?php foreach ($preguntas as $indice => $pregunta): ?>
				<div class='form-group'>
					<input type="hidden" name="pregunta_<?php echo ($indice+1); ?>" value="<?php echo str_replace('"', "'",$pregunta); ?>">
					<label for='respuesta_<?php echo ($indice+1); ?>'><?php echo ($indice+1).". ".htmlspecialchars($pregunta); ?></label>
					<textarea class='form-control' name='respuesta_<?php echo ($indice+1); ?>' id='respuesta_<?php echo ($indice+1); ?>' rows='3'></textarea>
				</div>
			<?php endforeach; ?>
			<button type="submit" class="btn btn-success">Enviar respuestas</button>
		</form>
	</div>
</body>
</html>