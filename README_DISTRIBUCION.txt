INSTRUCCIONES PARA DISTRIBUIR WIFI SCANNER PORTABLE
====================================================

Para que la aplicación WiFi Scanner funcione correctamente en cualquier PC, incluso sin Visual C++ Redistributable instalado, sigue estas instrucciones:

1. BIBLIOTECAS NECESARIAS
-------------------------
Para que la aplicación funcione en PCs que no tienen Visual C++ Redistributable instalado, debes incluir estos archivos en la carpeta 'php_portable\dll':

* VCRUNTIME140.dll
* MSVCP140.dll
* ucrtbase.dll (opcional, solo si aparecen errores adicionales)

Puedes obtener estos archivos de:
- Un PC con Visual C++ Redistributable ya instalado (C:\Windows\System32)
- Descargar el paquete redistributable de Microsoft, instalarlo y copiar los archivos
- Sitios web que ofrecen DLLs individuales (asegúrate de que sean fuentes confiables)

2. ESTRUCTURA DE DIRECTORIOS
---------------------------
Asegúrate de mantener esta estructura de directorios al distribuir:

myapp/
├── data/               # Carpeta para los datos
├── php_portable/       # PHP portable
│   ├── dll/            # AQUÍ VAN LAS DLLs (VCRUNTIME140.dll, etc.)
│   ├── ext/            # Extensiones PHP
│   └── ...             # Resto de archivos PHP
├── iniciar_universal.bat  # Script de inicio
└── wifi_scanner.py     # Script principal

3. SOLUCIÓN DE PROBLEMAS
-----------------------
Si los usuarios siguen teniendo problemas con DLLs faltantes:

a) Pueden ejecutar el archivo iniciar_universal.bat que intentará localizar y copiar las DLLs necesarias.
b) Pueden instalar "Microsoft Visual C++ Redistributable for Visual Studio 2015-2022" desde el sitio web de Microsoft.
c) Pueden copiar manualmente las DLLs faltantes en la carpeta php_portable\dll.

NOTA IMPORTANTE: El script iniciar_universal.bat está diseñado para buscar automáticamente las DLLs en varias ubicaciones y configurar el entorno correctamente, pero es más seguro incluir las DLLs directamente en tu paquete de distribución.
