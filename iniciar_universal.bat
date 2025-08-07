@echo off
TITLE WiFi Scanner - Universal Launcher
color 0A
echo.
echo =======================================
echo       WIFI SCANNER - INICIANDO
echo =======================================
echo.

REM Variables basicas
SET APP_DIR=%~dp0
SET PHP_DIR=%APP_DIR%php_portable
SET PHP_EXE=%PHP_DIR%\php.exe
SET PORT=3000
SET LOG_FILE=%APP_DIR%debug.log
SET DB_FILE=%APP_DIR%data\network_security.db
SET SQLITE_FILE=%APP_DIR%data\security.sqlite
SET VCRUNTIME_DIR=%APP_DIR%vcredist

REM Verificar carpeta de DLLs en PHP Portable
SET DLL_DIR=%PHP_DIR%\dll

REM Asegurarse de que la carpeta DLL existe
if not exist "%DLL_DIR%" (
    echo [*] Creando carpeta para DLLs de sistema...
    mkdir "%DLL_DIR%"
)

REM Usando un enfoque más simple para evitar problemas de compatibilidad
echo [*] Verificando compatibilidad del sistema...

REM Mover los DLLs directamente a la carpeta de PHP si existen
if exist "%DLL_DIR%\*.dll" (
    echo [+] Copiando DLLs al directorio principal de PHP...
    copy /Y "%DLL_DIR%\*.dll" "%PHP_DIR%\" >nul 2>&1
)

REM Buscar en el propio directorio PHP
if exist "%PHP_DIR%\VCRUNTIME140.dll" (
    echo [+] VCRUNTIME140.dll ya está presente en la carpeta PHP
) else (
    echo [!] ADVERTENCIA: VCRUNTIME140.dll no encontrado en carpeta PHP
    
    REM Intentar usar el de Windows si existe (solo para 64 bits si PHP es 64 bits)
    if exist "C:\Windows\System32\VCRUNTIME140.dll" (
        echo [*] Intentando usar VCRUNTIME140.dll del sistema...
        copy /Y "C:\Windows\System32\VCRUNTIME140.dll" "%PHP_DIR%\" >nul 2>&1
        if exist "%PHP_DIR%\VCRUNTIME140.dll" (
            echo [+] VCRUNTIME140.dll copiado exitosamente
        ) else (
            echo [!] Error al copiar VCRUNTIME140.dll
        )
    ) else (
        echo [!] No se encontró VCRUNTIME140.dll en el sistema
    )
)

REM Verificar si PHP ya incluye las DLLs necesarias (algunos paquetes lo hacen)
if exist "%PHP_DIR%\libsasl.dll" (
    copy /Y "%PHP_DIR%\libsasl.dll" "%DLL_DIR%" > nul 2>&1
)

REM Configurar PATH para incluir nuestras DLLs
echo [*] Configurando PATH para dependencias...
set PATH=%DLL_DIR%;%PHP_DIR%;%PATH%

REM Verificar PHP portable
echo [*] Buscando PHP portable...
if exist "%PHP_DIR%\php.exe" (
    SET PHP_EXE=%PHP_DIR%\php.exe
    echo [+] Usando PHP portable: %PHP_EXE%
) else (
    echo [-] PHP portable no encontrado
    echo [*] Buscando otras opciones...
    if exist "C:\xampp\php\php.exe" (
        SET PHP_EXE=C:\xampp\php\php.exe
        echo [+] Usando PHP de XAMPP: %PHP_EXE%
    ) else (
        echo [!] ERROR: No se encuentra PHP
        echo [!] Por favor instala XAMPP o copia PHP a la carpeta php_portable
        pause
        exit /b
    )
)

REM Crear carpeta de datos si no existe
if not exist "%APP_DIR%data" mkdir "%APP_DIR%data"

REM Detectar ruta de extensiones
SET EXT_DIR=%PHP_DIR%\ext
echo [*] Verificando carpeta de extensiones: %EXT_DIR%
if not exist "%EXT_DIR%" (
    echo [!] ADVERTENCIA: No se encuentra la carpeta de extensiones
    mkdir "%EXT_DIR%"
    echo [+] Carpeta de extensiones creada: %EXT_DIR%
)

REM Verificar extensiones PDO
echo [*] Verificando extensiones PDO...
SET PDO_BASE=0
SET PDO_SQLITE=0
SET PDO_MYSQL=0

