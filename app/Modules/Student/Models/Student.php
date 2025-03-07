<?php

namespace App\Modules\Student\Models;

use App\Traits\HasDataTable;
use App\Traits\HasEnabledState;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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

    //un estudiante pertenece a una persona
    public function person()
    {
        return $this->belongsTo('App\Modules\Person\Models\Person');
    }
    //un studinate puede estar matriculado en varios cursos
    public function enrollments()
    {
        return $this->hasMany('App\Modules\Enrollment\Models\Enrollment');
    }

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
    public static function getInfoById($id)
    {
        $item = self::select(
            'students.id',
            'student_types.name as studentType',
            'students.is_enabled as isEnabled',
            DB::raw('CONCAT_WS(" ", people.name, people.last_name_father, people.last_name_mother) as fullName'),
            'people.code',
            'people.document_number as documentNumber',
            'people.email',
            'people.phone',
        )
            ->join('people', 'people.id', '=', 'students.person_id')
            ->join('student_types', 'student_types.id', '=', 'students.student_type_id')
            ->where('students.id', $id)
            ->first();

        return $item;
    }
}
