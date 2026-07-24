<?php
function scanDirRecursive($dir, &$results = []) {
    $files = scandir($dir);
    foreach ($files as $key => $value) {
        $path = realpath($dir . DIRECTORY_SEPARATOR . $value);
        if (!is_dir($path)) {
            if (pathinfo($path, PATHINFO_EXTENSION) === 'php') {
                $content = file_get_contents($path);
                if (strpos($content, 'profil-madrasah') !== false) {
                    $results[] = $path;
                }
            }
        } else if ($value != "." && $value != "..") {
            scanDirRecursive($path, $results);
        }
    }
    return $results;
}

$results = scanDirRecursive('d:/APLIKASI RUNNING/XAMPP/htdocs/APLIKASI/SIMSYATIA/resources/views');
print_r($results);
