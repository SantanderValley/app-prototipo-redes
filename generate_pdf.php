<?php
// Requerir la biblioteca FPDF para crear PDFs
require('fpdf/fpdf.php');

// Verificar que se proporciona un ID de escaneo
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Es necesario proporcionar un ID de escaneo válido");
}

$scan_id = $_GET['id'];

// Configuración de la base de datos
$db_config = [
    'host' => 'localhost',
    'user' => 'root',
    'password' => '',
    'database' => 'network_security'
];

// Conectar a la base de datos
$conn = new mysqli($db_config['host'], $db_config['user'], $db_config['password'], $db_config['database']);

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Obtener información del escaneo
$sql = "SELECT * FROM scans WHERE scan_id = $scan_id";
$scan_result = $conn->query($sql);

if ($scan_result->num_rows === 0) {
    die("No se encontró el escaneo con ID: $scan_id");
}

$scan = $scan_result->fetch_assoc();

// Obtener información de redes WiFi
$sql = "SELECT * FROM wifi_networks WHERE scan_id = $scan_id";
$networks_result = $conn->query($sql);

// Obtener dispositivos
$sql = "SELECT * FROM devices WHERE scan_id = $scan_id";
$devices_result = $conn->query($sql);

// Obtener vulnerabilidades
$sql = "SELECT * FROM vulnerabilities WHERE scan_id = $scan_id";
$vulnerabilities_result = $conn->query($sql);

// Obtener recomendaciones
$sql = "SELECT * FROM ai_recommendations WHERE scan_id = $scan_id";
$recommendations_result = $conn->query($sql);

// Crear PDF
class PDF extends FPDF
{
    // Cabecera de página
    function Header()
    {
        // Logo
        // $this->Image('logo.png', 10, 8, 33);
        // Arial bold 15
        $this->SetFont('Arial', 'B', 15);
        // Movernos a la derecha
        $this->Cell(80);
        // Título
        $this->Cell(30, 10, 'Reporte de Seguridad de Red WiFi', 0, 0, 'C');
        // Salto de línea
        $this->Ln(20);
    }

