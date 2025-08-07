<?php
/**
 * Página principal - Redirige al dashboard o al login según el estado de autenticación
 * 
 * Este archivo actúa como punto de entrada principal del sistema y
 * redirige al usuario a la página adecuada según si ha iniciado sesión o no.
 */

// Comprobar si estamos usando la configuración portable correcta
$portable_ini = __DIR__ . '/portable_php.ini';

// Si no se está usando la configuración portable correcta y no estamos en modo diagnóstico
if (!file_exists($portable_ini) || !ini_get('extension_dir') || strpos(ini_get('extension_dir'), 'php_portable') === false) {
    // Guardar esta página para redirigir después
    $requested_page = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : 'index.php';
    $redirect_to = rawurlencode($requested_page);
    
    echo "<html><head><title>Redirigiendo a la versión portable</title>";
    echo "<meta http-equiv='refresh' content='3;url=iniciar.bat'>";
    echo "<style>body{font-family:Arial;background:#f0f0f0;margin:50px;text-align:center;}</style>";
    echo "</head><body>";
    echo "<h2>Configuración PHP portable requerida</h2>";
    echo "<p>Esta aplicación necesita ejecutarse con la configuración portable correcta.</p>";
    echo "<p>Redirigiendo al inicializador portable en 3 segundos...</p>";
    echo "<p><a href='iniciar.bat'>Haga clic aquí si no es redirigido automáticamente</a></p>";
    echo "</body></html>";
    exit();
}

// Incluir el archivo de verificación de autenticación
require_once 'check_auth.php';

// Verificar si el usuario está autenticado
if (is_authenticated()) {
    // Si ya está autenticado, redirigir al panel de control
    header('Location: dashboard.php');
} else {
    // Si no está autenticado, redirigir a la página de inicio de sesión
    header('Location: login.php');
}

// Asegurar que el script termine después de la redirección
exit;

