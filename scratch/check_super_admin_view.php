<?php
$content = file_get_contents('d:/APLIKASI RUNNING/XAMPP/htdocs/APLIKASI/SIMSYATIA/resources/views/dashboard/admin/super_admin.blade.php');
if (strpos($content, 'profil') !== false) {
    echo "Found 'profil'\n";
}
if (strpos($content, 'admin.profil.index') !== false) {
    echo "Found 'admin.profil.index'\n";
}
