<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Gallery;
use App\Models\SocialMedia;
use App\Models\Banner;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class PublicationController extends Controller
{
    private function checkSuperAdmin()
    {
        $user = Auth::user();
        if (!$user || (!$user->isSuperAdmin() && !$user->isAdmin())) {
            abort(403);
        }
    }

    // ==========================================
    // BERITA & PENGUMUMAN
    // ==========================================
    public function beritaIndex()
    {
        $this->checkSuperAdmin();
        $articles = Article::latest()->get();
        return view('dashboard.superadmin.publications.berita', compact('articles'));
    }

    public function storeBerita(Request $request)
    {
        $this->checkSuperAdmin();
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category' => 'required|in:Akademik,Prestasi,Agenda,Umum',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'published_at' => 'required|date',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/articles'), $filename);
            $imagePath = '/uploads/articles/' . $filename;
        }

        $article = Article::create([
            'title' => $request->title,
            'content' => $request->content,
            'category' => $request->category,
            'image_path' => $imagePath,
            'published_at' => $request->published_at,
        ]);

        // Audit Log
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'create_article',
            'entity' => 'articles',
            'entity_id' => $article->id,
            'new_value' => json_encode($article->toArray()),
        ]);

        return redirect()->route('admin.publikasi.berita.index')->with('success', 'Berita berhasil diterbitkan.');
    }

    public function updateBerita(Request $request, $id)
    {
        $this->checkSuperAdmin();
        $article = Article::findOrFail($id);
        $oldValue = $article->toArray();

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category' => 'required|in:Akademik,Prestasi,Agenda,Umum',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'published_at' => 'required|date',
        ]);

        if ($request->hasFile('image')) {
            // Delete old file if exists
            if ($article->image_path && file_exists(public_path($article->image_path))) {
                @unlink(public_path($article->image_path));
            }

            $file = $request->file('image');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/articles'), $filename);
            $article->image_path = '/uploads/articles/' . $filename;
        }

        $article->title = $request->title;
        $article->content = $request->content;
        $article->category = $request->category;
        $article->published_at = $request->published_at;
        $article->save();

        // Audit Log
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'update_article',
            'entity' => 'articles',
            'entity_id' => $article->id,
            'old_value' => json_encode($oldValue),
            'new_value' => json_encode($article->fresh()->toArray()),
        ]);

        return redirect()->route('admin.publikasi.berita.index')->with('success', 'Berita berhasil diubah.');
    }

    public function deleteBerita($id)
    {
        $this->checkSuperAdmin();
        $article = Article::findOrFail($id);
        $oldValue = $article->toArray();

        if ($article->image_path && file_exists(public_path($article->image_path))) {
            @unlink(public_path($article->image_path));
        }

        $article->delete();

        // Audit Log
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'delete_article',
            'entity' => 'articles',
            'entity_id' => $id,
            'old_value' => json_encode($oldValue),
        ]);

        return redirect()->route('admin.publikasi.berita.index')->with('success', 'Berita berhasil dihapus.');
    }


    // ==========================================
    // GALERI & VIDEO
    // ==========================================
    public function galeriIndex()
    {
        $this->checkSuperAdmin();
        $galleries = Gallery::latest()->get();
        return view('dashboard.superadmin.publications.galeri', compact('galleries'));
    }

    public function storeGaleri(Request $request)
    {
        $this->checkSuperAdmin();
        $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:photo,video',
            'file' => 'required_if:type,photo|image|mimes:jpeg,png,jpg,gif,svg|max:4096',
            'external_url' => 'required_if:type,video|nullable|url',
        ]);

        $filePath = null;
        if ($request->type === 'photo' && $request->hasFile('file')) {
            $file = $request->file('file');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/galleries'), $filename);
            $filePath = '/uploads/galleries/' . $filename;
        }

        $gallery = Gallery::create([
            'title' => $request->title,
            'type' => $request->type,
            'file_path' => $filePath,
            'external_url' => $request->type === 'video' ? $request->external_url : null,
        ]);

        // Audit Log
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'create_gallery',
            'entity' => 'galleries',
            'entity_id' => $gallery->id,
            'new_value' => json_encode($gallery->toArray()),
        ]);

        return redirect()->route('admin.publikasi.galeri.index')->with('success', 'Item galeri berhasil ditambahkan.');
    }

    public function updateGaleri(Request $request, $id)
    {
        $this->checkSuperAdmin();
        $gallery = Gallery::findOrFail($id);
        $oldValue = $gallery->toArray();

        $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:photo,video',
            'file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:4096',
            'external_url' => 'required_if:type,video|nullable|url',
        ]);

        if ($request->type === 'photo') {
            if ($request->hasFile('file')) {
                if ($gallery->file_path && file_exists(public_path($gallery->file_path))) {
                    @unlink(public_path($gallery->file_path));
                }

                $file = $request->file('file');
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/galleries'), $filename);
                $gallery->file_path = '/uploads/galleries/' . $filename;
            }
            $gallery->external_url = null;
        } else {
            if ($gallery->file_path && file_exists(public_path($gallery->file_path))) {
                @unlink(public_path($gallery->file_path));
                $gallery->file_path = null;
            }
            $gallery->external_url = $request->external_url;
        }

        $gallery->title = $request->title;
        $gallery->type = $request->type;
        $gallery->save();

        // Audit Log
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'update_gallery',
            'entity' => 'galleries',
            'entity_id' => $gallery->id,
            'old_value' => json_encode($oldValue),
            'new_value' => json_encode($gallery->fresh()->toArray()),
        ]);

        return redirect()->route('admin.publikasi.galeri.index')->with('success', 'Item galeri berhasil diubah.');
    }

    public function deleteGaleri($id)
    {
        $this->checkSuperAdmin();
        $gallery = Gallery::findOrFail($id);
        $oldValue = $gallery->toArray();

        if ($gallery->file_path && file_exists(public_path($gallery->file_path))) {
            @unlink(public_path($gallery->file_path));
        }

        $gallery->delete();

        // Audit Log
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'delete_gallery',
            'entity' => 'galleries',
            'entity_id' => $id,
            'old_value' => json_encode($oldValue),
        ]);

        return redirect()->route('admin.publikasi.galeri.index')->with('success', 'Item galeri berhasil dihapus.');
    }


    // ==========================================
    // MEDIA SOSIAL
    // ==========================================
    public function medsosIndex()
    {
        $this->checkSuperAdmin();
        $socials = SocialMedia::all();
        return view('dashboard.superadmin.publications.medsos', compact('socials'));
    }

    public function updateMedsos(Request $request)
    {
        $this->checkSuperAdmin();
        $request->validate([
            'urls' => 'required|array',
            'urls.*' => 'nullable|url',
        ]);

        $oldValue = SocialMedia::all()->toArray();

        foreach ($request->urls as $id => $url) {
            $social = SocialMedia::findOrFail($id);
            $social->url = $url;
            $social->is_active = $request->has("active.{$id}");
            $social->save();
        }

        // Audit Log
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'update_social_media',
            'entity' => 'social_media',
            'old_value' => json_encode($oldValue),
            'new_value' => json_encode(SocialMedia::all()->toArray()),
        ]);

        return redirect()->route('admin.publikasi.medsos.index')->with('success', 'Media sosial berhasil disimpan.');
    }


    // ==========================================
    // BANNER WEBSITE
    // ==========================================
    public function bannerIndex()
    {
        $this->checkSuperAdmin();
        $banners = Banner::orderBy('order')->get();
        return view('dashboard.superadmin.publications.banner', compact('banners'));
    }

    public function storeBanner(Request $request)
    {
        $this->checkSuperAdmin();
        $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:500',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:4096',
            'link_url' => 'nullable|url',
            'order' => 'nullable|integer',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/banners'), $filename);
            $imagePath = '/uploads/banners/' . $filename;
        }

        $banner = Banner::create([
            'title' => $request->title,
            'subtitle' => $request->subtitle,
            'image_path' => $imagePath,
            'link_url' => $request->link_url,
            'order' => $request->order ?? 0,
            'is_active' => true,
        ]);

        // Audit Log
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'create_banner',
            'entity' => 'banners',
            'entity_id' => $banner->id,
            'new_value' => json_encode($banner->toArray()),
        ]);

        return redirect()->route('admin.publikasi.banner.index')->with('success', 'Banner berhasil ditambahkan.');
    }

    public function updateBanner(Request $request, $id)
    {
        $this->checkSuperAdmin();
        $banner = Banner::findOrFail($id);
        $oldValue = $banner->toArray();

        $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:500',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:4096',
            'link_url' => 'nullable|url',
            'order' => 'nullable|integer',
        ]);

        if ($request->hasFile('image')) {
            if ($banner->image_path && file_exists(public_path($banner->image_path))) {
                @unlink(public_path($banner->image_path));
            }

            $file = $request->file('image');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/banners'), $filename);
            $banner->image_path = '/uploads/banners/' . $filename;
        }

        $banner->title = $request->title;
        $banner->subtitle = $request->subtitle;
        $banner->link_url = $request->link_url;
        $banner->order = $request->order ?? 0;
        $banner->is_active = $request->has('is_active');
        $banner->save();

        // Audit Log
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'update_banner',
            'entity' => 'banners',
            'entity_id' => $banner->id,
            'old_value' => json_encode($oldValue),
            'new_value' => json_encode($banner->fresh()->toArray()),
        ]);

        return redirect()->route('admin.publikasi.banner.index')->with('success', 'Banner berhasil diubah.');
    }

    public function deleteBanner($id)
    {
        $this->checkSuperAdmin();
        $banner = Banner::findOrFail($id);
        $oldValue = $banner->toArray();

        if ($banner->image_path && file_exists(public_path($banner->image_path))) {
            @unlink(public_path($banner->image_path));
        }

        $banner->delete();

        // Audit Log
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'delete_banner',
            'entity' => 'banners',
            'entity_id' => $id,
            'old_value' => json_encode($oldValue),
        ]);

        return redirect()->route('admin.publikasi.banner.index')->with('success', 'Banner berhasil dihapus.');
    }
}