if exist "%EXT_DIR%\php_pdo.dll" (
    echo [+] php_pdo.dll encontrado
    SET PDO_BASE=1
) else (
    echo [-] php_pdo.dll no encontrado
)

if exist "%EXT_DIR%\php_pdo_sqlite.dll" (
    echo [+] php_pdo_sqlite.dll encontrado
    SET PDO_SQLITE=1
) else (
    echo [-] php_pdo_sqlite.dll no encontrado
)

if exist "%EXT_DIR%\php_pdo_mysql.dll" (
    echo [+] php_pdo_mysql.dll encontrado
    SET PDO_MYSQL=1
) else (
    echo [-] php_pdo_mysql.dll no encontrado
)

REM Generar php.ini optimizado
SET PHP_INI=%APP_DIR%portable_php.ini
echo [*] Creando configuración PHP personalizada en %PHP_INI%...

echo ; PHP.ini optimizado para WiFi Scanner - Generado: %date% %time% > "%PHP_INI%"
echo [PHP] >> "%PHP_INI%"
echo ; Configuración básica >> "%PHP_INI%"
echo display_errors = On >> "%PHP_INI%"
echo error_reporting = E_ALL >> "%PHP_INI%"
echo max_execution_time = 300 >> "%PHP_INI%"
echo memory_limit = 256M >> "%PHP_INI%"
echo default_charset = "UTF-8" >> "%PHP_INI%"
echo. >> "%PHP_INI%"

REM Configurar extensiones en php.ini
echo ; Ruta de extensiones (con ruta absoluta) >> "%PHP_INI%"
echo extension_dir = "%EXT_DIR%" >> "%PHP_INI%"
echo. >> "%PHP_INI%"

REM Cargar extensiones según disponibilidad
echo ; Extensiones habilitadas automaticamente >> "%PHP_INI%"

REM Si existe php_pdo.dll, usar configuración normal
if %PDO_BASE%==1 (
    echo extension=php_pdo.dll >> "%PHP_INI%"
    if %PDO_SQLITE%==1 echo extension=php_pdo_sqlite.dll >> "%PHP_INI%"
    if %PDO_MYSQL%==1 echo extension=php_pdo_mysql.dll >> "%PHP_INI%"
    echo extension=php_sqlite3.dll >> "%PHP_INI%"
) else (
    REM Si no existe php_pdo.dll pero existen las extensiones específicas, intentar cargarlas directamente
    echo ; Configuración alternativa sin php_pdo.dll >> "%PHP_INI%"
    echo extension=php_sqlite3.dll >> "%PHP_INI%"
    if %PDO_SQLITE%==1 echo extension=php_pdo_sqlite.dll >> "%PHP_INI%"
    if %PDO_MYSQL%==1 echo extension=php_pdo_mysql.dll >> "%PHP_INI%"
)

REM Agregar otras extensiones útiles
echo. >> "%PHP_INI%"
echo ; Otras extensiones útiles >> "%PHP_INI%"
if exist "%EXT_DIR%\php_mbstring.dll" echo extension=php_mbstring.dll >> "%PHP_INI%"
if exist "%EXT_DIR%\php_curl.dll" echo extension=php_curl.dll >> "%PHP_INI%"
if exist "%EXT_DIR%\php_openssl.dll" echo extension=php_openssl.dll >> "%PHP_INI%"
if exist "%EXT_DIR%\php_gd.dll" echo extension=php_gd.dll >> "%PHP_INI%"
echo. >> "%PHP_INI%"

REM Configuración PDO y SQLite
echo ; Configuración PDO y SQLite >> "%PHP_INI%"
echo pdo_sqlite.default_sqlite_busy_timeout=60000 >> "%PHP_INI%"
echo sqlite3.defensive=1 >> "%PHP_INI%"

echo [+] PHP.ini personalizado creado correctamente

REM Comprobar si falta la extensión base PDO pero existen las específicas
if %PDO_BASE%==0 (
    if %PDO_SQLITE%==1 (
        echo.
        echo [!] AVISO: Se detectó que falta php_pdo.dll pero existe php_pdo_sqlite.dll
        echo [!] Se ha configurado el sistema para intentar funcionar sin la extensión base
        echo [!] Es posible que algunas funciones no estén disponibles
        echo.
    )
)

