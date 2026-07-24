<?php
function scanDirRecursive($dir, &$results = []) {
    $files = scandir($dir);
    foreach ($files as $key => $value) {
        $path = realpath($dir . DIRECTORY_ROOT . $value);
        if (!is_dir($path)) {
            if (pathinfo($path, PATHINFO_EXTENSION) === 'php') {
                $content = file_get_contents($path);
                if (strpos($content, 'isSuperAdmin') !== false) {
                    $results[] = $path;
                }
            }
        } else if ($value != "." && $value != "..") {
            scanDirRecursive($path, $results);
        }
    }
    return $results;
}

define('DIRECTORY_ROOT', '/');
$appResults = scanDirRecursive('d:/APLIKASI RUNNING/XAMPP/htdocs/APLIKASI/SIMSYATIA/app');
$viewResults = scanDirRecursive('d:/APLIKASI RUNNING/XAMPP/htdocs/APLIKASI/SIMSYATIA/resources/views');

echo "APP FILES:\n";
print_r($appResults);
echo "\nVIEW FILES:\n";
print_r($viewResults);
