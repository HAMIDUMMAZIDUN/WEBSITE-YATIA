<?php
function scanDirRecursive($dir, &$results = []) {
    $files = scandir($dir);
    foreach ($files as $key => $value) {
        $path = realpath($dir . DIRECTORY_SEPARATOR . $value);
        if (!is_dir($path)) {
            if (pathinfo($path, PATHINFO_EXTENSION) === 'php') {
                $content = file_get_contents($path);
                preg_match_all('/admin\.[a-zA-Z0-9_\.]+/', $content, $matches);
                if (!empty($matches[0])) {
                    foreach ($matches[0] as $m) {
                        $results[$m] = isset($results[$m]) ? $results[$m] + 1 : 1;
                    }
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
