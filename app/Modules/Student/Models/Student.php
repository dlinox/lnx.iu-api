<?php

namespace App\Modules\Student\Models;

use App\Traits\HasDataTable;
use App\Traits\HasEnabledState;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasDataTable, HasEnabledState;

    protected $fillable = [
        'person_id',
        'student_type_id',
        'is_enabled',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
    ];

    static $searchColumns = [
        'people.code',
        'people.document_number',
        'people.name',
        'people.last_name_father',
        'people.last_name_mother',
    ];

    public static function registerItem($data)
    {
        $item =  self::create([
            'person_id' => $data['person_id'],
            'student_type_id' => $data['student_type_id'],
        ]);

        return $item;
    }

    public static function updateItem($data)
    {
        $item =  self::find($data['id']);
        $item->update([
            'is_enabled' => $data['is_enabled'],
            'student_type_id' => $data['student_type_id'],
        ]);

        return $item;
    }
}
