<?php
// Incluir la configuración de la base de datos SQLite
require_once 'db_config.php';

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar si el usuario está autenticado
function is_authenticated() {
    return isset($_SESSION['user_id']);
}

// Redirigir al login si no está autenticado
function require_auth() {
    if (!is_authenticated()) {
        // Guardar la URL actual para redirigir después del login
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
        
        // Redirigir al login
        header("Location: login.php");
        exit;
    }
}

// Función para obtener información del usuario actual
function get_user_info() {
    if (!is_authenticated()) {
        return null;
    }
    
    try {
        $db = getDBConnection();
        $stmt = $db->prepare("SELECT user_id, name, email, role FROM users WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error al obtener información del usuario: " . $e->getMessage());
        return null;
    }
}
?>
