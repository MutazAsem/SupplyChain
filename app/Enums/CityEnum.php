<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum CityEnum: string implements HasLabel
{
    case ABYAN = 'Abyan';
    case ADEN = 'Aden';
    case AMRAN = 'Amran';
    case AL_BAYDA = 'Al-Bayda';
    case DHAMAR = 'Dhamar';
    case HADRAMAUT = 'Hadramaut';
    case HAJJAH = 'Hajjah';
    case AL_HUDAYDAH = 'Al-Hudaydah';
    case IBB = 'Ibb';
    case AL_JAWF = 'Al-Jawf';
    case LAHIJ = 'Lahij';
    case MARIB = 'Marib';
    case AL_MAHRAH = 'Al-Mahrah';
    case AL_MAHWIT = 'Al-Mahwit';
    case RAYMAH = 'Raymah';
    case SADAH = 'Sadah';
    case SHABWAH = 'Shabwah';
    case SANAA = 'Sanaa';
    case TAIZ = 'Taiz';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::ABYAN => 'Abyan',
            self::ADEN => 'Aden',
            self::AMRAN => 'Amran',
            self::AL_BAYDA => 'Al-Bayda',
            self::DHAMAR => 'Dhamar',
            self::HADRAMAUT => 'Hadramaut',
            self::HAJJAH => 'Hajjah',
            self::AL_HUDAYDAH => 'Al-Hudaydah',
            self::IBB => 'Ibb',
            self::AL_JAWF => 'Al-Jawf',
            self::LAHIJ => 'Lahij',
            self::MARIB => 'Marib',
            self::AL_MAHRAH => 'Al-Mahrah',
            self::AL_MAHWIT => 'Al-Mahwit',
            self::RAYMAH => 'Raymah',
            self::SADAH => 'Sadah',
            self::SHABWAH => 'Shabwah',
            self::SANAA => 'Sanaa',
            self::TAIZ => 'Taiz',
        };
    }
}
