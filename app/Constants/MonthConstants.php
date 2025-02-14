<?php

namespace App\Constants;

class MonthConstants
{
    protected const items = [
        [
            'value' => 1,
            'label' => 'ENERO',
        ],
        [
            'value' => 2,
            'label' => 'FEBRERO',
        ],
        [
            'value' => 3,
            'label' => 'MARZO',
        ],
        [
            'value' => 4,
            'label' => 'ABRIL',
        ],
        [
            'value' => 5,
            'label' => 'MAYO',
        ],
        [
            'value' => 6,
            'label' => 'JUNIO',
        ],
        [
            'value' => 7,
            'label' => 'JULIO',
        ],
        [
            'value' => 8,
            'label' => 'AGOSTO',
        ],
        [
            'value' => 9,
            'label' => 'SEPTIEMBRE',
        ],
        [
            'value' => 10,
            'label' => 'OCTUBRE',
        ],
        [
            'value' => 11,
            'label' => 'NOVIEMBRE',
        ],
        [
            'value' => 12,
            'label' => 'DICIEMBRE',
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

    public static function value(string $label): int
    {
        return collect(static::items)->firstWhere('label', $label)['value'];
    }
}
