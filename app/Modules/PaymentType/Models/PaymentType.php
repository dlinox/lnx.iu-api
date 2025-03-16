<?php

namespace App\Modules\PaymentType\Models;

use App\Traits\HasDataTable;
use App\Traits\HasEnabledState;
use App\Traits\HasLogs;
use Illuminate\Database\Eloquent\Model;

class PaymentType extends Model
{
    use HasDataTable, HasEnabledState,  HasLogs;

    protected $fillable = [
        'name',
        'is_enabled',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
    ];

    static $searchColumns = [
        'payment_types.name',
    ];

    public $timestamps = false;

    protected $logAttributes = ['name', 'is_enabled'];
    protected $logName = 'Tipo de pago';
}
