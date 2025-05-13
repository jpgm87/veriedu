<?php

$NPREGUNTAS=6;


// Función para enviar texto a la API de Ollama
function enviarAOllama($prompt) {
    $payload = [
        'model' => 'kwangsuklee/gemma-3-12b-it-Q4_K_M:latest', // Cambia por el modelo que uses
        'prompt' => $prompt,
		//inicio parametros configuracion
		'temperature' => 0.7,
        'top_k' => 40,
        'top_p' => 0.9,
        'repeat_penalty' => 0.5,
		//fin parametros configuracion
		'stream' => false
    ];
	
	
    $ch = curl_init('http://localhost:11434/api/generate');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

    $response = curl_exec($ch);
	
    curl_close($ch);

    return json_decode($response, true);
}

function guardarEnArchivoTemporal($nombre,$contenido ) {
    $directorio = __DIR__ . '/uploads/tmp/';

    // Crear carpeta si no existe
    if (!is_dir($directorio)) {
        mkdir($directorio, 0777, true);
    }

    // Ruta final del archivo
    $archivo = $directorio . $nombre . '.txt';

    // Guardar contenido
    $resultado = file_put_contents($archivo, $contenido);

    return $resultado !== false ? $archivo : null;
}
function extraerDeArchivoTemporal($nombre) {
    $archivo = __DIR__ . '/uploads/tmp/' . $nombre . '.txt';

    if (file_exists($archivo)) {
        $contenido = file_get_contents($archivo);
        $contenido = mb_convert_encoding($contenido, 'UTF-8', mb_detect_encoding($contenido, 'UTF-8, ISO-8859-1, UTF-16', true));
        return $contenido;
    }

    return null;
}
function validarNLISTA($nlista) {
    // Elimina espacios y convierte a mayúsculas por si acaso
    $convierte = strtoupper(trim($nlista));

   
    $patron = '/^[0-9]+$/';

    return preg_match($patron, $convierte); 
}
?>