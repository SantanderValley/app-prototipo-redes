<?php
// --- CONFIGURACIÓN DE ENTORNO ROBUSTA ---

// Desactivar el límite de tiempo de ejecución de PHP. 
// Es crucial para scripts largos como el nuestro.
set_time_limit(0);

// Aumentar el límite de memoria para manejar grandes volúmenes de datos del escaneo.
ini_set('memory_limit', '512M');

// Asegurar que todo el output sea capturado, incluso si el script falla.
ob_start();

// Configuración de reporte de errores para depuración.
ini_set('display_errors', 0); // No mostrar errores al usuario final.
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php_errors.log');
error_reporting(E_ALL);

// Iniciar el buffer de salida para capturar cualquier error o salida inesperada
ob_start();

// Establecer la cabecera JSON desde el principio
header('Content-Type: application/json');

// Respuesta por defecto en caso de fallo catastrófico
$response = ['success' => false, 'message' => 'Error inesperado en el servidor.'];

try {
    // Verificar si el usuario está logueado
    session_start();
    if (!isset($_SESSION['user_id'])) {
        throw new Exception('No autorizado');
    }


// Función para ejecutar el script Python
// Función para ejecutar el script Python en segundo plano
function runScript() {
    // Ruta al ejecutable de Python y al script
    $python_path = __DIR__ . '/python/python.exe';
    $script_path = __DIR__ . '/wifi_scanner_sqlite.py';

    // Verificar que los archivos existen
    if (!file_exists($python_path)) {
        error_log("ERROR: Intérprete de Python no encontrado en: {$python_path}");
        return ['success' => false, 'message' => 'Intérprete de Python no encontrado.'];
    }
    if (!file_exists($script_path)) {
        error_log("ERROR: Script not found: {$script_path}");
        return ['success' => false, 'message' => 'Script de escaneo no encontrado.'];
    }

    // Comando para ejecutar el script en segundo plano en Windows
    // Se usa start /B para que no abra una nueva ventana de consola
    // La salida se redirige a un log para depuración, pero el script PHP no espera
    $logFile = __DIR__ . '/scan_progress.log';
    // Asegurarse de que las rutas con espacios estén entre comillas
    $command = sprintf('start /B "" "%s" "%s" > "%s" 2>&1', $python_path, $script_path, $logFile);
    
    error_log("Ejecutando comando en segundo plano: {$command}");

    // Ejecutar el comando y cerrar el handle inmediatamente
    pclose(popen($command, 'r'));

    // Devolver inmediatamente una respuesta indicando que el proceso ha comenzado
    return [
        'success' => true,
        'status' => 'processing',
        'message' => 'El escaneo ha comenzado en segundo plano. Por favor, espere.'
    ];
}

// Verificar si se está enviando un parámetro de forzar redirección
$force_redirect = isset($_GET['redirect']) ? $_GET['redirect'] === '1' : false;

// Incluir configuración de base de datos SQLite
require_once 'db_config.php';

    // Lógica principal: ejecutar el script y procesar
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        // Iniciar el escaneo en segundo plano
        $result = runScript();
        $response = $result;
    } else {
        // Manejar el caso de acceso no-AJAX si es necesario
        throw new Exception('Acceso no válido. Se requiere una petición AJAX.');
    }

} catch (Exception $e) {
    // Si algo falla, capturamos la excepción y la preparamos para la respuesta JSON
    $response['message'] = $e->getMessage();
    // También podemos añadir más detalles si es necesario, como el fichero y la línea
    // $response['details'] = ['file' => $e->getFile(), 'line' => $e->getLine()];
} finally {
    // Limpiar el buffer de salida y añadir cualquier contenido inesperado al JSON
    $extra_output = ob_get_clean();
    if (!empty($extra_output)) {
        $response['debug_output'] = $extra_output;
    }

    // Asegurarse de que siempre se devuelva un JSON válido
    if (!headers_sent()) {
        header('Content-Type: application/json');
    }
    echo json_encode($response);
    exit;
}
