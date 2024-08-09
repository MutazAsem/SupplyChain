<?php

namespace App\Models;

use App\Enums\CityEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Address extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'city',
        'address_link',
        'addressable_id',
        'addressable_type'
    ];



    protected $casts = [
        'city' => CityEnum::class,
    ];

    public function addressable(): MorphTo
    {
        return $this->morphTo();
    }
}
