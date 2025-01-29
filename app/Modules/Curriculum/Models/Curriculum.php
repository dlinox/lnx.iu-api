<?php

namespace App\Modules\Curriculum\Models;

use App\Traits\HasDataTable;
use App\Traits\HasEnabledState;
use Illuminate\Database\Eloquent\Model;

class Curriculum extends Model
{
    use HasDataTable, HasEnabledState;

    protected $table = 'curriculums';

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
}
