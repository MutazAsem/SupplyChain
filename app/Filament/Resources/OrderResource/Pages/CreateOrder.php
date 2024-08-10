<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Models\Product;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateOrder extends CreateRecord
{
    protected static string $resource = OrderResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
{
    // الحصول على المنتج بناءً على معرف المنتج
    $product = Product::find($data['product_id']);
    
    // خصم الكمية المطلوبة من الكمية المتوفرة في المنتج
    $product->quantity_available -= $data['quantity'];
    
    // حفظ التعديلات على المنتج
    $product->save();

    // إرجاع البيانات دون تعديلها
    return $data;
}

}
