<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Candidate extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'picture',
        'cv',
        'profession',
        'phone_number',
        'gender',
        'birth_date',
        'address',
        'zipcode',
        'degree_id',
        'experience',
        'skill',
        'latest_university',
        'current_organization',
        'current_department',
        'current_position',
        'description',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
