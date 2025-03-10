<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReachUs extends Model
{
    use HasFactory;

    protected $table = 'reach_us';

    protected $fillable = [
        'whatsapp', 'username', 'message'
    ];
}
