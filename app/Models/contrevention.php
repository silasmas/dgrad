<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class contrevention extends Model
{
    /** @use HasFactory<\Database\Factories\ContreventionFactory> */
    use HasFactory;
    protected $guarded = [];

    public function user()
    {
        return $this->belongsToMany(User::class);
    }
}
