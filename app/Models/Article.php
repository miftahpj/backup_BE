<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    protected $table = 'articles'; 

    protected $fillable = ['author_id', 'article'];

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function items()
    {
        return $this->hasMany(ArticleItem::class, 'article_id'); 
    }
}