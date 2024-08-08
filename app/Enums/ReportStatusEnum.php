<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum ReportStatusEnum: string implements HasLabel
{
    case PENDING = 'Pending';
    case PASS = 'Pass';
    case FAIL = 'Fail';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::PASS => 'Pass',
            self::FAIL => 'Fail',
            
        };
    }
}
