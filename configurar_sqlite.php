<?php
// Configurar SQLite automáticamente
$app_dir = __DIR__;
$php_dir = $app_dir . '/php_portable';
$ext_dir = $php_dir . '/ext';

echo "<h1>Configuración Automática de SQLite</h1>";

// Verificar directorios
echo "<h2>Verificando directorios</h2>";
if (!file_exists($app_dir . '/data')) {
    mkdir($app_dir . '/data', 0777, true);
    echo "<p>✓ Directorio de datos creado</p>";
} else {
    echo "<p>✓ Directorio de datos existente</p>";
}

// Crear un php.ini optimizado
echo "<h2>Creando php.ini optimizado</h2>";

$php_ini_content = <<<INI
[PHP]
; Configuración básica
display_errors = On
error_reporting = E_ALL
memory_limit = 256M
max_execution_time = 300

; Ruta de extensiones
extension_dir = "{$ext_dir}"

; Extensiones SQLite
extension=php_pdo.dll
extension=php_pdo_sqlite.dll
extension=php_sqlite3.dll

; Otras extensiones útiles
extension=php_mbstring.dll
extension=php_curl.dll
INI;

$php_ini_file = $app_dir . '/php_wifi.ini';
if (file_put_contents($php_ini_file, $php_ini_content)) {
    echo "<p>✓ Archivo php.ini creado exitosamente: {$php_ini_file}</p>";
} else {
    echo "<p>✗ Error al crear php.ini</p>";
}

// Verificar extensiones disponibles
echo "<h2>Verificando extensiones disponibles</h2>";

if (file_exists($ext_dir . '/php_pdo_sqlite.dll')) {
    echo "<p>✓ Extensión php_pdo_sqlite.dll existe</p>";
} else {
    echo "<p>✗ Extensión php_pdo_sqlite.dll NO encontrada</p>";
}

if (file_exists($ext_dir . '/php_sqlite3.dll')) {
    echo "<p>✓ Extensión php_sqlite3.dll existe</p>";
} else {
    echo "<p>✗ Extensión php_sqlite3.dll NO encontrada</p>";
}

// Crear archivo .bat optimizado
echo "<h2>Creando script de inicio optimizado</h2>";

$bat_content = <<<BAT
@echo off
TITLE WiFi Scanner - SQLite Optimizado
color 0A
echo.
echo =======================================
echo       WIFI SCANNER - INICIANDO
echo =======================================
echo.

SET APP_DIR=%~dp0
SET PHP_EXE=%APP_DIR%php_portable\php.exe
SET PHP_INI=%APP_DIR%php_wifi.ini
SET PORT=3000

echo [*] Iniciando servidor en http://localhost:%PORT%/
echo [*] Usando configuración optimizada para SQLite
echo.
echo [!] IMPORTANTE: NO CIERRE esta ventana mientras use la aplicación
echo.

start "" "http://localhost:%PORT%/"
"%PHP_EXE%" -c "%PHP_INI%" -S localhost:%PORT% -t "%APP_DIR%"
BAT;

$bat_file = $app_dir . '/iniciar_sqlite_optimizado.bat';
if (file_put_contents($bat_file, $bat_content)) {
    echo "<p>✓ Script de inicio creado exitosamente: {$bat_file}</p>";
} else {
    echo "<p>✗ Error al crear script de inicio</p>";
}

echo "<h2>Diagnóstico de SQLite</h2>";

if (extension_loaded('pdo_sqlite')) {
    echo "<p style='color:green'>✓ PDO SQLite está habilitado en esta sesión</p>";
} else {
    echo "<p style='color:red'>✗ PDO SQLite NO está habilitado en esta sesión</p>";
}

echo "<h2>¿Qué hacer ahora?</h2>";
echo "<p>1. Cierre este servidor</p>";
echo "<p>2. Ejecute el nuevo script: <strong>iniciar_sqlite_optimizado.bat</strong></p>";
echo "<p>3. Acceda a su aplicación en: <a href='http://localhost:3000/'>http://localhost:3000/</a></p>";
?>
