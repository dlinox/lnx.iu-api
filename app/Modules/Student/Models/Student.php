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
        'student_type_id',
        'code',
        'document_type_id',
        'document_number',
        'name',
        'last_name_father',
        'last_name_mother',
        'gender_id',
        'phone',
        'date_of_birth',
        'address',
        'email',
        'location_id',
        'country_id',
        'is_enabled',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
    ];

    public function enrollments()
    {
        return $this->hasMany('App\Modules\Enrollment\Models\Enrollment');
    }

    public function enrollmentGroups()
    {
        return $this->hasMany('App\Modules\EnrollmentGroup\Models\EnrollmentGroup');
    }

    static $searchColumns = [
        'students.code',
        'students.document_number',
        'students.name',
        'students.last_name_father',
        'students.last_name_mother',
        'students.email',
        'students.phone',
    ];

    private static function generateCode()
    {
        $year = date('Y');
        $correlative = self::where('code', 'like', $year . '%')->max('code');
        if ($correlative) {
            $correlative = (int) substr($correlative, 4);
            $correlative++;
        } else {
            $correlative = 1;
        }
        $correlative = str_pad($correlative, 4, '0', STR_PAD_LEFT);
        $correlative = $year . $correlative;
        return $correlative;
    }

    public static function registerItem($data)
    {
        $code = self::generateCode();

        $item =  self::create([
            'student_type_id' => $data['student_type_id'],
            'code' => $code,
            'document_type_id' => $data['document_type_id'],
            'document_number' => $data['document_number'],
            'name' => $data['name'],
            'last_name_father' => $data['last_name_father'],
            'last_name_mother' => $data['last_name_mother'],
            'gender_id' => $data['gender'],
            'date_of_birth' => $data['date_of_birth'],
            'address' => $data['address'] ?? '',
            'phone' => $data['phone'],
            'email' => $data['email'],
        ]);

        return $item;
    }

    public static function updateItem($data)
    {
        $item =  self::find($data['id']);
        $item->update([
            'student_type_id' => $data['student_type_id'],
            'document_type_id' => $data['document_type_id'],
            'document_number' => $data['document_number'],
            'name' => $data['name'],
            'last_name_father' => $data['last_name_father'],
            'last_name_mother' => $data['last_name_mother'],
            'gender_id' => $data['gender'],
            'date_of_birth' => $data['date_of_birth'],
            'address' => $data['address'],
            'phone' => $data['phone'],
            'email' => $data['email'],
            'is_enabled' => $data['is_enabled'],
        ]);

        return $item;
    }
    public static function getInfoById($id)
    {
        $item = self::select(
            'students.id',
            'student_types.name as studentType',
            'students.is_enabled as isEnabled',
            DB::raw('CONCAT_WS(" ", students.name, students.last_name_father, students.last_name_mother) as fullName'),
            'students.code',
            'students.document_number as documentNumber',
            'students.email',
            'students.phone',
        )
            ->join('student_types', 'student_types.id', '=', 'students.student_type_id')
            ->where('students.id', $id)
            ->first();

        return $item;
    }

    public static function search($search)
    {
        $items = self::select(
            'students.id',
            'students.document_number',
            DB::raw('CONCAT_WS(" ", students.name, students.last_name_father, students.last_name_mother) as fullName'),
        )
            ->where(function ($query) use ($search) {
                $query->where('students.code', 'like', '%' . $search . '%')
                    ->orWhere('students.document_number', 'like', '%' . $search . '%')
                    ->orWhereRaw(
                        "CONCAT_WS(' ', students.name, students.last_name_father, students.last_name_mother) like ?",
                        ["%{$search}%"]
                    );
            })
            ->limit(20)
            ->get()->map(function ($item) {
                return [
                    'value' => $item->id,
                    'label' => $item->document_number . ' - ' . $item->fullName,
                ];
            });

        return $items;
    }
}
