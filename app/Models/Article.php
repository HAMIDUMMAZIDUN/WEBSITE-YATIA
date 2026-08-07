<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $fillable = [
        'title',
        'content',
        'category',
        'image_path',
        'embed_link',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];
}
