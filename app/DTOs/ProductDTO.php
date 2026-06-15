<?php

namespace App\DTOs;

use Illuminate\Http\Request;

readonly class ProductDTO
{
    /**
     * DTO Özellikleri (Property'leri)
     */
    public function __construct(
        public int $tenantId,
        public string $name,
        public string $barcode,
        public float $price,
        public float $costPrice,
        public string $unit,
    )
    {
    }

    /**
     * Ham istekten (Form Request veya array) DTO üretmek için yardımcı metot.
     */
    public static function fromRequest(Request|array $request): self
    {
        $data = $request instanceof Request ? $request->all() : $request;

        return new self(
            tenantId: $data['tenant_id'],
            name: $data['name'],
            barcode: $data['barcode'],
            price: $data['price'],
            costPrice: $data['costPrice'],
            unit: $data['unit'],
        );
    }

    /**
     * DTO verilerini diziye (array) çeviren yardımcı metot.
     */
    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
