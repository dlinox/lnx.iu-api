<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gender extends Model
{
    protected $casts = [
        'is_enabled' => 'boolean',
    ];
    
    public $timestamps = false;
}
