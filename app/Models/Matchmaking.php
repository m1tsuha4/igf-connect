<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Matchmaking extends Model
{
    protected $fillable = [
        'company_id_book',
        'company_id_match',
        'table_id',
        'time_start',
        'time_end',
        'approved_company',
        'approved_admin',
    ];

    public function company_book()
    {
        return $this->belongsTo(Company::class, 'company_id_book', 'id');
    }
    
    public function company_match()
    {
        return $this->belongsTo(Company::class, 'company_id_match', 'id');
    }

    public function table()
    {
        return $this->belongsTo(Table::class);
    }
}
