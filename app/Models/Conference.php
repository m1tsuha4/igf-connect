<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conference extends Model
{
    protected $fillable = [
        'name',
        'description',
        'image',
        'venue',
        'date_start',
        'date_end',
        'time_start',
        'time_end',
        'speaker',
        'moderator',
        'sum_table',
    ];

    public function tables()
    {
        return $this->hasMany(Table::class);
    }

    public function company()
    {
        return $this->hasMany(Company::class);
    }
}
