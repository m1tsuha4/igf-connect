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
    
}
