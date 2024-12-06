<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = [
        'company_name',
        'representative_name',
        'address',
        'company_logo',
        'about_us',
        'company_type',
        'country',
        'status',
        'user_id',
        'conference_id'
    ];

    public function keyProductLine()
    {
        return $this->hasMany(KeyProductLine::class);
    }
    
    public function bizMatch()
    {
        return $this->hasMany(BizMatching::class);
    }
    
    public function preferredPlatform()
    {
        return $this->hasMany(PreferredPlatform::class);
    }

    public function schedule()
    {
        return $this->hasmany(Schedule::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function conference()
    {
        return $this->hasMany(Conference::class);
    }

    public function matchmakingAsCompanyBook()
    {
        return $this->hasMany(Matchmaking::class, 'company_id_book');
    }

    public function matchmakingAsCompanyMatch()
    {
        return $this->hasMany(Matchmaking::class, 'company_id_match');
    }

}
