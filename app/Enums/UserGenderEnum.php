<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;


enum UserGenderEnum: string implements HasLabel
{
    case MALE = 'male';
    case FEMALE = 'female';


    public function getLabel(): ?string
    {
        return match ($this) {
            self::MALE => 'male',
            self::FEMALE => 'female',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
