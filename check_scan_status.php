<?php
// Configuración de reporte de errores y buffer de salida para garantizar una respuesta JSON
ob_start();
ini_set('display_errors', 0);
error_reporting(E_ALL);

header('Content-Type: application/json');
require_once 'db_config.php';

// Función para verificar si el escaneo está en progreso usando un archivo de bloqueo.
// Este método es mucho más rápido y fiable que consultar los procesos del sistema.
function isScannerRunning() {
    $lockFile = __DIR__ . '/scanner.lock';
    return file_exists($lockFile);
}

$response = [];

if (isScannerRunning()) {
    $response['status'] = 'processing';
    $response['message'] = 'El escaneo todavía está en progreso...';
} else {
    // El proceso ha terminado, ahora verificamos el resultado.
    $logFile = __DIR__ . '/scan_progress.log';
    $logContent = file_exists($logFile) ? file_get_contents($logFile) : '';

    // Intentamos obtener el último ID de escaneo de la base de datos
    try {
        $scan_id = getLastScanId();
        if ($scan_id) {
            $response['status'] = 'completed';
            $response['scan_id'] = $scan_id;
            $response['message'] = 'Escaneo completado.';
            // Opcional: limpiar el log si todo fue bien
            // unlink($logFile);
        } else {
            // No se encontró un nuevo escaneo, puede que el script fallara al inicio
            $response['status'] = 'error';
            $response['message'] = 'El escaneo terminó, pero no se pudo encontrar un nuevo registro en la base de datos.';
            $response['log'] = $logContent;
        }
    } catch (Exception $e) {
        $response['status'] = 'error';
        $response['message'] = 'Error al conectar con la base de datos para verificar el resultado.';
        $response['details'] = $e->getMessage();
        $response['log'] = $logContent;
    }
}

// Bloque finally para asegurar que siempre se envíe una respuesta JSON válida
$debug_output = ob_get_clean();
if (!empty($debug_output)) {
    $response['debug'] = $debug_output;
}

// Si después de todo, la respuesta está vacía, creamos una de error
if (empty($response)) {
    $response = ['status' => 'error', 'message' => 'Respuesta vacía desde el servidor de estado.'];
}

echo json_encode($response);
?>