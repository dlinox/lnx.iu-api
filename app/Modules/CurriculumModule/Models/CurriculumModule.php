<?php

namespace App\Modules\CurriculumModule\Models;

use App\Traits\HasDataTable;
use App\Traits\HasEnabledState;
use Illuminate\Database\Eloquent\Model;

class CurriculumModule extends Model
{
    use HasDataTable, HasEnabledState;

    protected $fillable = [
        'order',
        'area_id',
        'module_id',
        'curriculum_id',
        'is_extracurricular',
        'is_enabled',
    ];

    protected $casts = [
        'order' => 'integer',
        'area_id' => 'integer',
        'module_id' => 'integer',
        'curriculum_id' => 'integer',
        'is_extracurricular' => 'boolean',
        'is_enabled' => 'boolean',
    ];

    static $searchColumns = [
        'areas.name',
        'modules.name',
    ];
}
