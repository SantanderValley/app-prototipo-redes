<?php
// Script para mostrar los usuarios en la base de datos

// Incluir la configuración de la base de datos
require_once 'db_config.php';

echo "<h1>Usuarios en la base de datos</h1>";

try {
    // Establecer conexión con la base de datos
    $db = new PDO('sqlite:' . __DIR__ . '/data/network_security.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Consultar usuarios
    $stmt = $db->query("SELECT user_id, name, email, role FROM users");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($users) > 0) {
        echo "<table border='1'>";
        echo "<tr><th>ID</th><th>Nombre</th><th>Email</th><th>Rol</th></tr>";
        
        foreach ($users as $user) {
            echo "<tr>";
            echo "<td>" . $user['user_id'] . "</td>";
            echo "<td>" . $user['name'] . "</td>";
            echo "<td>" . $user['email'] . "</td>";
            echo "<td>" . $user['role'] . "</td>";
            echo "</tr>";
        }
        
        echo "</table>";
        
        echo "<p>Para iniciar sesión, use cualquiera de estos emails con la contraseña: <strong>password123</strong> (Esta es una contraseña común para entornos de desarrollo)</p>";
        echo "<p>Si esa contraseña no funciona, también pruebe con: <strong>admin123</strong> o <strong>123456</strong></p>";
    } else {
        echo "<p>No se encontraron usuarios en la base de datos.</p>";
        
        // Crear un usuario de prueba
        echo "<h2>Creando usuario de prueba...</h2>";
        
        // Hash de la contraseña "admin123"
        $password_hash = password_hash("admin123", PASSWORD_DEFAULT);
        
        // Insertar usuario de prueba
        $insert = $db->prepare("INSERT INTO users (name, email, password, role, created_at) VALUES (?, ?, ?, ?, datetime('now'))");
        $result = $insert->execute(["Administrador", "admin@example.com", $password_hash, "admin"]);
        
        if ($result) {
            echo "<p style='color:green'>Usuario de prueba creado exitosamente.</p>";
            echo "<p>Email: <strong>admin@example.com</strong></p>";
            echo "<p>Contraseña: <strong>admin123</strong></p>";
        } else {
            echo "<p style='color:red'>Error al crear usuario de prueba.</p>";
        }
    }
} catch (PDOException $e) {
    echo "<p style='color:red'>Error: " . $e->getMessage() . "</p>";
    
    // Si hay un error con la tabla de usuarios, intentar crearla
    if (strpos($e->getMessage(), "no such table") !== false) {
        echo "<h2>La tabla 'users' no existe. Intentando crearla...</h2>";
        
        try {
            $db->exec("CREATE TABLE users (
                user_id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                email TEXT UNIQUE NOT NULL,
                password TEXT NOT NULL,
                role TEXT NOT NULL DEFAULT 'user',
                created_at TEXT NOT NULL,
                last_login TEXT
            )");
            
            echo "<p style='color:green'>Tabla 'users' creada exitosamente.</p>";
            
            // Crear usuario administrador
            $password_hash = password_hash("admin123", PASSWORD_DEFAULT);
            $insert = $db->prepare("INSERT INTO users (name, email, password, role, created_at) VALUES (?, ?, ?, ?, datetime('now'))");
            $result = $insert->execute(["Administrador", "admin@example.com", $password_hash, "admin"]);
            
            if ($result) {
                echo "<p style='color:green'>Usuario administrador creado exitosamente.</p>";
                echo "<p>Email: <strong>admin@example.com</strong></p>";
                echo "<p>Contraseña: <strong>admin123</strong></p>";
            }
        } catch (PDOException $e2) {
            echo "<p style='color:red'>Error al crear la tabla: " . $e2->getMessage() . "</p>";
        }
    }
}
?>
