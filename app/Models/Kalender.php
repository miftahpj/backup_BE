<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kalender extends Model
{
    use HasFactory;

    protected $fillable = ['id_course', 'start', 'end'];

    public function course()
    {
        return $this->belongsTo(Course::class, 'id_course');
    }
}
