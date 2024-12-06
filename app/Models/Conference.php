<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conference extends Model
{
    protected $fillable = [
        'name',
        'description',
        'venue',
        'date_start',
        'date_end',
        'speaker',
        'moderator',
        'sum_table',
    ];

    public function tables()
    {
        return $this->hasMany(Table::class);
    }
}
