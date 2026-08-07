<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center bg-white dark:bg-slate-900 px-6 py-4 rounded-xl shadow-sm dark:shadow-lg border border-slate-200 dark:border-slate-800 transition-colors duration-300">
            <h2 class="font-bold text-xl text-slate-900 dark:text-white tracking-wide">
                {{ __('Publikasi: Berita & Pengumuman') }}
            </h2>
            <button onclick="document.getElementById('add-berita-modal').style.display='flex'" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow transition-colors flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Berita
            </button>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Toast Notification -->
            @if(session('success'))
                <div class="p-4 bg-emerald-50 dark:bg-emerald-950 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 rounded-xl flex items-center gap-3">
                    <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="text-sm font-semibold">{{ session('success') }}</span>
                </div>
            @endif

            <!-- Table Card -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-slate-200 dark:border-slate-800 text-slate-400 dark:text-slate-500 text-xs font-bold uppercase tracking-wider">
                                <th class="py-3 px-4">Gambar</th>
                                <th class="py-3 px-4">Judul Berita</th>
                                <th class="py-3 px-4">Kategori</th>
                                <th class="py-3 px-4">Tanggal Publikasi</th>
                                <th class="py-3 px-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-105 dark:divide-slate-850 text-sm text-slate-800 dark:text-slate-200">
                            @forelse($articles as $article)
                                <tr>
                                    <td class="py-3 px-4">
                                        @if($article->image_path)
                                            <img src="{{ $article->image_path }}" class="w-16 h-10 object-cover rounded-lg border border-slate-200 dark:border-slate-800">
                                        @else
                                            <div class="w-16 h-10 bg-slate-100 dark:bg-slate-850 rounded-lg flex items-center justify-center text-[10px] text-slate-400">No Image</div>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 font-bold">{{ $article->title }}</td>
                                    <td class="py-3 px-4">
                                        @php
                                            $badgeColor = 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300';
                                            if ($article->category === 'Akademik') $badgeColor = 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400';
                                            if ($article->category === 'Prestasi') $badgeColor = 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400';
                                            if ($article->category === 'Agenda') $badgeColor = 'bg-purple-100 text-purple-805 dark:bg-purple-900/30 dark:text-purple-400';
                                        @endphp
                                        <span class="px-2.5 py-1 rounded-lg text-xs font-semibold {{ $badgeColor }}">
                                            {{ $article->category }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-xs">{{ $article->published_at->format('d F Y H:i') }}</td>
                                    <td class="py-3 px-4 text-right space-x-2">
                                        <button onclick="openEditModal({{ json_encode($article) }})" class="p-1.5 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg text-blue-600 dark:text-blue-400 transition-colors inline-block">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </button>
                                        <form action="{{ route('admin.publikasi.berita.delete', $article->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus berita ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg text-rose-600 dark:text-rose-400 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-8 text-center text-slate-400 dark:text-slate-500">Belum ada berita atau pengumuman.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Modal -->
    <div id="add-berita-modal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white dark:bg-slate-900 rounded-3xl max-w-2xl w-full shadow-2xl overflow-hidden border border-slate-200 dark:border-slate-800">
            <div class="px-6 py-4 border-b border-slate-150 dark:border-slate-800 flex justify-between items-center bg-slate-50 dark:bg-slate-900/50">
                <h3 class="font-bold text-lg text-slate-900 dark:text-white">Tambah Berita & Pengumuman</h3>
                <button onclick="document.getElementById('add-berita-modal').style.display='none'" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form action="{{ route('admin.publikasi.berita.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                @csrf
                <div class="space-y-1">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Judul Berita</label>
                    <input type="text" name="title" required placeholder="Masukkan judul berita" class="w-full bg-slate-50 border border-slate-250 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-500">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Kategori</label>
                        <select name="category" required class="w-full bg-slate-50 border border-slate-250 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-500">
                            <option value="Umum">Umum</option>
                            <option value="Akademik">Akademik</option>
                            <option value="Prestasi">Prestasi</option>
                            <option value="Agenda">Agenda</option>
                        </select>
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Tanggal Publikasi</label>
                        <input type="datetime-local" name="published_at" required value="{{ date('Y-m-d\TH:i') }}" class="w-full bg-slate-50 border border-slate-250 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-500">
                    </div>
                </div>
                <div class="space-y-1">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Isi Konten</label>
                    <textarea name="content" rows="6" required placeholder="Tuliskan berita lengkap..." class="w-full bg-slate-50 border border-slate-250 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-500 resize-none"></textarea>
                </div>
                <div class="space-y-1">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Link Instagram / YouTube / Embed Video</label>
                    <input type="url" name="embed_link" placeholder="https://instagram.com/p/... atau https://youtube.com/watch?v=..." class="w-full bg-slate-50 border border-slate-250 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-500">
                </div>
                <div class="space-y-1">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Gambar Sampul</label>
                    <input type="file" name="image" class="w-full text-xs text-slate-500">
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" onclick="document.getElementById('add-berita-modal').style.display='none'" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs transition-colors">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs transition-colors">
                        Simpan Berita
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="edit-berita-modal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white dark:bg-slate-900 rounded-3xl max-w-2xl w-full shadow-2xl overflow-hidden border border-slate-200 dark:border-slate-800">
            <div class="px-6 py-4 border-b border-slate-150 dark:border-slate-800 flex justify-between items-center bg-slate-50 dark:bg-slate-900/50">
                <h3 class="font-bold text-lg text-slate-900 dark:text-white">Edit Berita & Pengumuman</h3>
                <button onclick="document.getElementById('edit-berita-modal').style.display='none'" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form id="edit-form" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                @csrf
                @method('PATCH')
                <div class="space-y-1">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Judul Berita</label>
                    <input type="text" name="title" id="edit-title" required class="w-full bg-slate-50 border border-slate-250 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-500">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Kategori</label>
                        <select name="category" id="edit-category" required class="w-full bg-slate-50 border border-slate-250 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-500">
                            <option value="Umum">Umum</option>
                            <option value="Akademik">Akademik</option>
                            <option value="Prestasi">Prestasi</option>
                            <option value="Agenda">Agenda</option>
                        </select>
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Tanggal Publikasi</label>
                        <input type="datetime-local" name="published_at" id="edit-published_at" required class="w-full bg-slate-50 border border-slate-250 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-500">
                    </div>
                </div>
                <div class="space-y-1">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Isi Konten</label>
                    <textarea name="content" id="edit-content" rows="6" required class="w-full bg-slate-50 border border-slate-250 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-500 resize-none"></textarea>
                </div>
                <div class="space-y-1">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Link Instagram / YouTube / Embed Video</label>
                    <input type="url" name="embed_link" id="edit-embed_link" placeholder="https://instagram.com/p/... atau https://youtube.com/watch?v=..." class="w-full bg-slate-50 border border-slate-250 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-500">
                </div>
                <div class="space-y-1">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Gambar Sampul (Biarkan kosong jika tidak ingin mengubah)</label>
                    <input type="file" name="image" class="w-full text-xs text-slate-500">
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" onclick="document.getElementById('edit-berita-modal').style.display='none'" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs transition-colors">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs transition-colors">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openEditModal(article) {
            document.getElementById('edit-form').action = `/admin/publikasi/berita/${article.id}`;
            document.getElementById('edit-title').value = article.title;
            document.getElementById('edit-category').value = article.category;
            document.getElementById('edit-content').value = article.content;
            document.getElementById('edit-embed_link').value = article.embed_link || '';
            
            // Format published_at for datetime-local input
            let pubDate = new Date(article.published_at);
            // offset timezone
            let tzoffset = pubDate.getTimezoneOffset() * 60000; 
            let localISOTime = (new Date(pubDate - tzoffset)).toISOString().slice(0, 16);
            document.getElementById('edit-published_at').value = localISOTime;

            document.getElementById('edit-berita-modal').style.display = 'flex';
        }
    </script>
</x-app-layout>
