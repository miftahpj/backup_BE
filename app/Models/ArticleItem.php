<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArticleItem extends Model
{
    use HasFactory;

    protected $table = 'article_items';

    protected $fillable = [
        'article_id',
        'title',
        'author_id',
        'image',
        'description',
        'content',
        'template',
    ];

    public function article()
    {
        return $this->belongsTo(Article::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }
    public function file()
    {
        return $this->hasOne(File::class);
    }
    public function files()
    {
        return $this->hasMany(File::class, 'article_item_id');
    }
}
