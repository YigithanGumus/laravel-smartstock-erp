<?php

namespace App\Providers;

use App\Repositories\ProductRepository\Interfaces\IProductRepository;
use App\Repositories\ProductRepository\ProductRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // bindings
        $this->app->bind(IProductRepository::class, ProductRepository::class);
    }
}
