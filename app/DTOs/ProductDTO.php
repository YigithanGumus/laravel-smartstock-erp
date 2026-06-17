<?php

namespace App\DTOs;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

readonly class ProductDTO
{
    public function __construct(
        public string $name,
        public string $barcode,
        public float $price,
        public float $costPrice,
        public string $unit,
    ) {}

    public static function fromRequest(FormRequest|array $request): self
    {
        $data = $request instanceof FormRequest ? $request->validated() : $request;

        return new self(
            name: $data['name'],
            barcode: $data['barcode'],
            price: $data['price'],
            costPrice: $data['cost_price'],
            unit: $data['unit'],
        );
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
