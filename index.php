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
		<h3>Trabajo para autoevaluar en PDF</h3>
		<form action="preguntas.php" method="post" enctype="multipart/form-data">
			<div class="form-group">
				<label for="nlista">Nº Lista:</label><input type="text" id="nlista" name="nlista" class="form-control" required>
			</div>
			
			<div class="form-group">
				<label for="pdf">Archivo PDF:</label>
				<input type="file" name="pdf" id="pdf" class="form-control" accept="application/pdf" required>
			</div>
			<button type="submit" name="enviar_pdf" class="btn btn-primary">Enviar PDF a IA</button>
			</div>
		</form>
	</div>
</body>
</html>