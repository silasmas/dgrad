<?php

namespace App\Models;

use App\Models\contreventionUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class transaction extends Model
{
    /** @use HasFactory<\Database\Factories\TransactionFactory> */
    use HasFactory;
    protected $guarded = [];
    public function user()
    {
        return $this->hasMany(contreventionUser::class);
    }
}
