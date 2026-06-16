<?php

namespace App\DTOs;

use Illuminate\Http\Request;

readonly class OrderDTO
{
    /**
     * DTO Özellikleri (Property'leri)
     */
    public function __construct(// Örn: public string $name,
        public int $tenantId,
        public int $userId,
        public array $items,
        public ?string $notes,
    )
    {
    }

    /**
     * Ham istekten (Form Request veya array) DTO üretmek için yardımcı metot.
     */
    public static function fromRequest(Request|array $request): self
    {
        $data = $request instanceof Request ? $request->all() : $request;

        return new self(// Örn: name: $data['name'] ?? '',
        tenantId: $data['tenantId'],
            userId: $data['userId'],
            items: $data['items'],
            notes: $data['notes'] ?? null,
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
