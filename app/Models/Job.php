<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Job extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $fillable = [
        'user_id',
        'organization_id',
        'category_id',
        'job_title',
        'description',
        'creator',
        'address',
        'zipcode',
        'status',
        'is_published',
        'is_remote',
        'skill',
        'experience',
        'degree_id',
        'budget',
        'bid_close',
        'deadline',
        'career_page_url',
        'is_pinned_in_career_page',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    public function organization()
    {
        return $this->belongsTo(Organization::class, 'organization_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'user_id');
    }

    public function application_form()
{
    return $this->hasMany(Application_form::class, 'job_id');
}

public function categories()
{
    return $this->belongsTo(Categories::class, 'cat_id');
}

public function degree()
{
    return $this->belongsTo(Degree::class, 'degree_id');
}

}
