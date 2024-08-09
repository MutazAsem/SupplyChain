<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;


enum UserGenderEnum: string
{
    case MALE = 'male';
    case FEMALE = 'female';


    public function getLabel(): ?string
    {
        return match ($this) {
            self::MALE => 'Male',
            self::FEMALE => 'Female',
        };
    }
}
