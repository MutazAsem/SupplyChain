<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;


    protected $fillable = [
        'name',
        'rfid_tag',
        'slug',
        'description',
        'image',
        'category_id',
        'farm_id',
        'unit',
        'unit_price',
        'quantity_available',
        'packaging',
        'type',
        'status',
    ];

    public function category(): BelongsTo
    {
        return $this->BelongsTo(Category::class, 'category_id');
    }

    public function farm(): BelongsTo
    {
        return $this->BelongsTo(Farm::class, 'farm_id');
    }
}
