<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'image',
        'invitation_token',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function organization()
{
    return $this->hasMany(Organization::class);
}

public function employees()
{
    return $this->hasMany(Employee::class, 'creator_id', 'user_id');
}

public function candidate()
{
    return $this->hasMany(Candidate::class, 'user_id');
}

public function application_form()
{
    return $this->hasMany(Application_form::class, 'user_id');
}



public function deleteWithRolesAndPermissions()
{
    $this->roles()->detach(); // Detach roles
    $this->permissions()->detach(); // Detach permissions
    $this->delete(); // Delete the user
}


}
