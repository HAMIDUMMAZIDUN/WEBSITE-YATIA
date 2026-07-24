<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class SystemController extends Controller
{
    private function checkSuperAdmin()
    {
        $user = Auth::user();
        if (!$user || !$user->isAdmin()) {
            abort(403);
        }
    }

    // ==========================================
    // LOG AKTIVITAS
    // ==========================================
    public function logsIndex(Request $request)
    {
        $this->checkSuperAdmin();
        
        $query = AuditLog::with('user');
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('action', 'like', "%{$search}%")
                  ->orWhere('entity', 'like', "%{$search}%")
                  ->orWhereHas('user', function($qu) use ($search) {
                      $qu->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $logs = $query->latest()->paginate(25)->withQueryString();
        
        return view('dashboard.admin.sistem.logs', compact('logs'));
    }

    // ==========================================
    // LAPORAN AUDIT
    // ==========================================
    public function auditIndex(Request $request)
    {
        $this->checkSuperAdmin();

        $query = AuditLog::with('user');

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by action type
        if ($request->filled('action_type')) {
            $query->where('action', 'like', "%{$request->action_type}%");
        }

        // Filter by entity/table
        if ($request->filled('entity')) {
            $query->where('entity', $request->entity);
        }

        // Filter by date range
        if ($request->filled('date_start')) {
            $query->whereDate('created_at', '>=', $request->date_start);
        }
        if ($request->filled('date_end')) {
            $query->whereDate('created_at', '<=', $request->date_end);
        }

        $logs = $query->latest()->paginate(50)->withQueryString();

        // Get filter options
        $users = User::whereIn('role_id', function($q) {
            $q->select('id')->from('roles')->whereIn('name', ['super_admin', 'admin']);
        })->orWhereIn('id', function($q) {
            $q->select('user_id')->from('audit_logs');
        })->get();
        
        $entities = AuditLog::select('entity')->distinct()->whereNotNull('entity')->pluck('entity');
        
        return view('dashboard.admin.sistem.audit', compact('logs', 'users', 'entities'));
    }

    public function clearAuditLogs(Request $request)
    {
        $this->checkSuperAdmin();

        $request->validate([
            'clear_option' => 'required|in:all,older_30',
        ]);

        $admin = Auth::user();

        if ($request->clear_option === 'all') {
            // Write one final log indicating clear before deletion
            AuditLog::truncate();
            AuditLog::create([
                'user_id' => $admin->id,
                'action' => 'clear_all_audit_logs',
                'entity' => 'audit_logs',
                'new_value' => 'Seluruh log aktivitas dan audit telah dibersihkan oleh Super Admin.',
            ]);
            $msg = 'Semua log audit telah berhasil dibersihkan.';
        } else {
            $cutoff = Carbon::now()->subDays(30);
            AuditLog::where('created_at', '<', $cutoff)->delete();
            
            AuditLog::create([
                'user_id' => $admin->id,
                'action' => 'clear_old_audit_logs',
                'entity' => 'audit_logs',
                'new_value' => 'Log audit yang berusia lebih dari 30 hari telah dibersihkan.',
            ]);
            $msg = 'Log audit yang berusia lebih dari 30 hari telah berhasil dibersihkan.';
        }

        return redirect()->route('admin.sistem.audit')->with('success', $msg);
    }

    public function exportAuditLogs(Request $request, $format)
    {
        $this->checkSuperAdmin();

        $query = AuditLog::with('user');

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('action_type')) {
            $query->where('action', 'like', "%{$request->action_type}%");
        }
        if ($request->filled('entity')) {
            $query->where('entity', $request->entity);
        }
        if ($request->filled('date_start')) {
            $query->whereDate('created_at', '>=', $request->date_start);
        }
        if ($request->filled('date_end')) {
            $query->whereDate('created_at', '<=', $request->date_end);
        }

        $logs = $query->latest()->get();

        if ($format === 'json') {
            $jsonData = $logs->toJson(JSON_PRETTY_PRINT);
            $filename = 'audit_report_' . date('Ymd_His') . '.json';
            return response($jsonData, 200, [
                'Content-Type' => 'application/json',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]);
        }

        if ($format === 'csv') {
            $filename = 'audit_report_' . date('Ymd_His') . '.csv';
            $handle = fopen('php://output', 'w');
            
            $headers = ['ID', 'Pengguna', 'Email', 'Aksi', 'Tabel/Entitas', 'ID Entitas', 'Nilai Lama', 'Nilai Baru', 'Waktu'];
            
            ob_start();
            $df = fopen("php://output", 'w');
            fputcsv($df, $headers);
            
            foreach ($logs as $log) {
                fputcsv($df, [
                    $log->id,
                    $log->user ? $log->user->name : 'Sistem/Guest',
                    $log->user ? $log->user->email : '-',
                    $log->action,
                    $log->entity ?: '-',
                    $log->entity_id ?: '-',
                    $log->old_value ?: '-',
                    $log->new_value ?: '-',
                    $log->created_at->toDateTimeString(),
                ]);
            }
            fclose($df);
            
            return response(ob_get_clean(), 200, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Pragma' => 'no-cache',
                'Expires' => '0',
            ]);
        }

        return redirect()->back()->with('error', 'Format ekspor tidak didukung.');
    }

    // ==========================================
    // BACKUP & PEMULIHAN
    // ==========================================
    public function backupIndex()
    {
        $this->checkSuperAdmin();

        $backupDir = storage_path('app/backups');
        if (!file_exists($backupDir)) {
            mkdir($backupDir, 0755, true);
        }

        $files = [];
        $rawFiles = scandir($backupDir);
        foreach ($rawFiles as $file) {
            if ($file !== '.' && $file !== '..') {
                $path = $backupDir . '/' . $file;
                $files[] = [
                    'filename' => $file,
                    'size' => round(filesize($path) / 1024, 2), // KB
                    'created_at' => date('Y-m-d H:i:s', filemtime($path)),
                ];
            }
        }

        // Sort files by date descending
        usort($files, function($a, $b) {
            return strcmp($b['created_at'], $a['created_at']);
        });

        $dbConnection = config('database.default');
        $dbName = config("database.connections.{$dbConnection}.database");

        return view('dashboard.admin.sistem.backup', compact('files', 'dbConnection', 'dbName'));
    }

    public function createBackup()
    {
        $this->checkSuperAdmin();

        $backupDir = storage_path('app/backups');
        if (!file_exists($backupDir)) {
            mkdir($backupDir, 0755, true);
        }

        $dbConnection = config('database.default');
        $timestamp = date('Ymd_His');
        
        try {
            if ($dbConnection === 'sqlite') {
                $dbPath = config('database.connections.sqlite.database');
                
                if ($dbPath === ':memory:' || !file_exists($dbPath)) {
                    // In-memory or missing SQLite file backup: Generate SQL dump
                    $filename = 'backup_sqlite_' . $timestamp . '.sql';
                    $destPath = $backupDir . '/' . $filename;
                    
                    $tables = [];
                    $result = DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'");
                    foreach ($result as $row) {
                        $tables[] = $row->name;
                    }
                    
                    $sqlDump = "-- SIMSYATIA SQLite In-Memory Database Backup\n";
                    $sqlDump .= "-- Generated: " . date('Y-m-d H:i:s') . "\n\n";
                    
                    foreach ($tables as $table) {
                        $createRes = DB::select("SELECT sql FROM sqlite_master WHERE type='table' AND name='{$table}'");
                        if (!empty($createRes)) {
                            $sqlDump .= "DROP TABLE IF EXISTS `{$table}`;\n";
                            $sqlDump .= $createRes[0]->sql . ";\n\n";
                        }
                        
                        $rows = DB::table($table)->get();
                        foreach ($rows as $row) {
                            $rowArray = (array)$row;
                            $columns = array_map(function($col) { return "`{$col}`"; }, array_keys($rowArray));
                            $values = array_map(function($val) {
                                if (is_null($val)) return 'NULL';
                                return DB::getPdo()->quote($val);
                            }, array_values($rowArray));
                            
                            $sqlDump .= "INSERT INTO `{$table}` (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $values) . ");\n";
                        }
                        $sqlDump .= "\n\n";
                    }
                    
                    file_put_contents($destPath, $sqlDump);
                } else {
                    // File-based SQLite backup: Copy database file
                    $filename = 'backup_sqlite_' . $timestamp . '.sqlite';
                    $destPath = $backupDir . '/' . $filename;
                    copy($dbPath, $destPath);
                }
            } else {
                // Generates dynamic MySQL dump using PHP/PDO
                $filename = 'backup_mysql_' . $timestamp . '.sql';
                $destPath = $backupDir . '/' . $filename;
                
                $tables = [];
                $result = DB::select('SHOW TABLES');
                $dbName = config('database.connections.mysql.database');
                $keyName = "Tables_in_" . $dbName;
                
                foreach ($result as $row) {
                    $tables[] = $row->$keyName;
                }
                
                $sqlDump = "-- SIMSYATIA MySQL Database Backup\n";
                $sqlDump .= "-- Generated: " . date('Y-m-d H:i:s') . "\n";
                $sqlDump .= "-- Database: " . $dbName . "\n\n";
                $sqlDump .= "SET FOREIGN_KEY_CHECKS=0;\n\n";
                
                foreach ($tables as $table) {
                    // Create table statement
                    $createRes = DB::select("SHOW CREATE TABLE `{$table}`");
                    $sqlDump .= "DROP TABLE IF EXISTS `{$table}`;\n";
                    $sqlDump .= $createRes[0]->{'Create Table'} . ";\n\n";
                    
                    // Insert statements
                    $rows = DB::table($table)->get();
                    foreach ($rows as $row) {
                        $rowArray = (array)$row;
                        $columns = array_map(function($col) { return "`{$col}`"; }, array_keys($rowArray));
                        $values = array_map(function($val) {
                            if (is_null($val)) return 'NULL';
                            return DB::getPdo()->quote($val);
                        }, array_values($rowArray));
                        
                        $sqlDump .= "INSERT INTO `{$table}` (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $values) . ");\n";
                    }
                    $sqlDump .= "\n\n";
                }
                
                $sqlDump .= "SET FOREIGN_KEY_CHECKS=1;\n";
                
                file_put_contents($destPath, $sqlDump);
            }

            // Log Audit
            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'create_backup',
                'entity' => 'system_backups',
                'new_value' => 'Cadangan database berhasil dibuat: ' . $filename,
            ]);

            return redirect()->route('admin.sistem.backup')->with('success', 'Cadangan database berhasil dibuat: ' . $filename);
        } catch (\Exception $e) {
            return redirect()->route('admin.sistem.backup')->with('error', 'Gagal membuat cadangan: ' . $e->getMessage());
        }
    }

    public function downloadBackup($filename)
    {
        $this->checkSuperAdmin();
        $path = storage_path('app/backups/' . $filename);
        
        if (file_exists($path)) {
            return response()->download($path);
        }
        
        return redirect()->back()->with('error', 'File cadangan tidak ditemukan.');
    }

    public function deleteBackup($filename)
    {
        $this->checkSuperAdmin();
        $path = storage_path('app/backups/' . $filename);
        
        if (file_exists($path)) {
            unlink($path);
            
            // Log Audit
            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'delete_backup',
                'entity' => 'system_backups',
                'old_value' => $filename,
            ]);

            return redirect()->route('admin.sistem.backup')->with('success', 'File cadangan berhasil dihapus.');
        }
        
        return redirect()->back()->with('error', 'File cadangan tidak ditemukan.');
    }

    public function restoreBackup(Request $request)
    {
        $this->checkSuperAdmin();
        
        $request->validate([
            'backup_file' => 'required|file',
        ]);

        $file = $request->file('backup_file');
        $dbConnection = config('database.default');

        try {
            if ($dbConnection === 'sqlite') {
                $ext = $file->getClientOriginalExtension();
                if ($ext !== 'sqlite' && $ext !== 'sql') {
                    return redirect()->back()->with('error', 'File pemulihan harus berformat .sqlite atau .sql.');
                }
                
                if ($ext === 'sql') {
                    $sqlContent = file_get_contents($file->getRealPath());
                    DB::unprepared($sqlContent);
                } else {
                    $dbPath = config('database.connections.sqlite.database');
                    if ($dbPath === ':memory:') {
                        return redirect()->back()->with('error', 'Tidak dapat memulihkan file .sqlite ke database in-memory.');
                    }
                    
                    // Copy current database as temporary backup before restoring
                    copy($dbPath, $dbPath . '.tmp');
                    
                    // Copy the new database
                    if (copy($file->getRealPath(), $dbPath)) {
                        unlink($dbPath . '.tmp');
                    } else {
                        rename($dbPath . '.tmp', $dbPath);
                        throw new \Exception("Gagal menyalin file database SQLite baru.");
                    }
                }
            } else {
                if ($file->getClientOriginalExtension() !== 'sql') {
                    return redirect()->back()->with('error', 'File pemulihan harus berformat .sql untuk koneksi MySQL.');
                }
                
                $sqlContent = file_get_contents($file->getRealPath());
                
                // Execute restoration query
                DB::unprepared($sqlContent);
            }

            // Log Audit
            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'restore_backup',
                'entity' => 'system_backups',
                'new_value' => 'Database berhasil dipulihkan menggunakan file unggahan: ' . $file->getClientOriginalName(),
            ]);

            return redirect()->route('admin.sistem.backup')->with('success', 'Database berhasil dipulihkan.');
        } catch (\Exception $e) {
            return redirect()->route('admin.sistem.backup')->with('error', 'Gagal memulihkan database: ' . $e->getMessage());
        }
    }
}
