<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    use HasFactory;

    protected $table = 'status';
    protected $fillable = ['title'];

    public function courses()
    {
        return $this->hasMany(Course::class, 'status_id');
    }
}




