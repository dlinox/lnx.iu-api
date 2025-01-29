<?php

namespace App\Modules\Module\Models;

use App\Traits\HasDataTable;
use App\Traits\HasEnabledState;
use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    use HasDataTable, HasEnabledState;

    protected $fillable = [
        'name',
        'description',
        'is_enabled',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
    ];

    static $searchColumns = [
        'name',
    ];
}
