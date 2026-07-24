<?php
$file = 'd:/APLIKASI RUNNING/XAMPP/htdocs/APLIKASI/SIMSYATIA/app/Http/Controllers/DashboardController.php';
$content = file_get_contents($file);

$replacements = [
    // storeUser
    "public function storeUser(Request \$request)\n    {\n        \$request->validate([" =>
    "public function storeUser(Request \$request)\n    {\n        \$currentUser = Auth::user();\n        if (!\$currentUser->isSuperAdmin() && !\$currentUser->isAdmin()) {\n            abort(403);\n        }\n        \$request->validate([",

    // updateUser
    "public function updateUser(Request \$request, \$id)\n    {\n        \$user = User::findOrFail(\$id);" =>
    "public function updateUser(Request \$request, \$id)\n    {\n        \$currentUser = Auth::user();\n        if (!\$currentUser->isSuperAdmin() && !\$currentUser->isAdmin()) {\n            abort(403);\n        }\n        \$user = User::findOrFail(\$id);",

    // deleteUser
    "public function deleteUser(\$id)\n    {\n        \$user = User::findOrFail(\$id);" =>
    "public function deleteUser(\$id)\n    {\n        \$currentUser = Auth::user();\n        if (!\$currentUser->isSuperAdmin() && !\$currentUser->isAdmin()) {\n            abort(403);\n        }\n        \$user = User::findOrFail(\$id);",

    // toggleUserStatus
    "public function toggleUserStatus(\$id)\n    {\n        \$user = User::findOrFail(\$id);" =>
    "public function toggleUserStatus(\$id)\n    {\n        \$currentUser = Auth::user();\n        if (!\$currentUser->isSuperAdmin() && !\$currentUser->isAdmin()) {\n            abort(403);\n        }\n        \$user = User::findOrFail(\$id);",

    // storeKelas
    "public function storeKelas(Request \$request)\n    {\n        \$request->validate([" =>
    "public function storeKelas(Request \$request)\n    {\n        \$currentUser = Auth::user();\n        if (!\$currentUser->isAdmin()) {\n            abort(403);\n        }\n        \$request->validate([",

    // updateKelas
    "public function updateKelas(Request \$request, \$id)\n    {\n        \$kelas = Kelas::findOrFail(\$id);" =>
    "public function updateKelas(Request \$request, \$id)\n    {\n        \$currentUser = Auth::user();\n        if (!\$currentUser->isAdmin()) {\n            abort(403);\n        }\n        \$kelas = Kelas::findOrFail(\$id);",

    // deleteKelas
    "public function deleteKelas(\$id)\n    {\n        \$kelas = Kelas::findOrFail(\$id);" =>
    "public function deleteKelas(\$id)\n    {\n        \$currentUser = Auth::user();\n        if (!\$currentUser->isAdmin()) {\n            abort(403);\n        }\n        \$kelas = Kelas::findOrFail(\$id);",

    // storeMapel
    "public function storeMapel(Request \$request)\n    {\n        \$request->validate([" =>
    "public function storeMapel(Request \$request)\n    {\n        \$currentUser = Auth::user();\n        if (!\$currentUser->isAdmin()) {\n            abort(403);\n        }\n        \$request->validate([",

    // updateMapel
    "public function updateMapel(Request \$request, \$id)\n    {\n        \$mapel = MataPelajaran::findOrFail(\$id);" =>
    "public function updateMapel(Request \$request, \$id)\n    {\n        \$currentUser = Auth::user();\n        if (!\$currentUser->isAdmin()) {\n            abort(403);\n        }\n        \$mapel = MataPelajaran::findOrFail(\$id);",

    // deleteMapel
    "public function deleteMapel(\$id)\n    {\n        \$mapel = MataPelajaran::findOrFail(\$id);" =>
    "public function deleteMapel(\$id)\n    {\n        \$currentUser = Auth::user();\n        if (!\$currentUser->isAdmin()) {\n            abort(403);\n        }\n        \$mapel = MataPelajaran::findOrFail(\$id);",

    // storeGuru
    "public function storeGuru(Request \$request)\n    {\n        \$request->validate([" =>
    "public function storeGuru(Request \$request)\n    {\n        \$currentUser = Auth::user();\n        if (!\$currentUser->isAdmin()) {\n            abort(403);\n        }\n        \$request->validate([",

    // updateGuru
    "public function updateGuru(Request \$request, \$id)\n    {\n        \$guru = Guru::findOrFail(\$id);" =>
    "public function updateGuru(Request \$request, \$id)\n    {\n        \$currentUser = Auth::user();\n        if (!\$currentUser->isAdmin()) {\n            abort(403);\n        }\n        \$guru = Guru::findOrFail(\$id);",

    // deleteGuru
    "public function deleteGuru(\$id)\n    {\n        \$guru = Guru::findOrFail(\$id);" =>
    "public function deleteGuru(\$id)\n    {\n        \$currentUser = Auth::user();\n        if (!\$currentUser->isAdmin()) {\n            abort(403);\n        }\n        \$guru = Guru::findOrFail(\$id);",

    // toggleGuruStatus
    "public function toggleGuruStatus(\$id)\n    {\n        \$guru = Guru::findOrFail(\$id);" =>
    "public function toggleGuruStatus(\$id)\n    {\n        \$currentUser = Auth::user();\n        if (!\$currentUser->isAdmin()) {\n            abort(403);\n        }\n        \$guru = Guru::findOrFail(\$id);",
];

foreach ($replacements as $search => $replace) {
    $normalizedSearch = str_replace("\n", "\r\n", $search);
    $normalizedReplace = str_replace("\n", "\r\n", $replace);
    
    if (strpos($content, $normalizedSearch) !== false) {
        $content = str_replace($normalizedSearch, $normalizedReplace, $content);
    } else {
        $content = str_replace($search, $replace, $content);
    }
}

file_put_contents($file, $content);
echo "SUCCESS\n";
