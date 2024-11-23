<?php

namespace App\Models;

use App\Models\User;
use App\Models\contrevention;
use Illuminate\Database\Eloquent\Model;

class contreventionUser extends Model
{
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function contrevention()
    {
        return $this->belongsTo(related: contrevention::class);
    }
}
