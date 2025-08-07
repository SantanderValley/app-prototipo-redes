<?php
// Comprobar arquitectura de PHP
echo "Arquitectura de PHP: " . (PHP_INT_SIZE == 8 ? "64 bits" : "32 bits") . "\n";
echo "Ruta de PHP: " . PHP_BINARY . "\n";
echo "Versión de PHP: " . PHP_VERSION . "\n";
?>
