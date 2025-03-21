<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $table = 'cart';

    protected $fillable = [
        'course_id',
        'name',
        'purpose',
        'whatsapp',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }
}
