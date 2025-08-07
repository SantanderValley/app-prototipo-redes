<?php
// Incluir la configuración de la base de datos
require_once 'db_config.php';

// Configurar la base de datos y mostrar mensaje
echo "<h2>Prueba de Conexión SQLite</h2>";

try {
    // Configurar la base de datos
    setupDatabase();
    echo "<p style='color:green'>✓ Base de datos configurada correctamente.</p>";
    
    // Probar conexión
    $db = getDBConnection();
    echo "<p style='color:green'>✓ Conexión a la base de datos establecida correctamente.</p>";
    
    // Verificar tablas
    $result = $db->query("SELECT name FROM sqlite_master WHERE type='table';");
    $tables = $result->fetchAll(PDO::FETCH_COLUMN);
    
    echo "<h3>Tablas en la base de datos:</h3>";
    echo "<ul>";
    foreach ($tables as $table) {
        echo "<li>$table</li>";
        
        // Contar registros en cada tabla
        $count = $db->query("SELECT COUNT(*) FROM $table")->fetchColumn();
        echo " ($count registros)";
    }
    echo "</ul>";
    
    echo "<p><a href='setup_user.php' class='button'>Configurar Usuarios de Prueba</a></p>";
    echo "<p><a href='login.php' class='button'>Ir a la página de Login</a></p>";
    
} catch (PDOException $e) {
    echo "<p style='color:red'>✗ Error: " . $e->getMessage() . "</p>";
}
?>

<style>
    body {
        font-family: Arial, sans-serif;
        max-width: 800px;
        margin: 0 auto;
        padding: 20px;
        line-height: 1.6;
    }
    h2, h3 {
        color: #333;
    }
    ul {
        background-color: #f9f9f9;
        border: 1px solid #ddd;
        border-radius: 4px;
        padding: 15px 15px 15px 40px;
    }
    li {
        margin-bottom: 8px;
    }
    .button {
        display: inline-block;
        background-color: #4CAF50;
        color: white;
        padding: 10px 15px;
        text-decoration: none;
        border-radius: 4px;
        margin-right: 10px;
    }
</style>
