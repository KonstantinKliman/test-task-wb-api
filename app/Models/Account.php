<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $fillable = [
        'company_id',
        'name'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function tokens()
    {
        return $this->hasMany(Token::class);
    }
}