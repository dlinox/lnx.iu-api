<?php

namespace App\Modules\Schedule\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    
    protected $fillable = [
        'day',
        'start_hour',
        'end_hour',
        'group_id',
    ];
    public $timestamps = false;

    public static function byGroup($groupId)
    {
        $shedule =  self::select(
            'start_hour as startHour',
            'end_hour as endHour',
        )
            ->selectRaw('GROUP_CONCAT(`day`) AS days')
            ->where('group_id', $groupId)
            ->groupBy('start_hour', 'end_hour')
            ->first();

        if (!$shedule) {
            return null;
        }

        $shedule->startHour = date('h:i A', strtotime($shedule->startHour));
        $shedule->endHour = date('h:i A', strtotime($shedule->endHour));

        return $shedule;
    }
}