<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center bg-white dark:bg-slate-900 px-6 py-4 rounded-xl shadow-sm dark:shadow-lg border border-slate-200 dark:border-slate-800 transition-colors duration-300">
            <h2 class="font-bold text-xl text-slate-900 dark:text-white tracking-wide">
                {{ __('Publikasi: Galeri & Video') }}
            </h2>
            <button onclick="document.getElementById('add-galeri-modal').style.display='flex'" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow transition-colors flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Galeri
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

            <!-- Grid Card -->
            <div class="grid md:grid-cols-4 gap-6">
                @forelse($galleries as $item)
                    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl overflow-hidden shadow-sm dark:shadow-none transition-all flex flex-col justify-between group">
                        <div class="relative aspect-video bg-slate-100 dark:bg-slate-950 flex items-center justify-center overflow-hidden">
                            @if($item->type === 'photo')
                                @if($item->file_path)
                                    <img src="{{ $item->file_path }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                @else
                                    <svg class="w-10 h-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                @endif
                                <span class="absolute top-2 left-2 px-2 py-0.5 rounded bg-emerald-600 text-white text-[10px] font-bold uppercase tracking-wider">Foto</span>
                            @else
                                <div class="flex flex-col items-center gap-1.5">
                                    <svg class="w-12 h-12 text-rose-600" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.163a3.003 3.003 0 00-2.11-2.11C19.518 3.545 12 3.545 12 3.545s-7.518 0-9.388.507a3.003 3.003 0 00-2.11 2.11C0 8.033 0 12 0 12s0 3.967.502 5.837a3.003 3.003 0 002.11 2.11c1.87.507 9.388.507 9.388.507s7.518 0 9.388-.507a3.003 3.003 0 002.11-2.11C24 15.967 24 12 24 12s0-3.967-.502-5.837zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                                    <span class="text-[10px] text-slate-400 font-medium truncate max-w-full px-4">{{ $item->external_url }}</span>
                                </div>
                                <span class="absolute top-2 left-2 px-2 py-0.5 rounded bg-rose-600 text-white text-[10px] font-bold uppercase tracking-wider">Video</span>
                            @endif
                        </div>
                        <div class="p-4 space-y-3">
                            <h4 class="font-bold text-slate-905 dark:text-white truncate" title="{{ $item->title }}">{{ $item->title }}</h4>
                            <div class="flex justify-end gap-2 border-t border-slate-100 dark:border-slate-800 pt-3">
                                <button onclick="openEditModal({{ json_encode($item) }})" class="px-2.5 py-1.5 border border-slate-205 dark:border-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 rounded-lg text-xs font-semibold transition-colors flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    Edit
                                </button>
                                <form action="{{ route('admin.publikasi.galeri.delete', $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus item galeri ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-2.5 py-1.5 bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 hover:bg-rose-100 dark:hover:bg-rose-900/30 rounded-lg text-xs font-semibold transition-colors flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-4 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl py-12 text-center text-slate-400 dark:text-slate-500">
                        Belum ada item galeri. Klik "Tambah Galeri" untuk memulainya.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Add Modal -->
    <div id="add-galeri-modal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white dark:bg-slate-900 rounded-3xl max-w-md w-full shadow-2xl overflow-hidden border border-slate-200 dark:border-slate-800">
            <div class="px-6 py-4 border-b border-slate-150 dark:border-slate-800 flex justify-between items-center bg-slate-50 dark:bg-slate-900/50">
                <h3 class="font-bold text-lg text-slate-900 dark:text-white">Tambah Galeri & Video</h3>
                <button onclick="document.getElementById('add-galeri-modal').style.display='none'" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form action="{{ route('admin.publikasi.galeri.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                @csrf
                <div class="space-y-1">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Judul / Label</label>
                    <input type="text" name="title" required placeholder="Masukkan judul foto/video" class="w-full bg-slate-50 border border-slate-250 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-500">
                </div>
                <div class="space-y-1">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Tipe Media</label>
                    <select name="type" id="add-type" onchange="toggleAddFields()" class="w-full bg-slate-50 border border-slate-250 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-500">
                        <option value="photo">Foto / Gambar</option>
                        <option value="video">Link Video (YouTube)</option>
                    </select>
                </div>
                <div id="add-photo-field" class="space-y-1">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Upload Foto</label>
                    <input type="file" name="file" class="w-full text-xs text-slate-500">
                </div>
                <div id="add-video-field" style="display: none;" class="space-y-1">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">URL Video Eksternal</label>
                    <input type="url" name="external_url" placeholder="https://www.youtube.com/watch?v=..." class="w-full bg-slate-50 border border-slate-250 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-500">
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" onclick="document.getElementById('add-galeri-modal').style.display='none'" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs transition-colors">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs transition-colors">
                        Simpan Item
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="edit-galeri-modal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white dark:bg-slate-900 rounded-3xl max-w-md w-full shadow-2xl overflow-hidden border border-slate-200 dark:border-slate-800">
            <div class="px-6 py-4 border-b border-slate-150 dark:border-slate-800 flex justify-between items-center bg-slate-50 dark:bg-slate-900/50">
                <h3 class="font-bold text-lg text-slate-900 dark:text-white">Edit Galeri & Video</h3>
                <button onclick="document.getElementById('edit-galeri-modal').style.display='none'" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form id="edit-form" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                @csrf
                @method('PATCH')
                <div class="space-y-1">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Judul / Label</label>
                    <input type="text" name="title" id="edit-title" required class="w-full bg-slate-50 border border-slate-250 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-500">
                </div>
                <div class="space-y-1">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Tipe Media</label>
                    <select name="type" id="edit-type" onchange="toggleEditFields()" class="w-full bg-slate-50 border border-slate-250 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-500">
                        <option value="photo">Foto / Gambar</option>
                        <option value="video">Link Video (YouTube)</option>
                    </select>
                </div>
                <div id="edit-photo-field" class="space-y-1">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Upload Foto (Biarkan kosong jika tidak ingin mengubah)</label>
                    <input type="file" name="file" class="w-full text-xs text-slate-500">
                </div>
                <div id="edit-video-field" style="display: none;" class="space-y-1">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">URL Video Eksternal</label>
                    <input type="url" name="external_url" id="edit-external_url" class="w-full bg-slate-50 border border-slate-250 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-500">
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" onclick="document.getElementById('edit-galeri-modal').style.display='none'" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs transition-colors">
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
        function toggleAddFields() {
            let type = document.getElementById('add-type').value;
            if(type === 'photo') {
                document.getElementById('add-photo-field').style.display = 'block';
                document.getElementById('add-video-field').style.display = 'none';
            } else {
                document.getElementById('add-photo-field').style.display = 'none';
                document.getElementById('add-video-field').style.display = 'block';
            }
        }

        function toggleEditFields() {
            let type = document.getElementById('edit-type').value;
            if(type === 'photo') {
                document.getElementById('edit-photo-field').style.display = 'block';
                document.getElementById('edit-video-field').style.display = 'none';
            } else {
                document.getElementById('edit-photo-field').style.display = 'none';
                document.getElementById('edit-video-field').style.display = 'block';
            }
        }

        function openEditModal(item) {
            document.getElementById('edit-form').action = `/admin/publikasi/galeri/${item.id}`;
            document.getElementById('edit-title').value = item.title;
            document.getElementById('edit-type').value = item.type;
            document.getElementById('edit-external_url').value = item.external_url || '';
            toggleEditFields();
            document.getElementById('edit-galeri-modal').style.display = 'flex';
        }
    </script>
</x-app-layout>
