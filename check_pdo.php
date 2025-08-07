<?php
// Script para verificar específicamente la extensión PDO SQLite
echo "<h1>Verificación de PDO SQLite</h1>";

// Verificar si PDO está cargado
echo "<h2>1. Verificar PDO:</h2>";
if (extension_loaded('pdo')) {
    echo "<p style='color:green'>✓ PDO está cargado correctamente</p>";
    echo "<p>Versión PDO: " . phpversion('pdo') . "</p>";
} else {
    echo "<p style='color:red'>✗ ERROR: PDO no está cargado</p>";
    echo "<p>Sin PDO, no es posible conectarse a ninguna base de datos usando PDO</p>";
}

// Verificar si PDO SQLite está cargado
echo "<h2>2. Verificar PDO SQLite:</h2>";
if (extension_loaded('pdo_sqlite')) {
    echo "<p style='color:green'>✓ PDO SQLite está cargado correctamente</p>";
    echo "<p>Versión: " . phpversion('pdo_sqlite') . "</p>";
} else {
    echo "<p style='color:red'>✗ ERROR: PDO SQLite no está cargado</p>";
    echo "<p>Esto explica el error 'could not find driver'</p>";
}

// Verificar drivers PDO disponibles
echo "<h2>3. Drivers PDO disponibles:</h2>";
$drivers = PDO::getAvailableDrivers();
if (empty($drivers)) {
    echo "<p style='color:red'>✗ No hay drivers PDO disponibles</p>";
} else {
    echo "<p>Drivers encontrados: " . implode(', ', $drivers) . "</p>";
    if (in_array('sqlite', $drivers)) {
        echo "<p style='color:green'>✓ El driver SQLite está disponible</p>";
    } else {
        echo "<p style='color:red'>✗ El driver SQLite NO está disponible</p>";
    }
}

// Verificar php.ini cargado
echo "<h2>4. Archivo php.ini cargado:</h2>";
echo "<p>Archivo: " . php_ini_loaded_file() . "</p>";

// Verificar ruta de extensiones configurada
echo "<h2>5. Directorio de extensiones:</h2>";
echo "<p>extension_dir = " . ini_get('extension_dir') . "</p>";

// Intentar conexión de prueba a SQLite
echo "<h2>6. Prueba de conexión SQLite:</h2>";
try {
    $db_file = __DIR__ . '/data/test_db.sqlite';
    $dsn = 'sqlite:' . $db_file;
    
    echo "<p>Intentando conectar a: $db_file</p>";
    
    $pdo = new PDO($dsn);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Crear tabla de prueba
    $pdo->exec("CREATE TABLE IF NOT EXISTS test (id INTEGER PRIMARY KEY, data TEXT)");
    
    // Insertar datos
    $pdo->exec("INSERT INTO test (data) VALUES ('Test: " . date('Y-m-d H:i:s') . "')");
    
    // Leer datos
    $stmt = $pdo->query("SELECT * FROM test ORDER BY id DESC LIMIT 1");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "<p style='color:green'>✓ Conexión SQLite exitosa</p>";
    echo "<p>Último registro: " . $row['data'] . "</p>";
    
} catch (PDOException $e) {
    echo "<p style='color:red'>✗ Error: " . $e->getMessage() . "</p>";
}

// Información de carga de extensiones
echo "<h2>7. Todas las extensiones cargadas:</h2>";
echo "<pre>";
print_r(get_loaded_extensions());
echo "</pre>";

// Soluciones
echo "<h2>Soluciones posibles:</h2>";
echo "<ol>";
echo "<li>Verifica que <code>php_pdo.dll</code> y <code>php_pdo_sqlite.dll</code> existan en la carpeta de extensiones</li>";
echo "<li>Asegúrate de que estén habilitadas en php.ini con la ruta correcta</li>";
echo "<li>Prueba usando nombres completos: <code>extension=php_pdo.dll</code> y <code>extension=php_pdo_sqlite.dll</code></li>";
echo "<li>Verifica que no haya errores de DLL (puede requerir Microsoft Visual C++ Redistributable)</li>";
echo "</ol>";
?>
