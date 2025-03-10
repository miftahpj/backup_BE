<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $table = 'course';

    protected $fillable = [
        'company_id',
        'curriculum_type_id',
        'author_id',
        'status_id',
        'title',
        'description',
        'price',
        'image',
        'duration'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function curriculumType()
    {
        return $this->belongsTo(CurriculumType::class, 'curriculum_type_id');
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id');
    }
}