    // Pie de página
    function Footer()
    {
        // Posición: a 1,5 cm del final
        $this->SetY(-15);
        // Arial italic 8
        $this->SetFont('Arial', 'I', 8);
        // Número de página
        $this->Cell(0, 10, 'Página ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }

    // Sección con título
    function Section($title)
    {
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 10, $title, 0, 1);
        $this->SetFont('Arial', '', 10);
        $this->Ln(1);
    }
}

// Instanciar PDF
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();

// Información general del escaneo
$pdf->Section("Información General del Escaneo");
$pdf->Cell(50, 6, 'ID de Escaneo:', 0, 0);
$pdf->Cell(0, 6, $scan['scan_id'], 0, 1);
$pdf->Cell(50, 6, 'Fecha:', 0, 0);
$pdf->Cell(0, 6, $scan['scan_date'], 0, 1);
$pdf->Cell(50, 6, 'Duración:', 0, 0);
$pdf->Cell(0, 6, round($scan['scan_duration'], 2) . ' segundos', 0, 1);
$pdf->Cell(50, 6, 'Total de Dispositivos:', 0, 0);
$pdf->Cell(0, 6, $scan['total_devices'], 0, 1);
$pdf->Cell(50, 6, 'Total de Vulnerabilidades:', 0, 0);
$pdf->Cell(0, 6, $scan['total_vulnerabilities'], 0, 1);
$pdf->Ln(5);

// Redes WiFi
$pdf->Section("Redes WiFi Detectadas");
if ($networks_result->num_rows > 0) {
    // Encabezados tabla
    $pdf->SetFillColor(200, 220, 255);
    $pdf->Cell(50, 7, 'SSID', 1, 0, 'C', true);
    $pdf->Cell(40, 7, 'Autenticación', 1, 0, 'C', true);
    $pdf->Cell(40, 7, 'Cifrado', 1, 0, 'C', true);
    $pdf->Cell(30, 7, 'Señal', 1, 0, 'C', true);
    $pdf->Cell(30, 7, 'Clave', 1, 1, 'C', true);
    
    // Datos de redes
    $pdf->SetFillColor(255, 255, 255);
    while ($network = $networks_result->fetch_assoc()) {
        $pdf->Cell(50, 6, $network['ssid'], 1, 0);
        $pdf->Cell(40, 6, $network['authentication'], 1, 0);
        $pdf->Cell(40, 6, $network['encryption'], 1, 0);
        $pdf->Cell(30, 6, $network['signal'], 1, 0);
        $pdf->Cell(30, 6, $network['security_key'], 1, 1);
    }
} else {
    $pdf->Cell(0, 10, 'No se detectaron redes WiFi.', 0, 1);
}
$pdf->Ln(5);

// Dispositivos
$pdf->Section("Dispositivos Detectados");
if ($devices_result->num_rows > 0) {
    while ($device = $devices_result->fetch_assoc()) {
        $pdf->SetFillColor(240, 240, 240);
        $pdf->Cell(0, 7, 'Dispositivo: ' . $device['ip_address'] . ' (' . $device['hostname'] . ')', 1, 1, 'L', true);
        
        // Puertos abiertos
        $device_id = $device['device_id'];
        $ports_sql = "SELECT * FROM open_ports WHERE device_id = $device_id";
        $ports_result = $conn->query($ports_sql);
        
        if ($ports_result->num_rows > 0) {
            $pdf->SetX(20); // Indentación
            $pdf->Cell(40, 6, 'Puerto', 1, 0);
            $pdf->Cell(40, 6, 'Servicio', 1, 1);
            
            while ($port = $ports_result->fetch_assoc()) {
                $pdf->SetX(20); // Indentación
                $pdf->Cell(40, 6, $port['port_number'], 1, 0);
                $pdf->Cell(40, 6, $port['service_name'], 1, 1);
            }
        } else {
            $pdf->SetX(20); // Indentación
            $pdf->Cell(0, 6, 'No se detectaron puertos abiertos', 0, 1);
        }
        $pdf->Ln(3);
    }
} else {
    $pdf->Cell(0, 10, 'No se detectaron dispositivos.', 0, 1);
}
$pdf->Ln(5);

// Vulnerabilidades
$pdf->AddPage();
$pdf->Section("Vulnerabilidades Detectadas");
if ($vulnerabilities_result->num_rows > 0) {
    while ($vuln = $vulnerabilities_result->fetch_assoc()) {
        // Determinar color según severidad
        switch (strtolower($vuln['severity'])) {
            case 'high':
                $pdf->SetFillColor(255, 200, 200);
                break;
            case 'medium':
                $pdf->SetFillColor(255, 235, 180);
                break;
            case 'low':
                $pdf->SetFillColor(200, 235, 255);
                break;
            default:
                $pdf->SetFillColor(240, 240, 240);
        }
        
        $pdf->Cell(0, 7, $vuln['vuln_type'] . ' - ' . $vuln['severity'], 1, 1, 'L', true);
        $pdf->SetX(20); // Indentación
        $pdf->MultiCell(0, 6, 'Descripción: ' . $vuln['description'], 0, 'L');
        $pdf->SetX(20); // Indentación
        $pdf->Cell(0, 6, 'Dispositivo afectado: ' . $vuln['affected_device'], 0, 1);
        
        if (!empty($vuln['details'])) {
            $pdf->SetX(20); // Indentación
            $pdf->MultiCell(0, 6, 'Detalles: ' . $vuln['details'], 0, 'L');
        }
        
        $pdf->Ln(5);
    }
} else {
    $pdf->Cell(0, 10, 'No se detectaron vulnerabilidades.', 0, 1);
}
$pdf->Ln(5);

// Recomendaciones
$pdf->Section("Recomendaciones de Seguridad");
if ($recommendations_result->num_rows > 0) {
    while ($rec = $recommendations_result->fetch_assoc()) {
        $pdf->SetFillColor(220, 240, 220);
        $pdf->MultiCell(0, 6, $rec['recommendation'], 1, 'L', true);
        $pdf->Ln(3);
    }
} else {
    $pdf->Cell(0, 10, 'No hay recomendaciones disponibles.', 0, 1);
}

// Fecha y hora de generación
$pdf->Ln(10);
$pdf->SetFont('Arial', 'I', 8);
$pdf->Cell(0, 6, 'Reporte generado el ' . date('Y-m-d H:i:s'), 0, 1, 'R');

// Nombre del archivo
$filename = "security_report_from_db_" . date('Ymd_His') . ".pdf";

// Salida del PDF
$pdf->Output('D', $filename);
?>