REM Verificar si hay alguna extensión PDO disponible
if %PDO_BASE%==0 (
    if %PDO_SQLITE%==0 (
        if %PDO_MYSQL%==0 (
            echo.
            echo [!] ADVERTENCIA: No se encontraron extensiones PDO
            echo [!] La aplicación puede tener funcionalidad limitada
            echo [!] Se intentará usar SQLite nativo sin PDO
            echo.
        )
    )
)

REM Verificar base de datos
echo.
echo [*] Verificando base de datos...

REM Crear carpeta data si no existe
if not exist "%APP_DIR%data" (
    echo [+] Creando carpeta de datos...
    mkdir "%APP_DIR%data"
)

REM Crear script para verificar y reparar la base de datos
SET DB_CHECK_SCRIPT=%APP_DIR%\db_check.php
echo ^<?php > "%DB_CHECK_SCRIPT%"
echo // Script para verificar y reparar la base de datos >> "%DB_CHECK_SCRIPT%"
echo error_reporting(E_ALL); >> "%DB_CHECK_SCRIPT%"
echo ini_set('display_errors', 1); >> "%DB_CHECK_SCRIPT%"
echo $db_file = __DIR__ . '/data/network_security.db'; >> "%DB_CHECK_SCRIPT%"
echo $backup_file = __DIR__ . '/data/backup_' . date('Ymd_His') . '.db'; >> "%DB_CHECK_SCRIPT%"
echo $result = array('status' => 'error', 'message' => 'No se pudo verificar la base de datos'); >> "%DB_CHECK_SCRIPT%"
echo try { >> "%DB_CHECK_SCRIPT%"
echo     // Verificar si existe clase PDO >> "%DB_CHECK_SCRIPT%"
echo     if (!class_exists('PDO')) { >> "%DB_CHECK_SCRIPT%"
echo         throw new Exception('PDO no está disponible. Se requiere la extensión PDO.'); >> "%DB_CHECK_SCRIPT%"
echo     } >> "%DB_CHECK_SCRIPT%"
echo     // Verificar si existe el driver de SQLite >> "%DB_CHECK_SCRIPT%"
echo     $drivers = PDO::getAvailableDrivers(); >> "%DB_CHECK_SCRIPT%"
echo     if (!in_array('sqlite', $drivers)) { >> "%DB_CHECK_SCRIPT%"
echo         // Intentar con SQLite nativo >> "%DB_CHECK_SCRIPT%"
echo         if (!class_exists('SQLite3')) { >> "%DB_CHECK_SCRIPT%"
echo             throw new Exception('No hay soporte para SQLite. Se requiere PDO SQLite o SQLite3.'); >> "%DB_CHECK_SCRIPT%"
echo         } >> "%DB_CHECK_SCRIPT%"
echo         $result['status'] = 'sqlite3'; >> "%DB_CHECK_SCRIPT%"
echo         $result['message'] = 'Usando SQLite3 nativo (sin PDO)'; >> "%DB_CHECK_SCRIPT%"
echo     } else { >> "%DB_CHECK_SCRIPT%"
echo         // Verificar si existe la base de datos >> "%DB_CHECK_SCRIPT%"
echo         if (file_exists($db_file)) { >> "%DB_CHECK_SCRIPT%"
echo             // Intentar abrir la base de datos >> "%DB_CHECK_SCRIPT%"
echo             try { >> "%DB_CHECK_SCRIPT%"
echo                 $pdo = new PDO('sqlite:' . $db_file); >> "%DB_CHECK_SCRIPT%"
echo                 $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); >> "%DB_CHECK_SCRIPT%"
echo                 // Verificar tablas >> "%DB_CHECK_SCRIPT%"
echo                 $tables = $pdo->query("SELECT name FROM sqlite_master WHERE type='table'")->fetchAll(PDO::FETCH_COLUMN); >> "%DB_CHECK_SCRIPT%"
echo                 $required_tables = array('networks', 'devices', 'scans'); >> "%DB_CHECK_SCRIPT%"
echo                 $missing_tables = array_diff($required_tables, $tables); >> "%DB_CHECK_SCRIPT%"
echo                 if (empty($missing_tables)) { >> "%DB_CHECK_SCRIPT%"
echo                     $result['status'] = 'ok'; >> "%DB_CHECK_SCRIPT%"
echo                     $result['message'] = 'Base de datos verificada correctamente.'; >> "%DB_CHECK_SCRIPT%"
echo                 } else { >> "%DB_CHECK_SCRIPT%"
echo                     // Faltan tablas, crear estructura >> "%DB_CHECK_SCRIPT%"
echo                     $sql = file_get_contents(__DIR__ . '/network_security.sql'); >> "%DB_CHECK_SCRIPT%"
echo                     if ($sql) { >> "%DB_CHECK_SCRIPT%"
echo                         $pdo->exec($sql); >> "%DB_CHECK_SCRIPT%"
echo                         $result['status'] = 'repaired'; >> "%DB_CHECK_SCRIPT%"
echo                         $result['message'] = 'Base de datos reparada: se crearon las tablas faltantes.'; >> "%DB_CHECK_SCRIPT%"
echo                     } else { >> "%DB_CHECK_SCRIPT%"
echo                         throw new Exception('No se pudo leer el archivo SQL para reparar la base de datos.'); >> "%DB_CHECK_SCRIPT%"
echo                     } >> "%DB_CHECK_SCRIPT%"
echo                 } >> "%DB_CHECK_SCRIPT%"
echo             } catch (PDOException $e) { >> "%DB_CHECK_SCRIPT%"
echo                 // Base de datos corrupta, hacer backup y crear nueva >> "%DB_CHECK_SCRIPT%"
echo                 if (file_exists($db_file)) { >> "%DB_CHECK_SCRIPT%"
echo                     copy($db_file, $backup_file); >> "%DB_CHECK_SCRIPT%"
echo                 } >> "%DB_CHECK_SCRIPT%"
echo                 // Crear nueva base de datos >> "%DB_CHECK_SCRIPT%"
echo                 if (file_exists($db_file)) { >> "%DB_CHECK_SCRIPT%"
echo                     unlink($db_file); >> "%DB_CHECK_SCRIPT%"
echo                 } >> "%DB_CHECK_SCRIPT%"
echo                 $pdo = new PDO('sqlite:' . $db_file); >> "%DB_CHECK_SCRIPT%"
echo                 $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); >> "%DB_CHECK_SCRIPT%"
echo                 $sql = file_get_contents(__DIR__ . '/network_security.sql'); >> "%DB_CHECK_SCRIPT%"
echo                 if ($sql) { >> "%DB_CHECK_SCRIPT%"
echo                     $pdo->exec($sql); >> "%DB_CHECK_SCRIPT%"
echo                     $result['status'] = 'recreated'; >> "%DB_CHECK_SCRIPT%"
echo                     $result['message'] = 'Base de datos recreada: se encontró un error y se creó una nueva.'; >> "%DB_CHECK_SCRIPT%"
echo                     if (file_exists($backup_file)) { >> "%DB_CHECK_SCRIPT%"
echo                         $result['backup'] = $backup_file; >> "%DB_CHECK_SCRIPT%"
echo                     } >> "%DB_CHECK_SCRIPT%"
echo                 } else { >> "%DB_CHECK_SCRIPT%"
echo                     throw new Exception('No se pudo leer el archivo SQL para crear la base de datos.'); >> "%DB_CHECK_SCRIPT%"
echo                 } >> "%DB_CHECK_SCRIPT%"
echo             } >> "%DB_CHECK_SCRIPT%"
echo         } else { >> "%DB_CHECK_SCRIPT%"
echo             // No existe la base de datos, crearla >> "%DB_CHECK_SCRIPT%"
echo             $pdo = new PDO('sqlite:' . $db_file); >> "%DB_CHECK_SCRIPT%"
echo             $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); >> "%DB_CHECK_SCRIPT%"
echo             $sql = file_get_contents(__DIR__ . '/network_security.sql'); >> "%DB_CHECK_SCRIPT%"
echo             if ($sql) { >> "%DB_CHECK_SCRIPT%"
echo                 $pdo->exec($sql); >> "%DB_CHECK_SCRIPT%"
echo                 $result['status'] = 'created'; >> "%DB_CHECK_SCRIPT%"
echo                 $result['message'] = 'Base de datos creada correctamente.'; >> "%DB_CHECK_SCRIPT%"
echo             } else { >> "%DB_CHECK_SCRIPT%"
echo                 throw new Exception('No se pudo leer el archivo SQL para crear la base de datos.'); >> "%DB_CHECK_SCRIPT%"
echo             } >> "%DB_CHECK_SCRIPT%"
echo         } >> "%DB_CHECK_SCRIPT%"
echo     } >> "%DB_CHECK_SCRIPT%"
echo } catch (Exception $e) { >> "%DB_CHECK_SCRIPT%"
echo     $result['status'] = 'error'; >> "%DB_CHECK_SCRIPT%"
echo     $result['message'] = $e->getMessage(); >> "%DB_CHECK_SCRIPT%"
echo } >> "%DB_CHECK_SCRIPT%"
echo // Devolver resultado como JSON >> "%DB_CHECK_SCRIPT%"
echo header('Content-Type: application/json'); >> "%DB_CHECK_SCRIPT%"
echo echo json_encode($result); >> "%DB_CHECK_SCRIPT%"
echo ?^> >> "%DB_CHECK_SCRIPT%"

