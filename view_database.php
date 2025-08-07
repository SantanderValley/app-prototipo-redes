<?php
// Archivo para visualizar el contenido de la base de datos SQLite

// Configuración de la base de datos
$db_path = __DIR__ . '/data/network_security.db';

// Verificar si el archivo de la base de datos existe
if (!file_exists($db_path)) {
    die("Error: El archivo de base de datos no existe en $db_path");
}

try {
    // Conectar a la base de datos
    $db = new PDO('sqlite:' . $db_path);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Obtener la lista de tablas
    $tables_query = $db->query("SELECT name FROM sqlite_master WHERE type='table' ORDER BY name");
    $tables = $tables_query->fetchAll(PDO::FETCH_COLUMN);
    
    // Estilos CSS
    echo '<style>
        body { font-family: Arial, sans-serif; max-width: 1200px; margin: 0 auto; padding: 20px; }
        h1, h2 { color: #333; }
        table { border-collapse: collapse; width: 100%; margin-bottom: 30px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .container { margin-bottom: 40px; }
        .scan-title { background-color: #e0f7fa; padding: 10px; border-radius: 5px; }
        .no-data { color: #999; font-style: italic; }
    </style>';
    
    echo '<h1>Contenido de la Base de Datos SQLite</h1>';
    
    // Obtener los escaneos
    $scans_query = $db->query("SELECT * FROM scans ORDER BY scan_id DESC");
    $scans = $scans_query->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($scans) === 0) {
        echo '<p class="no-data">No hay escaneos registrados en la base de datos.</p>';
    } else {
        // Mostrar cada escaneo y sus datos relacionados
        foreach ($scans as $scan) {
            echo '<div class="container">';
            echo '<h2 class="scan-title">Escaneo #' . $scan['scan_id'] . ' - ' . $scan['scan_date'] . '</h2>';
            
            // Mostrar detalles del escaneo
            echo '<h3>Detalles del escaneo</h3>';
            echo '<table>';
            echo '<tr><th>ID</th><th>Fecha</th><th>Duración (s)</th><th>Dispositivos</th><th>Vulnerabilidades</th></tr>';
            echo '<tr>';
            echo '<td>' . $scan['scan_id'] . '</td>';
            echo '<td>' . $scan['scan_date'] . '</td>';
            echo '<td>' . round($scan['scan_duration'], 2) . '</td>';
            echo '<td>' . $scan['total_devices'] . '</td>';
            echo '<td>' . $scan['total_vulnerabilities'] . '</td>';
            echo '</tr>';
            echo '</table>';
            
            // Mostrar red WiFi
            $wifi_query = $db->prepare("SELECT * FROM wifi_networks WHERE scan_id = ?");
            $wifi_query->execute([$scan['scan_id']]);
            $wifi = $wifi_query->fetch(PDO::FETCH_ASSOC);
            
            if ($wifi) {
                echo '<h3>Red WiFi</h3>';
                echo '<table>';
                echo '<tr><th>SSID</th><th>Autenticación</th><th>Cifrado</th><th>Señal</th></tr>';
                echo '<tr>';
                echo '<td>' . htmlspecialchars($wifi['ssid']) . '</td>';
                echo '<td>' . htmlspecialchars($wifi['authentication']) . '</td>';
                echo '<td>' . htmlspecialchars($wifi['encryption']) . '</td>';
                echo '<td>' . htmlspecialchars($wifi['signal']) . '</td>';
                echo '</tr>';
                echo '</table>';
            }
            
            // Mostrar dispositivos
            $devices_query = $db->prepare("SELECT * FROM devices WHERE scan_id = ?");
            $devices_query->execute([$scan['scan_id']]);
            $devices = $devices_query->fetchAll(PDO::FETCH_ASSOC);
            
            if (count($devices) > 0) {
                echo '<h3>Dispositivos detectados</h3>';
                echo '<table>';
                echo '<tr><th>IP</th><th>Hostname</th><th>Última vez visto</th><th>Puertos abiertos</th></tr>';
                
                foreach ($devices as $device) {
                    // Obtener puertos para este dispositivo
                    $ports_query = $db->prepare("SELECT * FROM open_ports WHERE device_id = ?");
                    $ports_query->execute([$device['device_id']]);
                    $ports = $ports_query->fetchAll(PDO::FETCH_ASSOC);
                    
                    $ports_list = '';
                    foreach ($ports as $port) {
                        $ports_list .= $port['port_number'] . '/' . $port['service_name'] . ', ';
                    }
                    $ports_list = rtrim($ports_list, ', ');
                    
                    echo '<tr>';
                    echo '<td>' . htmlspecialchars($device['ip_address']) . '</td>';
                    echo '<td>' . htmlspecialchars($device['hostname']) . '</td>';
                    echo '<td>' . $device['last_seen'] . '</td>';
                    echo '<td>' . $ports_list . '</td>';
                    echo '</tr>';
                }
                echo '</table>';
            }
            
            // Mostrar vulnerabilidades
            $vulns_query = $db->prepare("SELECT * FROM vulnerabilities WHERE scan_id = ?");
            $vulns_query->execute([$scan['scan_id']]);
            $vulns = $vulns_query->fetchAll(PDO::FETCH_ASSOC);
            
            if (count($vulns) > 0) {
                echo '<h3>Vulnerabilidades</h3>';
                echo '<table>';
                echo '<tr><th>Tipo</th><th>Severidad</th><th>Descripción</th><th>Dispositivo afectado</th><th>Detalles</th></tr>';
                
                foreach ($vulns as $vuln) {
                    echo '<tr>';
                    echo '<td>' . htmlspecialchars($vuln['vuln_type']) . '</td>';
                    echo '<td>' . htmlspecialchars($vuln['severity']) . '</td>';
                    echo '<td>' . htmlspecialchars($vuln['description']) . '</td>';
                    echo '<td>' . htmlspecialchars($vuln['affected_device']) . '</td>';
                    echo '<td>' . htmlspecialchars($vuln['details'] ?? 'N/A') . '</td>';
                    echo '</tr>';
                }
                echo '</table>';
            }
            
            // Mostrar recomendaciones IA
            $ai_query = $db->prepare("SELECT * FROM ai_recommendations WHERE scan_id = ?");
            $ai_query->execute([$scan['scan_id']]);
            $ai_rec = $ai_query->fetch(PDO::FETCH_ASSOC);
            
            if ($ai_rec) {
                echo '<h3>Recomendaciones de IA</h3>';
                echo '<div style="white-space: pre-wrap; background-color: #f9f9f9; padding: 15px; border: 1px solid #ddd; border-radius: 5px;">';
                echo htmlspecialchars($ai_rec['recommendation']);
                echo '</div>';
            }
            
            echo '</div>'; // Fin del contenedor del escaneo
        }
    }
    
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
