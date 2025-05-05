<?php

namespace App\Modules\Teacher\Models;

use App\Traits\HasDataTable;
use App\Traits\HasEnabledState;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    use HasDataTable, HasEnabledState;

    protected $fillable = [
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
        'is_enabled',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
    ];

    static $searchColumns = [
        'teachers.code',
        'teachers.document_number',
        'teachers.name',
        'teachers.last_name_father',
        'teachers.last_name_mother',
    ];




    public static function generateCode()
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
            'is_enabled' => $data['is_enabled'],
        ]);

        return $item;
    }

    public static function updateItem($data)
    {
        $item =  self::find($data['id']);
        $item->update([
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
}
