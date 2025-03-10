<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $table = 'companies';

    protected $fillable = ['name', 'image', 'description', 'type']; 

    public function courses()
    {
        return $this->hasMany(Course::class, 'company_id');
    }
}
