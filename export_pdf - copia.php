<?php
// Incluir el archivo de verificación de autenticación
require_once 'check_auth.php';

// Verificar que el usuario esté autenticado
require_auth();

// Incluir archivo de configuración de base de datos SQLite
require_once 'db_config.php';

// Verificar que se proporciona un ID válido
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Es necesario proporcionar un ID de escaneo válido");
}

$scan_id = (int)$_GET['id'];

// Conectar a la base de datos SQLite
try {
    $conn = getDBConnection();
} catch (PDOException $e) {
    die("Error de conexión a la base de datos: " . $e->getMessage());
}

// Obtener información del escaneo
$stmt = $conn->prepare("SELECT * FROM scans WHERE scan_id = ?");
$stmt->execute([$scan_id]);
$scan = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$scan) {
    die("No se encontró el escaneo con ID: $scan_id");
}

// Obtener información de redes WiFi
$networks_stmt = $conn->prepare("SELECT * FROM wifi_networks WHERE scan_id = ?");
$networks_stmt->execute([$scan_id]);
$all_networks = $networks_stmt->fetchAll(PDO::FETCH_ASSOC);
$networks_count = count($all_networks);

// Obtener dispositivos
$devices_stmt = $conn->prepare("SELECT * FROM devices WHERE scan_id = ?");
$devices_stmt->execute([$scan_id]);
$all_devices = $devices_stmt->fetchAll(PDO::FETCH_ASSOC);
$devices_count = count($all_devices);

