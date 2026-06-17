<?php

namespace App\Http\Requests\V1\ProductRequests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'barcode' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'cost_price' => ['required', 'numeric', 'min:0'],
            'unit' => ['required', 'string', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Ürün adı zorunludur.',
            'barcode.required' => 'Barkod zorunludur.',
            'price.required' => 'Fiyat zorunludur.',
            'price.min' => 'Fiyat negatif olamaz.',
            'cost_price.required' => 'Maliyet fiyatı zorunludur.',
            'unit.required' => 'Birim zorunludur.',
        ];
    }
}
