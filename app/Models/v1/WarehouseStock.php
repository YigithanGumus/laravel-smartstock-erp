<?php

namespace App\Models\v1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class WarehouseStock extends Model
{
    use HasFactory;

    public function warehouse()
    {
        $this->belongsTo(Warehouse::class);
    }

    public function product()
    {
        $this->belongsTo(Product::class);
    }
}