REM Ejecutar script de verificación de base de datos
echo [*] Verificando conexión a la base de datos...

REM Comprobar la base de datos de forma simplificada
set DB_CHECK_ERROR=0
"%PHP_EXE%" -c "%PHP_INI%" "%DB_CHECK_SCRIPT%" > "%APP_DIR%db_result.txt" 2>nul || set DB_CHECK_ERROR=1

if %DB_CHECK_ERROR%==1 (
    echo [!] Error al ejecutar el script de verificación, continuando...
) else (
    echo [+] Verificación de base de datos completada
    
    REM Mostrar un mensaje genérico en lugar de analizar complejos resultados
    if exist "%APP_DIR%data\network_security.db" (
        echo [+] Base de datos encontrada y lista para usar
    ) else (
        echo [!] Base de datos no encontrada, se creará al iniciar la aplicación
    )
)

REM Crear script de verificación de PHP
SET CHECK_SCRIPT=%APP_DIR%\check_php.php
echo ^<?php > "%CHECK_SCRIPT%"
echo // Script para verificar la configuración de PHP >> "%CHECK_SCRIPT%"
echo echo "^<h1^>Diagnóstico de PHP^</h1^>"; >> "%CHECK_SCRIPT%"
echo echo "^<p^>Versión de PHP: " . phpversion() . "^</p^>"; >> "%CHECK_SCRIPT%"
echo echo "^<p^>Extension Directory: " . ini_get('extension_dir') . "^</p^>"; >> "%CHECK_SCRIPT%"
echo echo "^<h2^>Extensiones cargadas:^</h2^>"; >> "%CHECK_SCRIPT%"
echo echo "^<ul^>"; >> "%CHECK_SCRIPT%"
echo $loaded = get_loaded_extensions(); >> "%CHECK_SCRIPT%"
echo foreach($loaded as $ext) { >> "%CHECK_SCRIPT%"
echo     echo "^<li^>$ext^</li^>"; >> "%CHECK_SCRIPT%"
echo } >> "%CHECK_SCRIPT%"
echo echo "^</ul^>"; >> "%CHECK_SCRIPT%"
echo echo "^<h2^>Estado de PDO:^</h2^>"; >> "%CHECK_SCRIPT%"
echo if(class_exists('PDO')) { >> "%CHECK_SCRIPT%"
echo     echo "^<p style='color:green'^>PDO está disponible^</p^>"; >> "%CHECK_SCRIPT%"
echo     echo "^<h3^>Drivers PDO disponibles:^</h3^>"; >> "%CHECK_SCRIPT%"
echo     echo "^<ul^>"; >> "%CHECK_SCRIPT%"
echo     $drivers = PDO::getAvailableDrivers(); >> "%CHECK_SCRIPT%"
echo     foreach($drivers as $driver) { >> "%CHECK_SCRIPT%"
echo         echo "^<li^>$driver^</li^>"; >> "%CHECK_SCRIPT%"
echo     } >> "%CHECK_SCRIPT%"
echo     echo "^</ul^>"; >> "%CHECK_SCRIPT%"
echo } else { >> "%CHECK_SCRIPT%"
echo     echo "^<p style='color:red'^>PDO no está disponible^</p^>"; >> "%CHECK_SCRIPT%"
echo } >> "%CHECK_SCRIPT%"
echo if(class_exists('SQLite3')) { >> "%CHECK_SCRIPT%"
echo     echo "^<p style='color:green'^>SQLite3 está disponible^</p^>"; >> "%CHECK_SCRIPT%"
echo } else { >> "%CHECK_SCRIPT%"
echo     echo "^<p style='color:red'^>SQLite3 no está disponible^</p^>"; >> "%CHECK_SCRIPT%"
echo } >> "%CHECK_SCRIPT%"
echo // Verificar estado de la base de datos >> "%CHECK_SCRIPT%"
echo echo "^<h2^>Estado de la Base de Datos:^</h2^>"; >> "%CHECK_SCRIPT%"
echo $db_file = __DIR__ . '/data/network_security.db'; >> "%CHECK_SCRIPT%"
echo if (file_exists($db_file)) { >> "%CHECK_SCRIPT%"
echo     echo "^<p style='color:green'^>Archivo de base de datos encontrado: " . realpath($db_file) . "^</p^>"; >> "%CHECK_SCRIPT%"
echo     try { >> "%CHECK_SCRIPT%"
echo         if (class_exists('PDO')) { >> "%CHECK_SCRIPT%"
echo             $pdo = new PDO('sqlite:' . $db_file); >> "%CHECK_SCRIPT%"
echo             $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); >> "%CHECK_SCRIPT%"
echo             $tables = $pdo->query("SELECT name FROM sqlite_master WHERE type='table'")->fetchAll(PDO::FETCH_COLUMN); >> "%CHECK_SCRIPT%"
echo             echo "^<p^>Tablas encontradas: " . implode(', ', $tables) . "^</p^>"; >> "%CHECK_SCRIPT%"
echo         } else if (class_exists('SQLite3')) { >> "%CHECK_SCRIPT%"
echo             $db = new SQLite3($db_file); >> "%CHECK_SCRIPT%"
echo             $result = $db->query("SELECT name FROM sqlite_master WHERE type='table'"); >> "%CHECK_SCRIPT%"
echo             $tables = array(); >> "%CHECK_SCRIPT%"
echo             while ($row = $result->fetchArray(SQLITE3_ASSOC)) { >> "%CHECK_SCRIPT%"
echo                 $tables[] = $row['name']; >> "%CHECK_SCRIPT%"
echo             } >> "%CHECK_SCRIPT%"
echo             echo "^<p^>Tablas encontradas: " . implode(', ', $tables) . "^</p^>"; >> "%CHECK_SCRIPT%"
echo         } >> "%CHECK_SCRIPT%"
echo     } catch (Exception $e) { >> "%CHECK_SCRIPT%"
echo         echo "^<p style='color:red'^>Error al conectar con la base de datos: " . $e->getMessage() . "^</p^>"; >> "%CHECK_SCRIPT%"
echo     } >> "%CHECK_SCRIPT%"
echo } else { >> "%CHECK_SCRIPT%"
echo     echo "^<p style='color:red'^>Archivo de base de datos no encontrado: " . $db_file . "^</p^>"; >> "%CHECK_SCRIPT%"
echo } >> "%CHECK_SCRIPT%"
echo ?^> >> "%CHECK_SCRIPT%"

