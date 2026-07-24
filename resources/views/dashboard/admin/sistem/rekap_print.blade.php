<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Kehadiran - {{ $selectedUnit === 'MA' ? 'Aliyah' : 'MTS' }} - {{ \Carbon\Carbon::parse($date)->translatedFormat('d F Y') }}</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace, Arial, sans-serif;
            color: #1e293b;
            background-color: #fff;
            margin: 0;
            padding: 40px;
            font-size: 13px;
            line-height: 1.5;
        }
        .header {
            text-align: center;
            border-bottom: 3px double #000;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }
        .header h1 {
            margin: 0 0 5px 0;
            font-size: 20px;
            text-transform: uppercase;
            font-weight: bold;
            letter-spacing: 1px;
        }
        .header p {
            margin: 0;
            font-size: 11px;
            color: #64748b;
        }
        .meta-table {
            width: 100%;
            margin-bottom: 25px;
            font-size: 13px;
        }
        .meta-table td {
            padding: 3px 0;
        }
        .meta-table td.label {
            width: 120px;
            font-weight: bold;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .data-table th, .data-table td {
            border: 1px solid #000;
            padding: 8px 10px;
            text-align: left;
        }
        .data-table th {
            background-color: #f1f5f9;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 11px;
        }
        .data-table td.center {
            text-align: center;
        }
        .data-table td.right {
            text-align: right;
            font-family: monospace;
        }
        .data-table tr.total-row td {
            font-weight: bold;
            background-color: #f8fafc;
        }
        .footer-sig {
            margin-top: 50px;
            width: 100%;
            page-break-inside: avoid;
        }
        .footer-sig td {
            width: 50%;
            text-align: center;
            padding-top: 10px;
        }
        .sig-space {
            height: 70px;
        }
        @media print {
            body {
                padding: 0;
            }
            .no-print {
                display: none;
            }
        }
        .btn-print-container {
            text-align: right;
            margin-bottom: 20px;
        }
        .btn-print {
            background-color: #0f172a;
            color: #fff;
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
            font-family: sans-serif;
            font-size: 12px;
        }
        .btn-print:hover {
            background-color: #1e293b;
        }
    </style>
</head>
<body>
    <div class="btn-print-container no-print">
        <button onclick="window.print()" class="btn-print">Cetak Laporan</button>
    </div>

    <div class="header">
        <h1>YAYASAN PENDIDIKAN ISLAM AL-FALAH (YATIA)</h1>
        <h1>REKAPITULASI KEHADIRAN HARIAN SISWA</h1>
        <p>Jl. Pameuntasan No. 10, Desa Pameuntasan, Kec. Kutawaringin, Kabupaten Bandung</p>
    </div>

    <table class="meta-table">
        <tr>
            <td class="label">Unit Madrasah</td>
            <td>: {{ $selectedUnit === 'MA' ? 'Madrasah Aliyah (MA)' : 'Madrasah Tsanawiyah (MTs)' }}</td>
        </tr>
        <tr>
            <td class="label">Tanggal Rekap</td>
            <td>: {{ \Carbon\Carbon::parse($date)->translatedFormat('l, d F Y') }}</td>
        </tr>
        <tr>
            <td class="label">Waktu Cetak</td>
            <td>: {{ \Carbon\Carbon::now()->translatedFormat('d-m-Y H:i') }}</td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 40px;" class="center">No</th>
                <th>Nama Kelas</th>
                <th style="width: 100px;" class="center">Total Siswa</th>
                <th style="width: 80px;" class="center">Hadir</th>
                <th style="width: 80px;" class="center">Sakit</th>
                <th style="width: 80px;" class="center">Izin</th>
                <th style="width: 80px;" class="center">Alpa</th>
                <th style="width: 110px;" class="right">Kehadiran (%)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($tableData as $idx => $row)
                <tr>
                    <td class="center">{{ $idx + 1 }}</td>
                    <td><strong>{{ $row['nama_kelas'] }}</strong></td>
                    <td class="center">{{ $row['total_siswa'] }}</td>
                    <td class="center">{{ $row['hadir'] }}</td>
                    <td class="center">{{ $row['sakit'] }}</td>
                    <td class="center">{{ $row['izin'] }}</td>
                    <td class="center">{{ $row['alpa'] }}</td>
                    <td class="right font-mono">{{ number_format($row['kehadiran_pct'], 1) }}%</td>
                </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="2" class="center">TOTAL / RATA-RATA</td>
                <td class="center">{{ $totals['total_siswa'] }}</td>
                <td class="center">{{ $totals['hadir'] }}</td>
                <td class="center">{{ $totals['sakit'] }}</td>
                <td class="center">{{ $totals['izin'] }}</td>
                <td class="center">{{ $totals['alpa'] }}</td>
                <td class="right font-mono">{{ number_format($totals['kehadiran_pct'], 1) }}%</td>
            </tr>
        </tbody>
    </table>

    <table class="footer-sig">
        <tr>
            <td>
                <!-- Left signature placeholder if needed -->
            </td>
            <td>
                Bandung, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>
                Kepala Madrasah,<br>
                <div class="sig-space"></div>
                <strong>_________________________</strong><br>
                NIP. ....................
            </td>
        </tr>
    </table>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
