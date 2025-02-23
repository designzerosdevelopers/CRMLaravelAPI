<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cv extends Model
{
    use HasFactory;
    protected $table = 'cv';
    protected $fillable = [
        'user_id',
        'name',
        'email',
        'phone',
        'address',
        'interest',
        'language',
        'education',
        'nationality',
        'achievements',
        'work_experience'
    ];
}
