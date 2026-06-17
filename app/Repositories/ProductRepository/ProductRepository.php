<?php

namespace App\Repositories\ProductRepository;

use App\Models\v1\Product;
use App\Repositories\BaseRepository\BaseRepository;
use App\Repositories\ProductRepository\Interfaces\IProductRepository;

class ProductRepository extends BaseRepository implements IProductRepository
{
    public function __construct(Product $model)
    {
        parent::__construct($model);
    }

    // Product'a özel sorgular buraya
}
