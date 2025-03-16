<?php

namespace App\Traits;

use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

trait HasLogs
{
    use LogsActivity;

    protected static function bootHasLogs()
    {
        static::creating(function ($model) {
            $model->setLogAttributes();
        });

        static::updating(function ($model) {
            $model->setLogAttributes();
        });

        static::deleting(function ($model) {
            $model->setLogAttributes();
        });
    }

    public function setLogAttributes()
    {
        $this->logAttributes = $this->logAttributes ?? [];
        $this->logName = $this->logName ?? class_basename($this);
    }

    public function getDescriptionForEvent(string $eventName): string
    {
        $eventNameSpanish = [
            'created' => 'creado',
            'updated' => 'actualizado',
            'deleted' => 'eliminado',
        ];

        $event = $eventNameSpanish[$eventName] ?? $eventName;

        $ip = request()->ip();
        return "{$ip}: {$this->logName} ha sido {$event}";
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly($this->logAttributes)
            ->logOnlyDirty()
            ->useLogName($this->logName);
    }
}
