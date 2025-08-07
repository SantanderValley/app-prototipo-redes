<?php
$file = 'export_pdf.php';
$content = file_get_contents($file);

// Corregir el problema del <?php duplicado en la sección de recomendaciones
$content = str_replace('<?php 
                // Verificar si hay un error de API de OpenAI
                $openai_error = false;
                if (isset($_GET[\'openai_error\']) && $_GET[\'openai_error\'] == \'quota_exceeded\') {
                    $openai_error = true;
                }
                
                <?php if (count($all_recommendations) > 0): ?>', 
                '<?php 
                // Verificar si hay un error de API de OpenAI
                $openai_error = false;
                if (isset($_GET[\'openai_error\']) && $_GET[\'openai_error\'] == \'quota_exceeded\') {
                    $openai_error = true;
                }
                
                if (count($all_recommendations) > 0): ?>', 
                $content);

// Corregir también cualquier referencia a vulnerabilities_result que no haya sido actualizada
$content = str_replace('$vulnerabilities_result->num_rows', 'count($all_vulnerabilities)', $content);
$content = str_replace('$recommendations_result->num_rows', 'count($all_recommendations)', $content);

file_put_contents('export_pdf_fixed.php', $content);
echo "Archivo corregido guardado como export_pdf_fixed.php\n";
?>