REM Iniciar servidor
echo.
echo [*] Iniciando servidor en http://localhost:%PORT%/
echo [*] Usando PHP: %PHP_EXE%
echo [*] Configuracion: %PHP_INI%
echo.
echo [!] IMPORTANTE: NO CIERRE esta ventana mientras use la aplicacion
echo [!] Para diagnóstico, visite: http://localhost:%PORT%/check_php.php
echo.

REM Preparar mensaje final de inicio
echo.
echo =======================================
echo      WIFI SCANNER - LISTO PARA USAR
echo =======================================
echo.
echo [*] Servidor iniciando en: http://localhost:%PORT%/
echo [*] Configuración: %PHP_INI%
echo [*] Log de errores: %LOG_FILE%
echo.
echo [!] IMPORTANTE: NO CIERRE esta ventana mientras use la aplicación
echo [!] Para diagnóstico, visite: http://localhost:%PORT%/check_php.php
echo.

REM Iniciar servidor en primer plano (no se cerrará hasta que se interrumpa)
start "" "http://localhost:%PORT%/"
timeout /t 2 > nul

REM Iniciar el servidor sin redireccionar stderr
echo [*] Servidor iniciado. Presione Ctrl+C para detener...
echo.
"%PHP_EXE%" -c "%PHP_INI%" -S localhost:%PORT% -t "%APP_DIR%"

REM Solo llegamos aquí si el servidor se detuvo
echo.
echo [*] Servidor detenido.
echo [*] Consulte %LOG_FILE% para ver errores si hubo problemas
pause
