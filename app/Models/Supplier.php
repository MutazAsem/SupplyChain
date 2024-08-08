<?php

namespace App\Models;

use App\Enums\SupplierTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'description', 'image', 'owner_id', 'type',
        'commercial_registration_number', 'status',
    ];

    protected $casts = [
        'city' => SupplierTypeEnum::class,
    ];

    public function supplier_owner ():BelongsTo
    {
        return $this->BelongsTo(User::class,'owner_id');
    }

    public function address(): MorphOne
    {
        return $this->morphOne(Address::class, 'addressable');
    }
}
