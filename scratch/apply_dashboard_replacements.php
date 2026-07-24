<?php
$file = 'd:/APLIKASI RUNNING/XAMPP/htdocs/APLIKASI/SIMSYATIA/app/Http/Controllers/DashboardController.php';
$content = file_get_contents($file);

$replacements = [
    // 1. kelasIndex
    "if (!\$user->isSuperAdmin()) {\n            abort(403);\n        }\n\n        \$unit = \$request->query('unit', 'MTs');\n        \$query = Kelas::with('waliGuru');" =>
    "if (!\$user->isAdmin()) {\n            abort(403);\n        }\n\n        \$unit = \$request->query('unit', 'MTs');\n        \$query = Kelas::with('waliGuru');",

    // 2. mapelIndex
    "public function mapelIndex(Request \$request)\n    {\n        \$user = Auth::user();\n        if (!\$user->isSuperAdmin()) {\n            abort(403);\n        }" =>
    "public function mapelIndex(Request \$request)\n    {\n        \$user = Auth::user();\n        if (!\$user->isAdmin()) {\n            abort(403);\n        }",

    // 3. adminAbsensiIndex
    "public function adminAbsensiIndex(Request \$request)\n    {\n        \$user = Auth::user();\n        if (!\$user->isSuperAdmin()) {\n            abort(403);\n        }" =>
    "public function adminAbsensiIndex(Request \$request)\n    {\n        \$user = Auth::user();\n        if (!\$user->isAdmin()) {\n            abort(403);\n        }",

    // 4. adminAbsensiAssign
    "public function adminAbsensiAssign(Request \$request)\n    {\n        \$user = Auth::user();\n        if (!\$user->isSuperAdmin()) {\n            abort(403);\n        }" =>
    "public function adminAbsensiAssign(Request \$request)\n    {\n        \$user = Auth::user();\n        if (!\$user->isAdmin()) {\n            abort(403);\n        }",

    // 5. adminAbsensiRevoke
    "public function adminAbsensiRevoke(\$id)\n    {\n        \$user = Auth::user();\n        if (!\$user->isSuperAdmin()) {\n            abort(403);\n        }" =>
    "public function adminAbsensiRevoke(\$id)\n    {\n        \$user = Auth::user();\n        if (!\$user->isAdmin()) {\n            abort(403);\n        }",

    // 6. adminAbsensiToggle
    "public function adminAbsensiToggle(\$id)\n    {\n        \$user = Auth::user();\n        if (!\$user->isSuperAdmin()) {\n            abort(403);\n        }" =>
    "public function adminAbsensiToggle(\$id)\n    {\n        \$user = Auth::user();\n        if (!\$user->isAdmin()) {\n            abort(403);\n        }",

    // 7. adminUsersIndex
    "public function adminUsersIndex()\n    {\n        \$user = Auth::user();\n        if (!\$user->isSuperAdmin()) {\n            abort(403);\n        }" =>
    "public function adminUsersIndex()\n    {\n        \$user = Auth::user();\n        if (!\$user->isSuperAdmin() && !\$user->isAdmin()) {\n            abort(403);\n        }",

    // 8. profilMadrasahIndex
    "public function profilMadrasahIndex()\n    {\n        \$user = Auth::user();\n        if (!\$user->isSuperAdmin()) {\n            abort(403);\n        }" =>
    "public function profilMadrasahIndex()\n    {\n        \$user = Auth::user();\n        if (!\$user->isAdmin()) {\n            abort(403);\n        }",

    // 9. updateProfilMadrasah
    "public function updateProfilMadrasah(Request \$request)\n    {\n        \$user = Auth::user();\n        if (!\$user->isSuperAdmin()) {\n            abort(403);\n        }" =>
    "public function updateProfilMadrasah(Request \$request)\n    {\n        \$user = Auth::user();\n        if (!\$user->isAdmin()) {\n            abort(403);\n        }",

    // 10. academicYearsIndex
    "public function academicYearsIndex()\n    {\n        \$user = Auth::user();\n        if (!\$user->isSuperAdmin()) {\n            abort(403);\n        }" =>
    "public function academicYearsIndex()\n    {\n        \$user = Auth::user();\n        if (!\$user->isAdmin()) {\n            abort(403);\n        }",

    // 11. storeAcademicYear
    "public function storeAcademicYear(Request \$request)\n    {\n        \$user = Auth::user();\n        if (!\$user->isSuperAdmin()) {\n            abort(403);\n        }" =>
    "public function storeAcademicYear(Request \$request)\n    {\n        \$user = Auth::user();\n        if (!\$user->isAdmin()) {\n            abort(403);\n        }",

    // 12. activateAcademicYear
    "public function activateAcademicYear(Request \$request, \$id)\n    {\n        \$user = Auth::user();\n        if (!\$user->isSuperAdmin()) {\n            abort(403);\n        }" =>
    "public function activateAcademicYear(Request \$request, \$id)\n    {\n        \$user = Auth::user();\n        if (!\$user->isAdmin()) {\n            abort(403);\n        }",

    // 13. lockAcademicYear
    "public function lockAcademicYear(Request \$request, \$id)\n    {\n        \$user = Auth::user();\n        if (!\$user->isSuperAdmin()) {\n            abort(403);\n        }" =>
    "public function lockAcademicYear(Request \$request, \$id)\n    {\n        \$user = Auth::user();\n        if (!\$user->isAdmin()) {\n            abort(403);\n        }",

    // 14. unlockAcademicYear
    "public function unlockAcademicYear(Request \$request, \$id)\n    {\n        \$user = Auth::user();\n        if (!\$user->isSuperAdmin()) {\n            abort(403);\n        }" =>
    "public function unlockAcademicYear(Request \$request, \$id)\n    {\n        \$user = Auth::user();\n        if (!\$user->isAdmin()) {\n            abort(403);\n        }",

    // 15. copyAcademicYearConfig
    "public function copyAcademicYearConfig(Request \$request, \$id)\n    {\n        \$user = Auth::user();\n        if (!\$user->isSuperAdmin()) {\n            abort(403);\n        }" =>
    "public function copyAcademicYearConfig(Request \$request, \$id)\n    {\n        \$user = Auth::user();\n        if (!\$user->isAdmin()) {\n            abort(403);\n        }",

    // 16. holidaysIndex
    "public function holidaysIndex(Request \$request)\n    {\n        \$user = Auth::user();\n        if (!\$user->isSuperAdmin()) {\n            abort(403);\n        }" =>
    "public function holidaysIndex(Request \$request)\n    {\n        \$user = Auth::user();\n        if (!\$user->isAdmin()) {\n            abort(403);\n        }",

    // 17. storeHoliday
    "public function storeHoliday(Request \$request)\n    {\n        \$user = Auth::user();\n        if (!\$user->isSuperAdmin()) {\n            abort(403);\n        }" =>
    "public function storeHoliday(Request \$request)\n    {\n        \$user = Auth::user();\n        if (!\$user->isAdmin()) {\n            abort(403);\n        }",

    // 18. deleteHoliday
    "public function deleteHoliday(\$id)\n    {\n        \$user = Auth::user();\n        if (!\$user->isSuperAdmin()) {\n            abort(403);\n        }" =>
    "public function deleteHoliday(\$id)\n    {\n        \$user = Auth::user();\n        if (!\$user->isAdmin()) {\n            abort(403);\n        }",

    // 19. siswaIndex
    "public function siswaIndex(Request \$request)\n    {\n        \$user = Auth::user();\n        if (!\$user->isSuperAdmin()) {\n            abort(403);\n        }" =>
    "public function siswaIndex(Request \$request)\n    {\n        \$user = Auth::user();\n        if (!\$user->isAdmin()) {\n            abort(403);\n        }",

    // 20. storeSiswa
    "public function storeSiswa(Request \$request)\n    {\n        \$user = Auth::user();\n        if (!\$user->isSuperAdmin()) {\n            abort(403);\n        }" =>
    "public function storeSiswa(Request \$request)\n    {\n        \$user = Auth::user();\n        if (!\$user->isAdmin()) {\n            abort(403);\n        }",

    // 21. updateSiswa
    "public function updateSiswa(Request \$request, \$id)\n    {\n        \$user = Auth::user();\n        if (!\$user->isSuperAdmin()) {\n            abort(403);\n        }" =>
    "public function updateSiswa(Request \$request, \$id)\n    {\n        \$user = Auth::user();\n        if (!\$user->isAdmin()) {\n            abort(403);\n        }",

    // 22. deleteSiswa
    "public function deleteSiswa(\$id)\n    {\n        \$user = Auth::user();\n        if (!\$user->isSuperAdmin()) {\n            abort(403);\n        }" =>
    "public function deleteSiswa(\$id)\n    {\n        \$user = Auth::user();\n        if (!\$user->isAdmin()) {\n            abort(403);\n        }",

    // 23. siswaHistory
    "public function siswaHistory(\$id)\n    {\n        \$user = Auth::user();\n        if (!\$user->isSuperAdmin()) {\n            abort(403);\n        }" =>
    "public function siswaHistory(\$id)\n    {\n        \$user = Auth::user();\n        if (!\$user->isAdmin()) {\n            abort(403);\n        }",

    // 24. pindahKelasIndex
    "public function pindahKelasIndex()\n    {\n        \$user = Auth::user();\n        if (!\$user->isSuperAdmin()) {\n            abort(403);\n        }" =>
    "public function pindahKelasIndex()\n    {\n        \$user = Auth::user();\n        if (!\$user->isAdmin()) {\n            abort(403);\n        }",

    // 25. pindahKelasStore
    "public function pindahKelasStore(Request \$request)\n    {\n        \$user = Auth::user();\n        if (!\$user->isSuperAdmin()) {\n            abort(403);\n        }" =>
    "public function pindahKelasStore(Request \$request)\n    {\n        \$user = Auth::user();\n        if (!\$user->isAdmin()) {\n            abort(403);\n        }",

    // 26. naikKelasIndex
    "public function naikKelasIndex()\n    {\n        \$user = Auth::user();\n        if (!\$user->isSuperAdmin()) {\n            abort(403);\n        }" =>
    "public function naikKelasIndex()\n    {\n        \$user = Auth::user();\n        if (!\$user->isAdmin()) {\n            abort(403);\n        }",

    // 27. naikKelasStore
    "public function naikKelasStore(Request \$request)\n    {\n        \$user = Auth::user();\n        if (!\$user->isSuperAdmin()) {\n            abort(403);\n        }" =>
    "public function naikKelasStore(Request \$request)\n    {\n        \$user = Auth::user();\n        if (!\$user->isAdmin()) {\n            abort(403);\n        }",

    // 28. alumniIndex
    "public function alumniIndex(Request \$request)\n    {\n        \$user = Auth::user();\n        if (!\$user->isSuperAdmin()) {\n            abort(403);\n        }" =>
    "public function alumniIndex(Request \$request)\n    {\n        \$user = Auth::user();\n        if (!\$user->isAdmin()) {\n            abort(403);\n        }",

    // 29. updateAlumniContact
    "public function updateAlumniContact(Request \$request, \$id)\n    {\n        \$user = Auth::user();\n        if (!\$user->isSuperAdmin()) {\n            abort(403);\n        }" =>
    "public function updateAlumniContact(Request \$request, \$id)\n    {\n        \$user = Auth::user();\n        if (!\$user->isAdmin()) {\n            abort(403);\n        }",

    // 30. adminGuruIndex
    "public function adminGuruIndex()\n    {\n        \$user = Auth::user();\n        if (!\$user->isSuperAdmin()) {\n            abort(403);\n        }" =>
    "public function adminGuruIndex()\n    {\n        \$user = Auth::user();\n        if (!\$user->isAdmin()) {\n            abort(403);\n        }",
];

foreach ($replacements as $search => $replace) {
    // Normalize newlines to CRLF or LF depending on the file
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