// Obtener vulnerabilidades con información detallada del dispositivo
$vulnerabilities_stmt = $conn->prepare("SELECT v.*, d.hostname, d.ip_address 
    FROM vulnerabilities v 
    LEFT JOIN devices d ON v.affected_device = d.device_id OR (v.affected_device = 'Unknown' AND d.scan_id = v.scan_id) 
    WHERE v.scan_id = ?");
$vulnerabilities_stmt->execute([$scan_id]);
$raw_vulnerabilities = $vulnerabilities_stmt->fetchAll(PDO::FETCH_ASSOC);

// Agrupar vulnerabilidades por tipo, severidad y descripción
$grouped_vulnerabilities = [];
$all_vulnerabilities = [];

foreach ($raw_vulnerabilities as $vuln) {
    $key = $vuln['vuln_type'] . '-' . $vuln['severity'] . '-' . $vuln['description'];
    
    if (!isset($grouped_vulnerabilities[$key])) {
        $grouped_vulnerabilities[$key] = [
            'vuln_type' => $vuln['vuln_type'],
            'severity' => $vuln['severity'],
            'description' => $vuln['description'],
            'details' => $vuln['details'],
            'affected_devices' => []
        ];
    }
    
    // Añadir información del dispositivo
    $device_info = [];
    $device_info['hostname'] = $vuln['hostname'];
    $device_info['ip_address'] = $vuln['ip_address'];
    $device_info['affected_device'] = $vuln['affected_device'];
    $grouped_vulnerabilities[$key]['affected_devices'][] = $device_info;
}

// Convertir de nuevo a array indexado para la vista
foreach ($grouped_vulnerabilities as $group) {
    $all_vulnerabilities[] = $group;
}
$vulnerabilities_count = count($all_vulnerabilities);

// Obtener recomendaciones
$recommendations_stmt = $conn->prepare("SELECT * FROM ai_recommendations WHERE scan_id = ?");
$recommendations_stmt->execute([$scan_id]);
$all_recommendations = $recommendations_stmt->fetchAll(PDO::FETCH_ASSOC);
$recommendations_count = count($all_recommendations);

// Generar un informe HTML que se puede imprimir o convertir a PDF
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informe de Auditoría de Seguridad WiFi #<?= $scan_id ?></title>
    <!-- Tailwind CSS desde CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Incluir Chart.js para gráficas -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Iconos -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        audit: {
                            primary: '#1e3a8a',      // azul oscuro para encabezados
                            secondary: '#2563eb',   // azul para acentos
                            light: '#dbeafe',       // azul claro para fondos
                            danger: '#dc2626',      // rojo para vulnerabilidades
                            warning: '#f59e0b',     // amarillo para advertencias
                            success: '#10b981',     // verde para información positiva
                        }
                    },
                    fontFamily: {
                        'sans': ['Inter', 'ui-sans-serif', 'system-ui'],
                        'serif': ['Georgia', 'ui-serif'],
                        'mono': ['JetBrains Mono', 'ui-monospace', 'SFMono-Regular'],
                    },
                }
            }
        }
    </script>
    <style>
        @page {
            size: A4;
            margin: 2cm;
        }
        .chart-container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto 20px auto;
            height: 300px;
        }
        .charts-row {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        @media print {
            body {
                font-family: Arial, sans-serif;
                font-size: 11pt;
                margin: 20mm;
            }
            h1 {
                font-size: 18pt;
                color: #333;
                margin-bottom: 5mm;
            }
            h2 {
                font-size: 14pt;
                color: #444;
                margin-top: 10mm;
                margin-bottom: 3mm;
                border-bottom: 1px solid #ddd;
                padding-bottom: 2mm;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 5mm;
            }
            th, td {
                border: 1px solid #ddd;
                padding: 2mm;
                text-align: left;
            }
            th {
                background-color: #f5f5f5;
            }
            .severity-high {
                background-color: #ffeeee;
            }
            .severity-medium {
                background-color: #ffffee;
            }
            .severity-low {
                background-color: #eeffee;
            }
            .footer {
                margin-top: 10mm;
                font-size: 9pt;
                color: #777;
                text-align: center;
            }
            .no-print-button {
                display: none;
            }
        }
        
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        h1 {
            color: #333;
        }
        h2 {
            color: #444;
            margin-top: 30px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f5f5f5;
        }
        .severity-high {
            background-color: #ffeeee;
        }
        .severity-medium {
            background-color: #ffffee;
        }
        .severity-low {
            background-color: #eeffee;
        }
        .device-card {
            border: 1px solid #ddd;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
        }
        .footer {
            margin-top: 30px;
            font-size: 12px;
            color: #777;
            text-align: center;
        }
        .print-button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin: 20px 0;
        }
        .print-button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body class="bg-gray-50 font-sans">
    <div class="fixed bottom-4 right-4 z-50 print:hidden">
        <button onclick="window.print()" class="flex items-center space-x-2 bg-audit-secondary text-white px-4 py-2 rounded-lg shadow-lg hover:bg-blue-700 transition-colors">
            <i class="ri-file-download-line"></i>
            <span>Exportar a PDF</span>
        </button>
    </div>
    
    <div class="max-w-4xl mx-auto bg-white shadow-xl my-8 print:shadow-none print:my-0">
        <!-- Encabezado del informe -->
        <div class="bg-audit-primary text-white p-6 rounded-t-lg print:bg-audit-primary">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl text-white font-bold mb-2">Informe de Auditoría de Seguridad</h1>
                    <p class="text-blue-100">Red WiFi y Dispositivos Conectados</p>
                </div>
                <div class="text-right">
                    <p class="text-sm">Informe #<?= $scan_id ?></p>
                    <p class="text-sm text-blue-200">Generado: <?= date('d/m/Y H:i') ?></p>
                </div>
            </div>
        </div>
        
        <!-- Contenido principal -->
        <div class="p-6">
    
            <!-- Resumen Ejecutivo -->
            <div class="mb-8">
                <h2 class="text-xl font-bold text-audit-primary mb-4 pb-2 border-b border-gray-200">Resumen Ejecutivo</h2>
                <p class="text-gray-700 mb-4">Este informe presenta los resultados de una auditoría de seguridad realizada en la red WiFi y los dispositivos conectados. El escaneo fue ejecutado utilizando herramientas especializadas para la detección de vulnerabilidades y análisis de seguridad.</p>
                
                <div class="bg-audit-light p-4 rounded-lg mb-4">
                    <h3 class="font-bold text-audit-primary mb-2">Hallazgos Principales:</h3>
                    <ul class="ml-5 list-disc text-gray-700 space-y-1">
                        <li><span class="font-medium">Se detectaron <?= $scan['total_devices'] ?> dispositivos</span> conectados a la red</li>
                        <li><span class="font-medium"><?= $scan['total_vulnerabilities'] ?> vulnerabilidades</span> identificadas que requieren atención</li>
                        <li>Tiempo de escaneo: <span class="font-medium"><?= round($scan['scan_duration'], 2) ?> segundos</span></li>
                    </ul>
                </div>
            </div>

            <!-- Detalles del Escaneo -->
            <div class="mb-8">
                <h2 class="text-xl font-bold text-audit-primary mb-4 pb-2 border-b border-gray-200">Detalles del Escaneo</h2>
                
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-4">
                        <div class="flex items-center mb-1">
                            <i class="ri-calendar-check-line text-audit-secondary mr-2"></i>
                            <h3 class="font-bold text-gray-700">Fecha y Hora</h3>
                        </div>
                        <p class="text-gray-600"><?= date('d/m/Y H:i', strtotime($scan['scan_date'])) ?></p>
                    </div>
                    
                    <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-4">
                        <div class="flex items-center mb-1">
                            <i class="ri-timer-line text-audit-secondary mr-2"></i>
                            <h3 class="font-bold text-gray-700">Duración</h3>
                        </div>
                        <p class="text-gray-600"><?= round($scan['scan_duration'], 2) ?> segundos</p>
                    </div>
                    
                    <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-4">
                        <div class="flex items-center mb-1">
                            <i class="ri-router-line text-audit-secondary mr-2"></i>
                            <h3 class="font-bold text-gray-700">Dispositivos</h3>
                        </div>
                        <p class="text-gray-600"><?= $scan['total_devices'] ?> dispositivos detectados</p>
                    </div>
                    
                    <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-4 <?= $scan['total_vulnerabilities'] > 0 ? 'border-l-4 border-l-audit-danger' : 'border-l-4 border-l-audit-success' ?>">
                        <div class="flex items-center mb-1">
                            <i class="ri-error-warning-line <?= $scan['total_vulnerabilities'] > 0 ? 'text-audit-danger' : 'text-audit-success' ?> mr-2"></i>
                            <h3 class="font-bold text-gray-700">Vulnerabilidades</h3>
                        </div>
                        <p class="<?= $scan['total_vulnerabilities'] > 0 ? 'text-audit-danger' : 'text-audit-success' ?> font-medium">
                            <?= $scan['total_vulnerabilities'] ?> <?= $scan['total_vulnerabilities'] != 1 ? 'vulnerabilidades' : 'vulnerabilidad' ?> detectadas
                        </p>
                    </div>
                </div>
    
            <!-- Gráficas de Análisis -->
            <div class="mb-8">
                <h2 class="text-xl font-bold text-audit-primary mb-4 pb-2 border-b border-gray-200">Análisis Gráfico</h2>
                
                <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-4 mb-6">
                    <h3 class="font-medium text-audit-secondary mb-3">Resumen del Escaneo</h3>
                    <div class="chart-container h-60">
                        <canvas id="summaryChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Redes WiFi -->
            <div class="mb-8">
                <h2 class="text-xl font-bold text-audit-primary mb-4 pb-2 border-b border-gray-200">
                    <div class="flex items-center">
                        <i class="ri-wifi-line mr-2"></i>
                        <span>Redes WiFi Detectadas</span>
                    </div>
                </h2>
                
                <!-- Gráficas para redes WiFi -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-4">
                        <h3 class="font-medium text-audit-secondary mb-3">Distribución por Tipo de Autenticación</h3>
                        <div class="h-60">
                            <canvas id="authChart"></canvas>
                        </div>
                    </div>
                    <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-4">
                        <h3 class="font-medium text-audit-secondary mb-3">Distribución por Intensidad de Señal</h3>
                        <div class="h-60">
                            <canvas id="signalChart"></canvas>
                        </div>
                    </div>
                </div>
    
                <?php if (count($all_recommendations) > 0): ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white border border-gray-200 rounded-lg shadow-sm">
                        <thead>
                            <tr class="bg-gray-50 text-audit-primary">
                                <th class="py-2 px-4 text-left font-medium border-b">SSID</th>
                                <th class="py-2 px-4 text-left font-medium border-b">Autenticación</th>
                                <th class="py-2 px-4 text-left font-medium border-b">Cifrado</th>
                                <th class="py-2 px-4 text-left font-medium border-b">Señal</th>
                                <th class="py-2 px-4 text-left font-medium border-b">Clave de Seguridad</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php foreach ($all_networks as $network): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="py-2 px-4"><?= $network['ssid'] ?></td>
                                    <td class="py-2 px-4">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= strtolower($network['authentication']) == 'open' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800' ?>">
                                            <?= $network['authentication'] ?>
                                        </span>
                                    </td>
                                    <td class="py-2 px-4"><?= $network['encryption'] ?></td>
                                    <td class="py-2 px-4">
                                        <?php
                                        // Formatear la señal como una barra de progreso
                                        $signal_val = intval($network['signal']);
                                        $signal_color = '';
                                        if ($signal_val >= 80) $signal_color = 'bg-green-500';
                                        else if ($signal_val >= 60) $signal_color = 'bg-green-400';
                                        else if ($signal_val >= 40) $signal_color = 'bg-yellow-400';
                                        else $signal_color = 'bg-red-400';
                                        ?>
                                        <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700 mr-2">
                                            <div class="<?= $signal_color ?> h-2.5 rounded-full" style="width: <?= $signal_val ?>%"></div>
                                        </div>
                                        <span class="text-xs text-gray-500"><?= $network['signal'] ?></span>
                                    </td>
                                    <td class="py-2 px-4">
                                        <?php if ($network['security_key'] && $network['security_key'] != 'Unknown'): ?>
                                            <span class="text-green-600 font-medium"><i class="ri-lock-line mr-1"></i>Protegido</span>
                                        <?php else: ?>
                                            <span class="text-red-500 font-medium"><i class="ri-lock-unlock-line mr-1"></i>No protegido</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-lg">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="ri-error-warning-line text-yellow-400 text-xl"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-yellow-700">No se detectaron redes WiFi durante el escaneo.</p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
    
            <!-- Dispositivos Detectados -->
            <div class="mb-8">
                <h2 class="text-xl font-bold text-audit-primary mb-4 pb-2 border-b border-gray-200">
                    <div class="flex items-center">
                        <i class="ri-computer-line mr-2"></i>
                        <span>Dispositivos Detectados</span>
                    </div>
                </h2>
                
                <?php
                // Verificar si hay puertos para mostrar en la gráfica
                $has_ports = false;
                $port_counts = [];
                $devices_stmt_temp = $conn->prepare("SELECT * FROM devices WHERE scan_id = ?");
                $devices_stmt_temp->execute([$scan_id]);
                $temp_devices = $devices_stmt_temp->fetchAll(PDO::FETCH_ASSOC);
                
                foreach ($temp_devices as $device) {
                    $device_id = $device['device_id'];
                    $ports_stmt_temp = $conn->prepare("SELECT * FROM open_ports WHERE device_id = ?");
                    $ports_stmt_temp->execute([$device_id]);
                    $ports_temp = $ports_stmt_temp->fetchAll(PDO::FETCH_ASSOC);
                    
                    if (count($ports_temp) > 0) {
                        $has_ports = true;
                        break;
                    }
                }
                ?>
                
                <!-- Gráfica para puertos/servicios más comunes -->
                <?php if ($has_ports): ?>
                <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-4 mb-6">
                    <h3 class="font-medium text-audit-secondary mb-3">Servicios/Puertos Más Comunes</h3>
                    <div class="chart-container h-60">
                        <canvas id="portsChart"></canvas>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Lista de dispositivos -->
                <?php if (count($all_devices) > 0): ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <?php foreach ($all_devices as $device): ?>
                            <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">
                                <!-- Encabezado del dispositivo -->
                                <div class="border-b border-gray-200 bg-gray-50 py-3 px-4">
                                    <div class="flex justify-between items-center">
                                        <h3 class="font-bold text-audit-primary truncate">
                                            <?= $device['hostname'] != 'Unknown' ? $device['hostname'] : 'Dispositivo sin nombre' ?>
                                        </h3>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            <?= $device['ip_address'] ?>
                                        </span>
                                    </div>
                                    <div class="text-xs text-gray-500 mt-1">
                                        Última conexión: <?= date('d/m/Y H:i', strtotime($device['last_seen'])) ?>
                                    </div>
                                </div>
                                
                                <!-- Puertos abiertos -->
                                <div class="p-4">
                                    <?php
                                    // Puertos abiertos
                                    $device_id = $device['device_id'];
                                    $ports_stmt = $conn->prepare("SELECT * FROM open_ports WHERE device_id = ?");
                                    $ports_stmt->execute([$device_id]);
                                    $ports_data = $ports_stmt->fetchAll(PDO::FETCH_ASSOC);
                                    $ports_count = count($ports_data);
                                    
                                    if ($ports_count > 0): 
                                    ?>
                                        <div class="mb-2 flex items-center">
                                            <i class="ri-door-open-line text-audit-secondary mr-1"></i>
                                            <h4 class="font-medium text-gray-700">Puertos abiertos</h4>
                                            <span class="ml-2 text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full"><?= $ports_count ?></span>
                                        </div>
                                        <div class="overflow-x-auto">
                                            <table class="min-w-full divide-y divide-gray-200">
                                                <thead>
                                                    <tr class="bg-gray-50">
                                                        <th class="py-2 px-3 text-xs font-medium text-gray-500 text-left">Puerto</th>
                                                        <th class="py-2 px-3 text-xs font-medium text-gray-500 text-left">Servicio</th>
                                                        <th class="py-2 px-3 text-xs font-medium text-gray-500 text-left">Riesgo</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="divide-y divide-gray-200 bg-white">
                                                    <?php 
                                                    $high_risk_ports = [20, 21, 22, 23, 25, 53, 110, 135, 137, 138, 139, 161, 445, 1433, 1434, 3306, 3389];
                                                    $medium_risk_ports = [20, 79, 111, 119, 143, 389, 636, 993, 995, 1521, 2049, 5432, 8080];
                                                    
                                                    foreach ($ports_data as $port): 
                                                        $port_num = intval($port['port_number']);
                                                        $risk_class = '';
                                                        $risk_label = '';
                                                        
                                                        if (in_array($port_num, $high_risk_ports)) {
                                                            $risk_class = 'text-red-600';
                                                            $risk_label = 'Alto';
                                                        } else if (in_array($port_num, $medium_risk_ports)) {
                                                            $risk_class = 'text-yellow-600';
                                                            $risk_label = 'Medio';
                                                        } else {
                                                            $risk_class = 'text-green-600';
                                                            $risk_label = 'Bajo';
                                                        }
                                                    ?>
                                                        <tr class="hover:bg-gray-50">
                                                            <td class="py-2 px-3 text-sm"><?= $port['port_number'] ?></td>
                                                            <td class="py-2 px-3 text-sm font-medium"><?= $port['service_name'] ?></td>
                                                            <td class="py-2 px-3">
                                                                <span class="<?= $risk_class ?> text-xs font-medium"><?= $risk_label ?></span>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php else: ?>
                                        <div class="text-center py-4 text-gray-500">
                                            <i class="ri-shield-check-line text-xl text-green-500 mb-1"></i>
                                            <p class="text-sm">No se detectaron puertos abiertos.</p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-lg">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="ri-error-warning-line text-yellow-400 text-xl"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-yellow-700">No se detectaron dispositivos durante el escaneo.</p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
    
            <!-- Vulnerabilidades Detectadas -->
            <div class="mb-8">
                <h2 class="text-xl font-bold text-audit-primary mb-4 pb-2 border-b border-gray-200">
                    <div class="flex items-center">
                        <i class="ri-error-warning-line mr-2"></i>
                        <span>Vulnerabilidades Detectadas</span>
                    </div>
                </h2>
                
                <!-- Gráficas para vulnerabilidades -->
                <?php if (count($all_vulnerabilities) > 0): ?>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-4">
                        <h3 class="font-medium text-audit-secondary mb-3">Distribución por Severidad</h3>
                        <div class="h-60">
                            <canvas id="vulnSeverityChart"></canvas>
                        </div>
                    </div>
                    <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-4">
                        <h3 class="font-medium text-audit-secondary mb-3">Tipos de Vulnerabilidades</h3>
                        <div class="h-60">
                            <canvas id="vulnTypeChart"></canvas>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Tabla de vulnerabilidades -->
                <?php if (count($all_vulnerabilities) > 0): ?>
                    <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden mb-6">
                        <div class="border-b border-gray-200 bg-gray-50 py-3 px-4">
                            <div class="flex items-center justify-between">
                                <h3 class="font-bold text-audit-primary">Resumen de Vulnerabilidades</h3>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $scan['total_vulnerabilities'] > 0 ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' ?>">
                                    <?= $scan['total_vulnerabilities'] ?> Encontradas
                                </span>
                            </div>
                        </div>
                        
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                    <tr class="bg-gray-50">
                                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Severidad</th>
                                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dispositivo</th>
                                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descripción</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php foreach ($all_vulnerabilities as $vuln): 
                                        // Definir clases de severidad
                                        $severity = strtolower($vuln['severity']);
                                        $severity_bg = '';
                                        $severity_text = '';
                                        
                                        switch ($severity) {
                                            case 'high':
                                                $severity_bg = 'bg-red-100';
                                                $severity_text = 'text-red-800';
                                                break;
                                            case 'medium':
                                                $severity_bg = 'bg-yellow-100';
                                                $severity_text = 'text-yellow-800';
                                                break;
                                            case 'low':
                                                $severity_bg = 'bg-blue-100';
                                                $severity_text = 'text-blue-800';
                                                break;
                                            default:
                                                $severity_bg = 'bg-gray-100';
                                                $severity_text = 'text-gray-800';
                                        }
                                    ?>
                                        <tr class="hover:bg-gray-50">
                                            <td class="py-4 px-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <i class="ri-bug-line text-audit-secondary mr-2"></i>
                                                    <div class="font-medium text-gray-900"><?= $vuln['vuln_type'] ?></div>
                                                </div>
                                            </td>
                                            <td class="py-4 px-4 whitespace-nowrap">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $severity_bg ?> <?= $severity_text ?>">
                                                    <?= $vuln['severity'] ?>
                                                </span>
                                            </td>
                                            <td class="py-4 px-4 text-sm text-gray-700">
                                                <?php if (isset($vuln['affected_devices']) && is_array($vuln['affected_devices'])): ?>
                                                    <ul class="list-disc pl-5 text-sm space-y-1">
                                                        <?php foreach ($vuln['affected_devices'] as $device): ?>
                                                            <li>
                                                                <?php 
                                                                // Determinar la mejor forma de mostrar el dispositivo
                                                                if (!empty($device['hostname']) && $device['hostname'] != 'Unknown') {
                                                                    echo $device['hostname'];
                                                                    if (!empty($device['ip_address'])) {
                                                                        echo ' (' . $device['ip_address'] . ')';
                                                                    }
                                                                } elseif (!empty($device['ip_address'])) {
                                                                    echo $device['ip_address'];
                                                                } elseif ($device['affected_device'] !== 'Unknown') {
                                                                    echo $device['affected_device'];
                                                                } else {
                                                                    echo 'No identificado';
                                                                }
                                                                ?>
                                                            </li>
                                                        <?php endforeach; ?>
                                                    </ul>
                                                <?php else: ?>
                                                    No se identificaron dispositivos específicos
                                                <?php endif; ?>
                                            </td>
                                            <td class="py-4 px-4 text-sm text-gray-500">
                                                <?= $vuln['description'] ?>
                                                <?php if (!empty($vuln['details'])): ?>
                                                    <button class="ml-2 text-xs text-audit-secondary hover:underline" onclick="alert('<?= addslashes($vuln['details']) ?>')">
                                                        Ver detalles
                                                    </button>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="bg-green-50 border-l-4 border-green-400 p-4 rounded-lg">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="ri-shield-check-line text-green-400 text-xl"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-green-700">No se detectaron vulnerabilidades. La red parece estar segura.</p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
    
            <!-- Recomendaciones de Seguridad -->
            <div class="mb-8">
                <h2 class="text-xl font-bold text-audit-primary mb-4 pb-2 border-b border-gray-200">
                    <div class="flex items-center">
                        <i class="ri-shield-keyhole-line mr-2"></i>
                        <span>Recomendaciones de Seguridad</span>
                    </div>
                </h2>
                
                <?php 
                // Verificar si hay un error de API de OpenAI
                $openai_error = false;
                if (isset($_GET['openai_error']) && $_GET['openai_error'] == 'quota_exceeded') {
                    $openai_error = true;
                }
                
                if (count($all_recommendations) > 0): ?>
                    <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-5">
                        <h3 class="font-medium text-audit-secondary mb-4">Acciones recomendadas</h3>
                        <ul class="space-y-4">
                            <?php foreach ($all_recommendations as $rec): ?>
                                <li class="flex">
                                    <div class="flex-shrink-0 mt-1">
                                        <i class="ri-checkbox-circle-line text-audit-success text-xl"></i>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-gray-700"><?= nl2br($rec['recommendation']) ?></p>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php elseif ($openai_error): ?>
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-lg">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="ri-error-warning-line text-yellow-400 text-xl"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-yellow-700">No se pudieron generar recomendaciones automáticas debido a un error con el servicio de IA. La cuota de la API ha sido excedida. Por favor, contacte al administrador del sistema para actualizar la suscripción.</p>
                            </div>
                        </div>
                        <div class="mt-3 ml-8">
                            <h4 class="text-sm font-medium text-yellow-800">Recomendaciones generales:</h4>
                            <ul class="list-disc pl-5 mt-1 text-sm text-yellow-700">
                                <li>Actualice el firmware de sus dispositivos de red regularmente</li>
                                <li>Utilice contraseñas fuertes y únicas para cada servicio</li>
                                <li>Active la autenticación de dos factores cuando esté disponible</li>
                                <li>Realice escaneos de seguridad periódicamente</li>
                                <li>Mantenga un inventario actualizado de todos los dispositivos conectados</li>
                            </ul>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded-lg">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="ri-information-line text-blue-500 text-xl"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-blue-700">No hay recomendaciones específicas disponibles para este escaneo. Continuar con las prácticas habituales de seguridad de red.</p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Conclusión -->
            <div class="mb-8 bg-gray-50 border border-gray-200 rounded-lg p-5">
                <h2 class="text-lg font-bold text-audit-primary mb-3">Conclusión</h2>
                <p class="text-gray-700 mb-3">Este informe de auditoría de seguridad WiFi proporciona una evaluación detallada del estado actual de la red examinada. Se han identificado <?= $scan['total_devices'] ?> dispositivos conectados y <?= $scan['total_vulnerabilities'] ?> vulnerabilidades potenciales que requieren atención.</p>
                <p class="text-gray-700">Se recomienda realizar auditorías periódicas para mantener un nivel óptimo de seguridad en la red y proteger la información confidencial.</p>
            </div>
        </div>
        
        <!-- Pie de página -->
        <div class="bg-audit-primary text-white py-4 px-6 rounded-b-lg">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-sm font-medium">Informe de Auditoría de Seguridad WiFi</p>
                    <p class="text-xs text-blue-200 mt-1">Generado por WiFi Scanner v1.0</p>
                </div>
                <div class="text-right">
                    <p class="text-sm">&copy; <?= date('Y') ?> WiFi Scanner</p>
                    <p class="text-xs text-blue-200 mt-1">ID de Informe: <?= $scan_id ?>-<?= date('Ymd') ?></p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Botón flotante para imprimir/guardar -->
    <div class="fixed bottom-4 right-4 z-50 print:hidden">
        <button onclick="window.print()" class="flex items-center space-x-2 bg-audit-secondary text-white px-4 py-2 rounded-lg shadow-lg hover:bg-blue-700 transition-colors">
            <i class="ri-file-download-line"></i>
            <span>Exportar a PDF</span>
        </button>
    </div>
    
    <script>
        // Procesar datos para gráficas
        <?php
        // 1. Datos para gráfica de resumen
        $total_devices = $scan['total_devices'];
        $total_vulns = $scan['total_vulnerabilities'];
        
        // 2. Datos para autenticación WiFi
        $auth_types = [];
        foreach ($all_networks as $network) {
            $auth = $network['authentication'];
            if (!isset($auth_types[$auth])) {
                $auth_types[$auth] = 1;
            } else {
                $auth_types[$auth]++;
            }
        }
        
        // 3. Datos para intensidad de señal
        $signal_levels = ['Excelente' => 0, 'Buena' => 0, 'Regular' => 0, 'Débil' => 0];
        foreach ($all_networks as $network) {
            $signal = $network['signal'];
            // Categorizar la señal
            if (strpos($signal, '%') !== false) {
                $signal_val = intval($signal);
                if ($signal_val >= 80) $signal_levels['Excelente']++;
                else if ($signal_val >= 60) $signal_levels['Buena']++;
                else if ($signal_val >= 40) $signal_levels['Regular']++;
                else $signal_levels['Débil']++;
            }
        }
        
        // 4. Datos para puertos más comunes
        $port_counts = [];
        foreach ($all_devices as $device) {
            $device_id = $device['device_id'];
            $ports_stmt = $conn->prepare("SELECT * FROM open_ports WHERE device_id = ?");
            $ports_stmt->execute([$device_id]);
            $ports_data = $ports_stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($ports_data as $port) {
                $service = $port['service_name'];
                if (!isset($port_counts[$service])) {
                    $port_counts[$service] = 1;
                } else {
                    $port_counts[$service]++;
                }
            }
        }
        arsort($port_counts); // Sort by count
        $port_counts = array_slice($port_counts, 0, 5); // Top 5
        
        // 5. Datos para vulnerabilidades por severidad
        $severity_counts = ['High' => 0, 'Medium' => 0, 'Low' => 0, 'Info' => 0];
        foreach ($all_vulnerabilities as $vuln) {
            $severity = $vuln['severity'];
            if (isset($severity_counts[$severity])) {
                $severity_counts[$severity]++;
            } else {
                $severity_counts['Info']++;
            }
        }
        
        // 6. Datos para tipos de vulnerabilidades
        $vuln_types = [];
        foreach ($all_vulnerabilities as $vuln) {
            $type = $vuln['vuln_type'];
            if (!isset($vuln_types[$type])) {
                $vuln_types[$type] = 1;
            } else {
                $vuln_types[$type]++;
            }
        }
        arsort($vuln_types); // Sort by count
        $vuln_types = array_slice($vuln_types, 0, 5); // Top 5
        ?>
        
        // Crear gráficas
        document.addEventListener('DOMContentLoaded', function() {
            // Función de ayuda para crear gráficas solo si el elemento existe
            function createChartIfExists(elementId, chartConfig) {
                const element = document.getElementById(elementId);
                if (element) {
                    new Chart(element, chartConfig);
                }
            }

            // 1. Gráfica de resumen
            createChartIfExists('summaryChart', {
                type: 'bar',
                data: {
                    labels: ['Dispositivos', 'Vulnerabilidades'],
                    datasets: [{
                        label: 'Cantidad',
                        data: [<?= $total_devices ?>, <?= $total_vulns ?>],
                        backgroundColor: ['rgba(54, 162, 235, 0.6)', 'rgba(255, 99, 132, 0.6)'],
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Resumen del Escaneo'
                        },
                        legend: {
                            display: false
                        }
                    }
                }
            });
            
            // 2. Gráfica de autenticación WiFi
            createChartIfExists('authChart', {
                type: 'pie',
                data: {
                    labels: [<?php echo "'" . implode("', '", array_keys($auth_types)) . "'" ?>],
                    datasets: [{
                        data: [<?= implode(', ', array_values($auth_types)) ?>],
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.6)',
                            'rgba(54, 162, 235, 0.6)',
                            'rgba(255, 206, 86, 0.6)',
                            'rgba(75, 192, 192, 0.6)',
                            'rgba(153, 102, 255, 0.6)'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'right'
                        }
                    }
                }
            });
            
            // 3. Gráfica de intensidad de señal
            createChartIfExists('signalChart', {
                type: 'doughnut',
                data: {
                    labels: [<?php echo "'" . implode("', '", array_keys($signal_levels)) . "'" ?>],
                    datasets: [{
                        data: [<?= implode(', ', array_values($signal_levels)) ?>],
                        backgroundColor: [
                            'rgba(75, 192, 192, 0.6)',
                            'rgba(54, 162, 235, 0.6)',
                            'rgba(255, 206, 86, 0.6)',
                            'rgba(255, 99, 132, 0.6)'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'right'
                        }
                    }
                }
            });
            
            // 4. Gráfica de puertos más comunes
            createChartIfExists('portsChart', {
                type: 'bar',
                data: {
                    labels: [<?php echo "'" . implode("', '", array_keys($port_counts)) . "'" ?>],
                    datasets: [{
                        label: 'Frecuencia',
                        data: [<?= implode(', ', array_values($port_counts)) ?>],
                        backgroundColor: 'rgba(54, 162, 235, 0.6)'
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Servicios más comunes en los dispositivos'
                        },
                        legend: {
                            display: false
                        }
                    }
                }
            });
            
            // 5. Gráfica de vulnerabilidades por severidad
            createChartIfExists('vulnSeverityChart', {
                type: 'pie',
                data: {
                    labels: [<?php echo "'" . implode("', '", array_keys($severity_counts)) . "'" ?>],
                    datasets: [{
                        data: [<?= implode(', ', array_values($severity_counts)) ?>],
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.6)',
                            'rgba(255, 206, 86, 0.6)',
                            'rgba(54, 162, 235, 0.6)',
                            'rgba(75, 192, 192, 0.6)'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'right'
                        }
                    }
                }
            });
            
            // 6. Gráfica de tipos de vulnerabilidades
            createChartIfExists('vulnTypeChart', {
                type: 'pie',
                data: {
                    labels: [<?php echo "'" . implode("', '", array_keys($vuln_types)) . "'" ?>],
                    datasets: [{
                        data: [<?= implode(', ', array_values($vuln_types)) ?>],
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.6)',
                            'rgba(54, 162, 235, 0.6)',
                            'rgba(255, 206, 86, 0.6)',
                            'rgba(75, 192, 192, 0.6)',
                            'rgba(153, 102, 255, 0.6)'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'right'
                        }
                    }
                }
            });
        });
    
    // Opcionalmente, podemos abrir el diálogo de impresión automáticamente
    // window.onload = function() { window.print(); };
</script>
</body>
</html>
