<?php

namespace App\Constants;

class PeriodStatusConstants

{
    protected const items = [
        [
            'value' => 'MATRICULA',
            'label' => 'MATRICULA',
        ],
        [
            'value' => 'EN CURSO',
            'label' => 'EN CURSO',
        ],
        [
            'value' => 'FINALIZADO',
            'label' => 'FINALIZADO',
        ],
        [
            'value' => 'CANCELADO',
            'label' => 'CANCELADO',
        ],
        [
            'value' => 'PENDIENTE',
            'label' => 'PENDIENTE',
        ],
    ];

    public static function all(): array
    {
        return static::items;
    }

    public static function label(int $value): string
    {
        return collect(static::items)->firstWhere('value', $value)['label'];
    }

    public static function value(string $label): string
    {
        return collect(static::items)->firstWhere('label', $label)['value'];
    }
}
