<?php

namespace App\Modules\Person\Models;

use App\Traits\HasDataTable;
use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    use HasDataTable;

    protected $fillable = [
        'code',
        'document_type_id',
        'document_number',
        'name',
        'last_name_father',
        'last_name_mother',
        'gender',
        'date_of_birth',
        'address',
        'phone',
        'email',
        'location_id',
        'country_id',
    ];

    protected $casts = [];

    static $searchColumns = [
        'name',
    ];

    public static function registerItem($data)
    {
        $item =  self::create([
            'code' => $data['code'],
            'document_type_id' => $data['document_type_id'],
            'document_number' => $data['document_number'],
            'name' => $data['name'],
            'last_name_father' => $data['last_name_father'],
            'last_name_mother' => $data['last_name_mother'],
            'gender' => $data['gender'],
            'date_of_birth' => $data['date_of_birth'],
            'address' => $data['address'],
            'phone' => $data['phone'],
            'email' => $data['email'],
        ]);

        return $item;
    }

    public static function updateItem($data)
    {
        $item =  self::find($data['person_id']);
        $item->update([
            'code' => $data['code'],
            'document_type_id' => $data['document_type_id'],
            'document_number' => $data['document_number'],
            'name' => $data['name'],
            'last_name_father' => $data['last_name_father'],
            'last_name_mother' => $data['last_name_mother'],
            'gender' => $data['gender'],
            'date_of_birth' => $data['date_of_birth'],
            'address' => $data['address'],
            'phone' => $data['phone'],
            'email' => $data['email'],
        ]);

        return $item;
    }
}
