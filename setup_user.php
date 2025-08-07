<?php
// Incluir la configuración de la base de datos
require_once 'db_config.php';

// Configurar la base de datos si no existe
setupDatabase();

// Conexión a la base de datos
$db = getDBConnection();

// Verificar si ya existe un usuario administrador
$stmt = $db->prepare("SELECT COUNT(*) as count FROM users WHERE email = ?");
$admin_email = 'admin@ejemplo.com';
$stmt->execute([$admin_email]);
$result = $stmt->fetch(PDO::FETCH_ASSOC);

if ($result['count'] == 0) {
    // Crear usuario administrador
    $name = 'Administrador';
    $password = password_hash('admin123', PASSWORD_DEFAULT);
    
    $stmt = $db->prepare("INSERT INTO users (name, email, password, role, created_at) VALUES (?, ?, ?, ?, datetime('now'))");
    $stmt->execute([$name, $admin_email, $password, 'admin']);
    
    echo "Usuario administrador creado con éxito:<br>";
    echo "Email: $admin_email<br>";
    echo "Contraseña: admin123<br>";
} else {
    echo "El usuario administrador ya existe.<br>";
}

// Verificar si ya existe un usuario normal
$stmt = $db->prepare("SELECT COUNT(*) as count FROM users WHERE email = ?");
$user_email = 'usuario@ejemplo.com';
$stmt->execute([$user_email]);
$result = $stmt->fetch(PDO::FETCH_ASSOC);

if ($result['count'] == 0) {
    // Crear usuario normal
    $name = 'Usuario de Prueba';
    $password = password_hash('usuario123', PASSWORD_DEFAULT);
    
    $stmt = $db->prepare("INSERT INTO users (name, email, password, role, created_at) VALUES (?, ?, ?, ?, datetime('now'))");
    $stmt->execute([$name, $user_email, $password, 'user']);
    
    echo "<br>Usuario normal creado con éxito:<br>";
    echo "Email: $user_email<br>";
    echo "Contraseña: usuario123<br>";
} else {
    echo "<br>El usuario normal ya existe.<br>";
}

echo "<br><a href='login.php'>Ir a la página de login</a>";
?>
