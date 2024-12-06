<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    protected $fillable = [
        'id',
        'conference_id',
        'name_table',
        'date',
    ];

    public function conference()
    {
        return $this->belongsTo(Conference::class);
    }
}
