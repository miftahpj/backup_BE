<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CurriculumType extends Model
{
    use HasFactory;

    protected $table = 'curriculum_types';

    protected $fillable = ['name', 'company_id'];

    public function courses()
    {
        return $this->hasMany(Course::class, 'curriculum_type_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
}
