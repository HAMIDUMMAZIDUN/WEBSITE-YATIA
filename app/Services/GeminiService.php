<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    protected string $apiKey;
    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.gemini.key', env('GEMINI_API_KEY', ''));
        $this->baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent';
    }

    /**
     * Generate teaching planning drafts (Modul Ajar, Kisi-kisi, Materi, Soal).
     *
     * @param string $documentType Type of document (e.g., 'modul_ajar', 'kisi_kisi', 'soal')
     * @param array $context Context details (madrasah, mapel, fase, kelas, characteristics, etc.)
     * @return string|null Generated draft text or null on failure
     */
    public function generatePlanningDraft(string $documentType, array $context): ?string
    {
        if (empty($this->apiKey)) {
            Log::error('Gemini API Key is not configured.');
            return '[Error] API Key Gemini belum dikonfigurasi di server. Silakan hubungi Super Admin.';
        }

        $prompt = $this->buildPrompt($documentType, $context);

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post("{$this->baseUrl}?key={$this->apiKey}", [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.7,
                    'maxOutputTokens' => 2048,
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
            }

            Log::error('Gemini API Error: ' . $response->body());
            return '[Error] Gagal berkomunikasi dengan asisten AI. Silakan coba beberapa saat lagi.';
        } catch (\Exception $e) {
            Log::error('Gemini Service Exception: ' . $e->getMessage());
            return '[Error] Terjadi kesalahan koneksi saat menghubungi AI.';
        }
    }

    /**
     * Build dynamic prompt based on document type and school/academic context.
     */
    protected function buildPrompt(string $documentType, array $context): string
    {
        $madrasah = $context['madrasah'] ?? 'SIMS YATIA';
        $mapel = $context['mapel'] ?? 'Mata Pelajaran';
        $fase = $context['fase'] ?? 'Fase D';
        $kelas = $context['kelas'] ?? 'Kelas VII';
        $karakteristik = $context['karakteristik'] ?? 'Siswa dengan gaya belajar beragam (visual/auditori/kinestetik)';
        $topik = $context['topik'] ?? 'Topik Pokok';

        $basePrompt = "Anda adalah Asisten Pembelajaran Madrasah SIMS YATIA, seorang pakar kurikulum Merdeka Kemenag dan psikologi pendidikan Islam. ";
        $basePrompt .= "Gunakan konteks berikut:\n";
        $basePrompt .= "- Madrasah: {$madrasah}\n";
        $basePrompt .= "- Mata Pelajaran: {$mapel}\n";
        $basePrompt .= "- Fase / Kelas: {$fase} / {$kelas}\n";
        $basePrompt .= "- Topik Pembelajaran: {$topik}\n";
        $basePrompt .= "- Karakteristik Siswa: {$karakteristik}\n\n";

        if ($documentType === 'modul_ajar') {
            return $basePrompt . "TUGAS: Buatkan draf rancangan Modul Ajar (Rencana Pelaksanaan Pembelajaran) yang terstruktur.\n" .
                "Modul Ajar harus mengimplementasikan prinsip Pembelajaran Mendalam (Deep Learning) yaitu:\n" .
                "1. Berkesadaran (Mindful): Mengajak siswa fokus, berdoa, dan reflektif.\n" .
                "2. Bermakna (Meaningful): Menghubungkan materi dengan kehidupan nyata dan nilai keagamaan.\n" .
                "3. Menggembirakan (Joyful): Menyertakan aktivitas ice breaking atau diskusi interaktif.\n" .
                "Modul ini juga wajib mengintegrasikan nilai Panca Cinta YATIA (Cinta Allah & Rasul, Cinta Orang Tua & Guru, Cinta Ilmu & Kebenaran, Cinta Sesama, Cinta Tanah Air).\n\n" .
                "Hasilkan draf lengkap dengan struktur:\n" .
                "- Identitas Modul\n" .
                "- Tujuan Pembelajaran (TP)\n" .
                "- Alur Pembelajaran (Pendahuluan, Inti, Penutup)\n" .
                "- Asesmen (Formatif/Sumatif)\n" .
                "- Refleksi Guru & Siswa.\n\n" .
                "Catatan: Hasilkan draf dalam Bahasa Indonesia. Tuliskan catatan bahwa dokumen ini adalah DRAF yang perlu ditinjau dan disahkan oleh guru.";
        }

        if ($documentType === 'kisi_kisi') {
            return $basePrompt . "TUGAS: Buatkan draf Kisi-Kisi Soal Asesmen.\n" .
                "Hasilkan tabel kisi-kisi yang mencakup:\n" .
                "- Tujuan Pembelajaran (TP)\n" .
                "- Indikator Ketercapaian Tujuan Pembelajaran (IKTP)\n" .
                "- Level Kognitif (L1/L2/L3)\n" .
                "- Bentuk Soal (Pilihan Ganda, Uraian)\n" .
                "- Nomor Soal.\n\n" .
                "Pastikan kisi-kisi selaras dengan regulasi KMA 1503/2025 dan prinsip pembelajaran madrasah.";
        }

        if ($documentType === 'soal') {
            $jumlahSoal = $context['jumlah_soal'] ?? 5;
            return $basePrompt . "TUGAS: Buatkan draf soal ujian/asesmen sebanyak {$jumlahSoal} soal pilihan ganda beserta kunci jawaban dan pembahasannya.\n" .
                "Soal harus dikembangkan dengan standar HOTS (Higher Order Thinking Skills) dan diintegrasikan dengan konteks madrasah yang Islami dan relevan dengan topik pembelajaran.";
        }

        return $basePrompt . "TUGAS: Buatkan draf materi ajar ringkas (handout) untuk siswa dengan gaya penulisan yang bermakna dan mudah dipahami.";
    }
}
