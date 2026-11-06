<?php

namespace App\Enums;

enum TaskPriority: int
{
    case Low = 1;
    case Medium = 2;
    case High = 3;

    public function label(): string
    {
        return match ($this) {
            self::Low => 'Basse',
            self::Medium => 'Moyenne',
            self::High => 'Haute',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Low => 'bg-green-100 text-green-800',
            self::Medium => 'bg-yellow-100 text-yellow-800',
            self::High => 'bg-red-100 text-red-800',
        };
    }
}
