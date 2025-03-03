<?php

namespace App\Modules\Role\Models;

use App\Traits\HasDataTable;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use  HasDataTable;

    protected $fillable = [
        'name',
        'account_level',
        'is_enabled',
        'guard_name',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected function casts(): array
    {
        return [
            'is_enabled' => 'boolean',
        ];
    }
}
