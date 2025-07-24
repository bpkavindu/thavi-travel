<?php

namespace App\Enums;

enum ReservationStatus: int
{
    case CANCELLED = 0;
    case PENDING = 1;
    case CONFIRMED = 2;

    public function label(): string
    {
        return match ($this) {
            self::CANCELLED => 'Cancelled',
            self::PENDING => 'Pending',
            self::CONFIRMED => 'Confirmed',
        };
    }

    public static function options(): array
    {
        return [
            self::PENDING->value => 'Pending',
            self::CONFIRMED->value => 'Confirmed',
            self::CANCELLED->value => 'Cancelled',
        ];
    }
}
