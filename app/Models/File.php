<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory;

    protected $table = 'files'; // Nama tabel di database

    protected $fillable = [
        'filename',
        'path',
        'mime_type',
        'article_item_id',
    ];

    // Relasi ke tabel article_items
    public function articleItem()
    {
        return $this->belongsTo(ArticleItem::class, 'article_item_id');
    }
}
