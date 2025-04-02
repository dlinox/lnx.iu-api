<?php

namespace App\Modules\Role\Models;

use App\Traits\HasDataTable;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use  HasDataTable;

    protected $fillable = [
        'name',
        'model_type',
        'guard_name',
        'is_enabled',
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

    public static function getByName($name)
    {
        $item = self::where('name', $name)->first();
        
        return $item ? $item : null;
    }
}
