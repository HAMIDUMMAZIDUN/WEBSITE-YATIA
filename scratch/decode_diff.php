<?php
$content = file_get_contents('d:/APLIKASI RUNNING/XAMPP/htdocs/APLIKASI/SIMSYATIA/temp_diff.txt');
$contentUtf8 = mb_convert_encoding($content, 'UTF-8', 'UTF-16LE');
file_put_contents('d:/APLIKASI RUNNING/XAMPP/htdocs/APLIKASI/SIMSYATIA/scratch/temp_diff_decoded.txt', $contentUtf8);
echo "DONE\n";
