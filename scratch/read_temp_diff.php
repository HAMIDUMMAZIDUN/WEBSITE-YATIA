<?php
$content = file_get_contents('d:/APLIKASI RUNNING/XAMPP/htdocs/APLIKASI/SIMSYATIA/temp_diff.txt');
// Convert from UTF-16LE to UTF-8
$contentUtf8 = mb_convert_encoding($content, 'UTF-8', 'UTF-16LE');
echo $contentUtf8;
