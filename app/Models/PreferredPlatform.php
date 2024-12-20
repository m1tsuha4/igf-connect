<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PreferredPlatform extends Model
{
    protected $fillable = [
        'company_id',
        'name',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
