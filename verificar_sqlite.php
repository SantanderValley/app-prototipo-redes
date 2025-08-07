<?php
// Script para verificar las extensiones de SQLite

echo "<h1>Verificación de SQLite</h1>";

// Verificar si PDO SQLite está habilitado
echo "<h2>PDO SQLite:</h2>";
if (extension_loaded('pdo_sqlite')) {
    echo "<p style='color:green'>✓ La extensión PDO SQLite está habilitada.</p>";
} else {
    echo "<p style='color:red'>✗ ERROR: La extensión PDO SQLite NO está habilitada.</p>";
}

// Verificar si SQLite3 está habilitado
echo "<h2>SQLite3:</h2>";
if (extension_loaded('sqlite3')) {
    echo "<p style='color:green'>✓ La extensión SQLite3 está habilitada.</p>";
} else {
    echo "<p style='color:red'>✗ ERROR: La extensión SQLite3 NO está habilitada.</p>";
}

// Listar todas las extensiones disponibles
echo "<h2>Todas las extensiones habilitadas:</h2>";
echo "<ul>";
$extensions = get_loaded_extensions();
sort($extensions);
foreach ($extensions as $ext) {
    echo "<li>$ext</li>";
}
echo "</ul>";

// Mostrar información de PDO
echo "<h2>Controladores PDO disponibles:</h2>";
if (extension_loaded('pdo')) {
    $drivers = PDO::getAvailableDrivers();
    if (empty($drivers)) {
        echo "<p style='color:red'>No hay controladores PDO disponibles.</p>";
    } else {
        echo "<ul>";
        foreach ($drivers as $driver) {
            echo "<li>$driver</li>";
        }
        echo "</ul>";
    }
} else {
    echo "<p style='color:red'>La extensión PDO no está habilitada.</p>";
}
?>
