<?php
// Iniciar sesión
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

// Verificar si es una petición POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

// Verificar si se proporcionó un ID
if (!isset($_POST['scan_id']) || empty($_POST['scan_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'ID de escaneo no proporcionado']);
    exit;
}

$scan_id = (int)$_POST['scan_id'];

// Incluir la configuración de la base de datos centralizada
require_once 'db_config.php';

// Obtener la conexión a la base de datos
$pdo = getDBConnection();

if (!$pdo) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Error de conexión a la base de datos.']);
    exit;
}

try {
    // Iniciar transacción
    $pdo->beginTransaction();

    // 1. Eliminar vulnerabilidades asociadas al escaneo
    $stmt = $pdo->prepare("DELETE FROM vulnerabilities WHERE scan_id = ?");
    $stmt->execute([$scan_id]);

    // 2. Eliminar puertos abiertos asociados a los dispositivos del escaneo
    $stmt = $pdo->prepare("DELETE FROM open_ports WHERE device_id IN (SELECT device_id FROM devices WHERE scan_id = ?)");
    $stmt->execute([$scan_id]);

    // 3. Eliminar dispositivos asociados al escaneo
    $stmt = $pdo->prepare("DELETE FROM devices WHERE scan_id = ?");
    $stmt->execute([$scan_id]);

    // 4. Eliminar la información de la red WiFi del escaneo
    $stmt = $pdo->prepare("DELETE FROM wifi_networks WHERE scan_id = ?");
    $stmt->execute([$scan_id]);

    // 5. Eliminar las recomendaciones de IA
    $stmt = $pdo->prepare("DELETE FROM ai_recommendations WHERE scan_id = ?");
    $stmt->execute([$scan_id]);

    // 6. Eliminar el escaneo principal
    $stmt = $pdo->prepare("DELETE FROM scans WHERE scan_id = ?");
    $stmt->execute([$scan_id]);

    // Confirmar la transacción
    $pdo->commit();

    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'message' => 'Escaneo eliminado correctamente.']);

} catch (PDOException $e) {
    // Si hay un error, revertir la transacción
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    header('Content-Type: application/json');
    // No mostrar detalles del error en producción por seguridad
    error_log('Error en delete_scan.php: ' . $e->getMessage()); // Registrar el error
    echo json_encode(['success' => false, 'message' => 'Error al procesar la solicitud.']);

} finally {
    // Cerrar la conexión
    $pdo = null;
}
?>
