<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'creator_id',
        'picture',
        'resume',
        'phone_number',
        'gender',
        'birth_date',
        'address',
        'zipcode',
        'latest_degree',
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

    public function job()
    {
        return $this->hasMany(Job::class, 'user_id');
    }
    
}
