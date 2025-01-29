<?php

namespace App\Modules\StudentType\Models;

use App\Traits\HasDataTable;
use App\Traits\HasEnabledState;
use Illuminate\Database\Eloquent\Model;

class StudentType extends Model
{
    use HasDataTable, HasEnabledState;

    protected $fillable = [
        'name',
        'is_enabled',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
    ];

    static $searchColumns = [
        'name',
    ];

    public $timestamps = false;
}
