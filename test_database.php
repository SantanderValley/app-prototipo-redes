<?php
// Este archivo prueba la conectividad y consultas a la base de datos SQLite

// Incluir la configuración de la base de datos
require_once 'db_config.php';

// Función para mostrar resultados en formato HTML
function displayTable($title, $data) {
    echo "<h3>{$title}</h3>";
    if (empty($data)) {
        echo "<p>No hay datos disponibles.</p>";
        return;
    }
    
    echo "<table border='1' cellpadding='5' cellspacing='0'>";
    // Mostrar cabeceras
    echo "<tr>";
    foreach (array_keys($data[0]) as $header) {
        echo "<th>{$header}</th>";
    }
    echo "</tr>";
    
    // Mostrar datos
    foreach ($data as $row) {
        echo "<tr>";
        foreach ($row as $value) {
            echo "<td>" . htmlspecialchars($value) . "</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
}

// Intentar conectar a la base de datos
try {
    $db = getDBConnection();
    echo "<p>Conexión exitosa a la base de datos SQLite.</p>";
    
    // Probar consultas a las diferentes tablas
    $tables = ['users', 'scans', 'wifi_networks', 'devices', 'vulnerabilities', 'ai_recommendations', 'open_ports'];
    $results = [];
    
    foreach ($tables as $table) {
        $stmt = $db->query("SELECT * FROM {$table} LIMIT 5");
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $results[$table] = $data;
    }
    
    // Mostrar resultados
    echo "<h2>Contenido de la base de datos SQLite</h2>";
    foreach ($results as $table => $data) {
        displayTable("Tabla: {$table}", $data);
        echo "<hr>";
    }
    
    // Verificar relaciones
    echo "<h2>Verificación de relaciones entre tablas</h2>";
    
    // Obtener un escaneo y sus redes WiFi relacionadas
    $scan_stmt = $db->query("SELECT scan_id FROM scans ORDER BY scan_id DESC LIMIT 1");
    $scan = $scan_stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($scan) {
        $scan_id = $scan['scan_id'];
        echo "<p>Mostrando relaciones para el escaneo #{$scan_id}</p>";
        
        // Redes WiFi del escaneo
        $wifi_stmt = $db->prepare("SELECT * FROM wifi_networks WHERE scan_id = ?");
        $wifi_stmt->execute([$scan_id]);
        $wifi_networks = $wifi_stmt->fetchAll(PDO::FETCH_ASSOC);
        displayTable("Redes WiFi del escaneo #{$scan_id}", $wifi_networks);
        
        // Dispositivos del escaneo
        $devices_stmt = $db->prepare("SELECT * FROM devices WHERE scan_id = ?");
        $devices_stmt->execute([$scan_id]);
        $devices = $devices_stmt->fetchAll(PDO::FETCH_ASSOC);
        displayTable("Dispositivos del escaneo #{$scan_id}", $devices);
        
        // Vulnerabilidades del escaneo
        $vulnerabilities_stmt = $db->prepare("SELECT * FROM vulnerabilities WHERE scan_id = ?");
        $vulnerabilities_stmt->execute([$scan_id]);
        $vulnerabilities = $vulnerabilities_stmt->fetchAll(PDO::FETCH_ASSOC);
        displayTable("Vulnerabilidades del escaneo #{$scan_id}", $vulnerabilities);
    } else {
        echo "<p>No se encontraron escaneos en la base de datos.</p>";
    }
    
} catch (PDOException $e) {
    echo "<p>Error de conexión: " . $e->getMessage() . "</p>";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prueba de Base de Datos SQLite</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            line-height: 1.6;
        }
        h2 {
            background-color: #f0f0f0;
            padding: 10px;
            border-left: 5px solid #333;
        }
        h3 {
            color: #555;
            margin-top: 20px;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            margin-bottom: 20px;
        }
        th {
            background-color: #f0f0f0;
            text-align: left;
        }
        td, th {
            padding: 8px;
            border: 1px solid #ddd;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        p {
            margin: 10px 0;
        }
        hr {
            border: 0;
            border-top: 1px solid #eee;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <h1>Prueba de Base de Datos SQLite para el Escáner de Redes</h1>
    <p><a href="dashboard.php">Volver al Dashboard</a></p>
</body>
</html>
