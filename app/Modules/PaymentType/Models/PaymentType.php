<?php

namespace App\Modules\PaymentType\Models;

use App\Traits\HasDataTable;
use App\Traits\HasEnabledState;
use Illuminate\Database\Eloquent\Model;

class PaymentType extends Model
{
    use HasDataTable, HasEnabledState;

    protected $fillable = [
        'name',
        'commission',
        'is_enabled',
    ];

    protected $casts = [
        'commission' => 'decimal:2',
        'is_enabled' => 'boolean',
    ];

    static $searchColumns = [
        'payment_types.name',
    ];

    public $timestamps = false;
}
