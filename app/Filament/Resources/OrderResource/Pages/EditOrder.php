<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Models\Product;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Validation\ValidationException;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()->before(function ($record) {
                // استعادة المنتج بناءً على معرف المنتج في الطلب
                $product = Product::find($record->product_id);

                if ($product) {
                    // إضافة الكمية الموجودة في الطلب إلى الكمية المتاحة في المنتج
                    $product->quantity_available += $record->quantity;

                    // حفظ التعديلات على المنتج
                    $product->save();
                }
            }),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
{
    // الحصول على نسخة من الطلب الأصلي
    $originalOrder = $this->record->replicate();
    $originalOrder->quantity = $this->record->quantity;

    // الحصول على المنتج بناءً على معرف المنتج
    $product = Product::find($data['product_id']);

    // استعادة الكمية الأصلية إلى المنتج قبل تعديل الكمية
    $product->quantity_available += $originalOrder->quantity;

    // التحقق من أن الكمية الجديدة المطلوبة لا تتجاوز الكمية المتاحة
    if ($data['quantity'] > $product->quantity_available) {
        throw ValidationException::withMessages([
            'quantity' => 'The quantity requested exceeds the available quantity.',
        ]);
    }

    // خصم الكمية الجديدة من الكمية المتاحة في المنتج
    $product->quantity_available -= $data['quantity'];
    
    // حفظ التعديلات على المنتج
    $product->save();

    // إرجاع البيانات دون تعديلها
    return $data;
}

}
